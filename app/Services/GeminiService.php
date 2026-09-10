<?php

namespace App\Services;

use Exception;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * اقتراح المكونات للوجبة باستخدام Gemini AI
     *
     * @param  string  $mealName  اسم الوجبة
     * @param  string|null  $description  وصف الوجبة
     * @param  float|null  $quantity  الكمية الإجمالية بالجرام
     * @param  \Illuminate\Support\Collection  $availableIngredients  المكونات المتاحة
     * @return array المكونات المقترحة مع الكميات
     *
     * @throws Exception
     */
    public function suggestIngredients($mealName, $availableIngredients, $description = null, $quantity = null)
    {
        try {
            // تحضير قائمة المكونات بصيغة مناسبة للـ AI
            $ingredientsList = $availableIngredients->map(function ($ingredient) {
                return "ID: {$ingredient->ingredients_id}, Name: {$ingredient->name_ar}";
            })->implode("\n");

            // بناء الـ Prompt
            $prompt = $this->buildIngredientsPrompt($mealName, $description, $quantity, $ingredientsList);

            // إرسال الطلب لـ Gemini (استخدام gemini-flash-latest)
            // استخدم دالة generativeModel لتحديد الموديل
            // سنستخدم gemini-flash-latest لأنه الأكثر استقراراً وشيوعاً
            $result = Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);
            // تنظيف النص من علامات Markdown
            $cleanJson = $this->cleanJsonResponse($result->text());

            // تحويل JSON إلى Array
            $suggestedData = json_decode($cleanJson, true);

            if (! $suggestedData || ! is_array($suggestedData)) {
                throw new Exception('فشل تحليل استجابة الذكاء الاصطناعي');
            }

            // التحقق من صحة البيانات
            $validatedData = $this->validateIngredientsData($suggestedData, $availableIngredients);

            Log::info('Gemini AI suggested ingredients successfully', [
                'meal_name' => $mealName,
                'suggestions_count' => count($validatedData),
            ]);

            return $validatedData;

        } catch (Exception $e) {
            Log::error('Gemini AI Error in suggestIngredients', [
                'error' => $e->getMessage(),
                'meal_name' => $mealName,
            ]);

            throw new Exception('حدث خطأ أثناء الاتصال بالذكاء الاصطناعي: '.$e->getMessage());
        }
    }

    /**
     * اقتراح الحميات المناسبة للوجبة
     *
     * @param  array  $mealData  بيانات الوجبة
     * @param  array  $ingredients  المكونات
     * @param  array  $nutritionValues  القيم الغذائية
     * @return array الحميات المقترحة
     *
     * @throws Exception
     */
    public function suggestDiets($mealData, $ingredients, $nutritionValues)
    {
        try {
            // جلب الحميات العامة المتاحة فقط
            $availableDiets = \App\Models\Diet::select('diets_id', 'name', 'description')
                ->where('is_public', true)
                ->get();

            // تحضير بيانات المكونات
            $ingredientsDetails = collect($ingredients)->map(function ($ing) {
                return "- {$ing['name_ar']}: {$ing['pivot']['quantity_g']}g";
            })->implode("\n");

            // تحضير قائمة الحميات
            $dietsList = $availableDiets->map(function ($diet) {
                return "ID: {$diet->diets_id}, Name: {$diet->name}, Description: {$diet->description}";
            })->implode("\n");

            // بناء الـ Prompt
            $prompt = $this->buildDietsPrompt(
                $mealData['name'],
                $ingredientsDetails,
                $nutritionValues,
                $dietsList
            );

            // إرسال الطلب لـ Gemini (استخدام gemini-2.0-flash)
            // استخدم دالة generativeModel لتحديد الموديل
            // سنستخدم gemini-flash-latest لأنه الأكثر استقراراً وشيوعاً
            $result = Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);
            // تنظيف وتحليل الاستجابة
            $cleanJson = $this->cleanJsonResponse($result->text());
            $suggestedData = json_decode($cleanJson, true);

            if (! $suggestedData || ! isset($suggestedData['suggested_diet_ids'])) {
                throw new Exception('فشل تحليل استجابة الذكاء الاصطناعي');
            }

            // التحقق من صحة IDs الحميات
            $validDietIds = $availableDiets->pluck('diets_id')->toArray();
            $suggestedData['suggested_diet_ids'] = array_filter(
                $suggestedData['suggested_diet_ids'],
                fn ($id) => in_array($id, $validDietIds)
            );

            Log::info('Gemini AI suggested diets successfully', [
                'meal_name' => $mealData['name'],
                'suggested_diets' => $suggestedData['suggested_diet_ids'],
            ]);

            return $suggestedData;

        } catch (Exception $e) {
            Log::error('Gemini AI Error in suggestDiets', [
                'error' => $e->getMessage(),
                'meal_name' => $mealData['name'] ?? 'unknown',
            ]);

            throw new Exception('حدث خطأ أثناء اقتراح الحميات: '.$e->getMessage());
        }
    }

    /**
     * اقتراح الوجبات المناسبة للحمية بناءً على القيود
     *
     * @param  array  $dietData  بيانات الحمية (name, description)
     * @param  array  $restrictions  القيود الغذائية
     * @param  \Illuminate\Support\Collection  $availableMeals  الوجبات المتاحة
     * @return array الوجبات المقترحة
     *
     * @throws Exception
     */
    public function suggestMealsForDiet($dietData, $restrictions, $availableMeals)
    {
        try {
            // تحضير قائمة الوجبات بصيغة مناسبة للـ AI
            $mealsList = $availableMeals->map(function ($meal) {
                return sprintf(
                    'ID: %d, Name: %s, Calories: %.2f, Protein: %.2fg, Fat: %.2fg, Carbs: %.2fg',
                    $meal->meals_id,
                    $meal->name,
                    $meal->calories ?? 0,
                    $meal->protein_g ?? 0,
                    $meal->fat_g ?? 0,
                    $meal->carbs_g ?? 0
                );
            })->implode("\n");

            // تحضير قائمة القيود
            $restrictionsList = collect($restrictions)->map(function ($restriction) {
                $fieldNames = [
                    'calories' => 'السعرات الحرارية',
                    'protein_g' => 'البروتين',
                    'fat_g' => 'الدهون',
                    'carbs_g' => 'الكربوهيدرات',
                ];

                $fieldName = $fieldNames[$restriction['field_name']] ?? $restriction['field_name'];

                return sprintf(
                    '- %s %s %s (%s)',
                    $fieldName,
                    $restriction['operator'],
                    $restriction['value'],
                    $restriction['restriction'] ?? ''
                );
            })->implode("\n");

            // بناء الـ Prompt
            $prompt = $this->buildMealsForDietPrompt(
                $dietData['name'],
                $dietData['description'] ?? '',
                $restrictionsList,
                $mealsList
            );

            // إرسال الطلب لـ Gemini
            $result = Gemini::generativeModel(model: 'gemini-flash-latest')->generateContent($prompt);

            // تنظيف وتحليل الاستجابة
            $cleanJson = $this->cleanJsonResponse($result->text());
            $suggestedData = json_decode($cleanJson, true);

            if (! $suggestedData || ! isset($suggestedData['suggested_meal_ids'])) {
                throw new Exception('فشل تحليل استجابة الذكاء الاصطناعي');
            }

            // التحقق من صحة IDs الوجبات
            $validMealIds = $availableMeals->pluck('meals_id')->toArray();
            $suggestedData['suggested_meal_ids'] = array_filter(
                $suggestedData['suggested_meal_ids'],
                fn ($id) => in_array($id, $validMealIds)
            );

            // جلب تفاصيل الوجبات المقترحة
            $suggestedMeals = $availableMeals->whereIn('meals_id', $suggestedData['suggested_meal_ids'])
                ->map(function ($meal) {
                    return [
                        'meals_id' => $meal->meals_id,
                        'name' => $meal->name,
                        'calories' => $meal->calories,
                        'protein_g' => $meal->protein_g,
                        'fat_g' => $meal->fat_g,
                        'carbs_g' => $meal->carbs_g,
                    ];
                })->values()->toArray();

            Log::info('Gemini AI suggested meals for diet successfully', [
                'diet_name' => $dietData['name'],
                'suggested_meals_count' => count($suggestedMeals),
            ]);

            return [
                'meals' => $suggestedMeals,
                'reason' => $suggestedData['reason'] ?? 'تم اختيار الوجبات بناءً على القيود المحددة',
            ];

        } catch (Exception $e) {
            Log::error('Gemini AI Error in suggestMealsForDiet', [
                'error' => $e->getMessage(),
                'diet_name' => $dietData['name'] ?? 'unknown',
            ]);

            throw new Exception('حدث خطأ أثناء اقتراح الوجبات: '.$e->getMessage());
        }
    }

    /**
     * بناء Prompt لاقتراح المكونات
     */
    private function buildIngredientsPrompt($mealName, $description, $quantity, $ingredientsList)
    {
        $quantityText = $quantity ? "- الكمية الإجمالية: {$quantity} جرام" : '';
        $descriptionText = $description ? "- الوصف: {$description}" : '';

        return <<<PROMPT
أنت خبير تغذية وذكاء اصطناعي متخصص في تحليل الوجبات.

معلومات الوجبة:
- الاسم: {$mealName}
{$descriptionText}
{$quantityText}

المكونات المتاحة في قاعدة البيانات:
{$ingredientsList}

المطلوب:
1. حلل الوجبة وحدد المكونات الأساسية التي تتكون منها
2. اختر المكونات من القائمة المتاحة فقط (استخدم الـ ID الموجود)
3. قدّر الكمية المناسبة بالجرام لكل مكون لوجبة  واحد
4. رد بصيغة JSON Array فقط بدون أي نص إضافي أو علامات markdown

الصيغة المطلوبة (مثال):
[
  {"ingredients_id": 1, "quantity_g": 150},
  {"ingredients_id": 5, "quantity_g": 30}
]

ملاحظات مهمة:
- استخدم فقط المكونات الموجودة في القائمة أعلاه
- الكميات يجب أن تكون منطقية ومناسبة لوجبة شخص واحد
- لا تضف أي تفسيرات أو نصوص خارج JSON
- لا تستخدم علامات ```json أو أي markdown
PROMPT;
    }

    /**
     * بناء Prompt لاقتراح الحميات
     */
    private function buildDietsPrompt($mealName, $ingredientsDetails, $nutritionValues, $dietsList)
    {
        return <<<PROMPT
أنت خبير تغذية متخصص في تحليل توافق الوجبات مع الحميات الغذائية.

الوجبة: {$mealName}

المكونات:
{$ingredientsDetails}

القيم الغذائية الإجمالية:
- السعرات الحرارية: {$nutritionValues['calories']} kcal
- البروتين: {$nutritionValues['protein_g']}g
- الدهون: {$nutritionValues['fat_g']}g
- الكربوهيدرات: {$nutritionValues['carbs_g']}g

الحميات المتاحة في النظام:
{$dietsList}

المطلوب:
حدد الحميات المناسبة من القائمة أعلاه بناءً على:
1. المكونات المستخدمة
2. القيم الغذائية
3. معايير كل حمية

رد بصيغة JSON فقط بدون أي نص إضافي أو علامات markdown:
{
  "suggested_diet_ids": [1, 4, 6],
  "reason": "شرح مختصر بالعربية لماذا هذه الحميات مناسبة"
}

ملاحظات:
- استخدم فقط IDs الحميات الموجودة في القائمة
- لا تستخدم علامات ```json أو أي markdown
PROMPT;
    }

    /**
     * تنظيف استجابة JSON من علامات Markdown
     */
    private function cleanJsonResponse($text)
    {
        // إزالة علامات markdown
        $cleaned = preg_replace('/```json\s*/', '', $text);
        $cleaned = preg_replace('/```\s*/', '', $cleaned);

        // إزالة المسافات الزائدة
        $cleaned = trim($cleaned);

        return $cleaned;
    }

    /**
     * التحقق من صحة بيانات المكونات المقترحة
     */
    private function validateIngredientsData($suggestedData, $availableIngredients)
    {
        $validIds = $availableIngredients->pluck('ingredients_id')->toArray();
        $validated = [];

        foreach ($suggestedData as $item) {
            // التحقق من وجود الحقول المطلوبة
            if (! isset($item['ingredients_id']) || ! isset($item['quantity_g'])) {
                continue;
            }

            // التحقق من أن الـ ID موجود في المكونات المتاحة
            if (! in_array($item['ingredients_id'], $validIds)) {
                continue;
            }

            // التحقق من أن الكمية منطقية
            if ($item['quantity_g'] <= 0 || $item['quantity_g'] > 5000) {
                continue;
            }

            $validated[] = [
                'ingredients_id' => (int) $item['ingredients_id'],
                'quantity_g' => (float) $item['quantity_g'],
            ];
        }

        return $validated;
    }

    /**
     * بناء Prompt لاقتراح الوجبات للحمية
     */
    private function buildMealsForDietPrompt($dietName, $description, $restrictionsList, $mealsList)
    {
        return <<<PROMPT
أنت خبير تغذية متخصص في تحليل توافق الوجبات مع الحميات الغذائية.

معلومات الحمية:
- الاسم: {$dietName}
- الوصف: {$description}

القيود الغذائية:
{$restrictionsList}

الوجبات المتاحة في التطبيق:
{$mealsList}

المطلوب:
1. حلل كل وجبة وتحقق من توافقها مع جميع القيود المحددة
2. اختر فقط الوجبات التي تطابق جميع القيود
3. رد بصيغة JSON فقط بدون أي نص إضافي أو علامات markdown

الصيغة المطلوبة:
{
  "suggested_meal_ids": [1, 5, 12, 23],
  "reason": "شرح مختصر بالعربية لماذا هذه الوجبات مناسبة"
}

ملاحظات مهمة:
- يجب أن تطابق الوجبة جميع القيود (AND logic)
- استخدم فقط IDs الوجبات الموجودة في القائمة
- لا تستخدم علامات ```json أو أي markdown
- إذا لم تجد وجبات مناسبة، أرجع مصفوفة فارغة
PROMPT;
    }

    /**
     * اقتراح حمية عامة للمستخدم بناءً على بياناته
     *
     * @param  array  $userData  بيانات المستخدم (age, gender, weight, height, activity_level, diseases, allergies)
     * @param  \Illuminate\Support\Collection  $publicDiets  الحميات العامة المتاحة
     * @return int|null ID الحمية المناسبة أو null إذا لم توجد
     *
     * @throws Exception
     */
    public function suggestDietForUser($userData, $publicDiets)
    {
        try {
            // تحضير قائمة الحميات
            $dietsList = $publicDiets->map(function ($diet) {
                return sprintf(
                    'ID: %d, Name: %s, Description: %s',
                    $diet->diets_id,
                    $diet->name,
                    $diet->description ?? 'لا يوجد وصف'
                );
            })->implode("\n");

            // بناء الـ Prompt
            $prompt = $this->buildUserDietPrompt($userData, $dietsList);

            // إرسال الطلب لـ Gemini
            $result = Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);

            // تنظيف وتحليل الاستجابة
            $cleanJson = $this->cleanJsonResponse($result->text());
            $response = json_decode($cleanJson, true);

            if (! $response || ! isset($response['diet_id'])) {
                throw new Exception('فشل تحليل استجابة الذكاء الاصطناعي');
            }

            // إرجاع ID الحمية
            $dietId = isset($response['diet_id']) ? (int) $response['diet_id'] : 0;

            // في حال الفشل أو العودة بـ 0، نستخدم الخيار الأول كاحتياط
            if ($dietId == 0 && $publicDiets->isNotEmpty()) {
                $dietId = $publicDiets->first()->diets_id;
                Log::info('Gemini fallback: Selected first available diet', ['diet_id' => $dietId]);
            }

            Log::info('Gemini AI suggested diet for user', [
                'user_data' => $userData,
                'suggested_diet_id' => $dietId,
            ]);

            return $dietId;

        } catch (Exception $e) {
            Log::error('Gemini AI Error in suggestDietForUser', [
                'error' => $e->getMessage(),
                'user_data' => $userData,
            ]);

            throw new Exception('حدث خطأ أثناء اقتراح الحمية: '.$e->getMessage());
        }
    }

    /**
     * توليد حمية خاصة للمستخدم
     *
     * @param  array  $userData  بيانات المستخدم
     * @param  \Illuminate\Support\Collection  $availableMeals  الوجبات المتاحة
     * @return array بيانات الحمية الجديدة مع الوجبات المقترحة
     *
     * @throws Exception
     */
    public function generatePrivateDiet($userData, $availableMeals)
    {
        try {
            // تحضير قائمة الوجبات
            $mealsList = $availableMeals->map(function ($meal) {
                return sprintf(
                    'ID: %d, Name: %s, Calories: %.2f, Protein: %.2fg, Fat: %.2fg, Carbs: %.2fg',
                    $meal->meals_id,
                    $meal->name,
                    $meal->calories ?? 0,
                    $meal->protein_g ?? 0,
                    $meal->fat_g ?? 0,
                    $meal->carbs_g ?? 0
                );
            })->implode("\n");

            // بناء الـ Prompt
            $prompt = $this->buildPrivateDietPrompt($userData, $mealsList);

            // إرسال الطلب لـ Gemini
            $result = Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);

            // تنظيف وتحليل الاستجابة
            $cleanJson = $this->cleanJsonResponse($result->text());
            $response = json_decode($cleanJson, true);

            if (! $response) {
                throw new Exception('فشل تحليل استجابة الذكاء الاصطناعي');
            }

            // التحقق من صحة IDs الوجبات
            $validMealIds = $availableMeals->pluck('meals_id')->toArray();
            if (isset($response['suggested_meal_ids'])) {
                $response['suggested_meal_ids'] = array_filter(
                    $response['suggested_meal_ids'],
                    fn ($id) => in_array($id, $validMealIds)
                );
            }

            Log::info('Gemini AI generated private diet', [
                'user_data' => $userData,
                'diet_name' => $response['name'] ?? 'حمية خاصة',
            ]);

            return $response;

        } catch (Exception $e) {
            Log::error('Gemini AI Error in generatePrivateDiet', [
                'error' => $e->getMessage(),
                'user_data' => $userData,
            ]);

            throw new Exception('حدث خطأ أثناء توليد الحمية الخاصة: '.$e->getMessage());
        }
    }

    /**
     * بناء Prompt لاقتراح حمية للمستخدم
     */
    private function buildUserDietPrompt($userData, $dietsList)
    {
        $age = $userData['age'] ?? 'غير محدد';
        $gender = $userData['gender'] ?? 'غير محدد';
        $weight = $userData['weight'] ?? 'غير محدد';
        $height = $userData['height'] ?? 'غير محدد';
        $activityLevel = $userData['activity_level'] ?? 'غير محدد';
        $diseases = ! empty($userData['diseases']) ? implode(', ', $userData['diseases']) : 'لا يوجد';
        $allergies = ! empty($userData['allergies']) ? implode(', ', $userData['allergies']) : 'لا يوجد';

        return <<<PROMPT
أنت خبير تغذية متخصص في تحليل الحالة الصحية واقتراح الحميات المناسبة.

بيانات المستخدم:
- العمر: {$age} سنة
- الجنس: {$gender}
- الوزن: {$weight} كجم
- الطول: {$height} سم
- مستوى النشاط: {$activityLevel}
- الأمراض المزمنة: {$diseases}
- الحساسيات: {$allergies}

الحميات العامة المتاحة:
{$dietsList}

المطلوب:
1. حلل الحالة الصحية للمستخدم
2. اختر الحمية الأنسب من القائمة أعلاه
3. يجب عليك اختيار حمية من القائمة. إذا لم تجد حمية مناسبة تماماً، اختر أقرب حمية مناسبة. لا ترجع 0 أبداً.

رد بصيغة JSON فقط بدون أي نص إضافي أو علامات markdown:
{
  "diet_id": 3,
  "reason": "شرح مختصر بالعربية لماذا هذه الحمية هي الأنسب (أو الأقرب)"
}

ملاحظات مهمة:
- استخدم فقط IDs الحميات الموجودة في القائمة
- يجب أن ترجع ID حمية صالح، لا ترجع 0 أو null
- لا تستخدم علامات ```json أو أي markdown
- لا تستخدم علامات ```json أو أي markdown
PROMPT;
    }

    /**
     * بناء Prompt لتوليد حمية خاصة
     */
    private function buildPrivateDietPrompt($userData, $mealsList)
    {
        $age = $userData['age'] ?? 'غير محدد';
        $gender = $userData['gender'] ?? 'غير محدد';
        $weight = $userData['weight'] ?? 'غير محدد';
        $height = $userData['height'] ?? 'غير محدد';
        $activityLevel = $userData['activity_level'] ?? 'غير محدد';
        $diseases = ! empty($userData['diseases']) ? implode(', ', $userData['diseases']) : 'لا يوجد';
        $allergies = ! empty($userData['allergies']) ? implode(', ', $userData['allergies']) : 'لا يوجد';

        return <<<PROMPT
أنت خبير تغذية متخصص في تصميم الحميات الغذائية المخصصة.

بيانات المستخدم:
- العمر: {$age} سنة
- الجنس: {$gender}
- الوزن: {$weight} كجم
- الطول: {$height} سم
- مستوى النشاط: {$activityLevel}
- الأمراض المزمنة: {$diseases}
- الحساسيات: {$allergies}

الوجبات المتاحة في التطبيق:
{$mealsList}

المطلوب:
1. صمم حمية غذائية مخصصة لهذا المستخدم
2. اختر الوجبات المناسبة من القائمة أعلاه
3. قدم نصائح وتحذيرات مهمة

رد بصيغة JSON فقط بدون أي نص إضافي أو علامات markdown:
{
  "name": "اسم الحمية المقترحة",
  "description": "وصف مفصل للحمية",
  "warning": "تحذيرات مهمة (إن وجدت)",
  "advice": "نصائح للمستخدم",
  "suggested_meal_ids": [1, 5, 12, 23],
  "reason": "شرح مختصر لماذا هذه الوجبات مناسبة"
}

ملاحظات مهمة:
- استخدم فقط IDs الوجبات الموجودة في القائمة
- تجنب الوجبات التي قد تسبب حساسية
- راعي الأمراض المزمنة في الاختيار
- لا تستخدم علامات ```json أو أي markdown
PROMPT;
    }
}
