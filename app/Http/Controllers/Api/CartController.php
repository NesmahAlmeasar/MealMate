<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Meal;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * API 10: عرض محتويات السلة للمستخدم الحالي
     * GET /api/cart
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // الحصول على السلة النشطة للمستخدم
            $cart = Cart::with(['items.meal', 'restaurant'])
                ->where('clients_id', $user->user_id)
                ->where('state', 'pending')
                ->first();

            if (! $cart) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'السلة فارغة',
                    'data' => [
                        'cart_id' => null,
                        'restaurant' => null,
                        'items' => [],
                        'summary' => [
                            'items_count' => 0,
                            'subtotal' => 0,
                            'tax' => 0,
                            'total' => 0,
                        ],
                    ],
                ]);
            }

            // حساب ملخص السلة
            $items = $cart->items->map(function ($item) {
                return [
                    'cart_item_id' => $item->cart_item_id,
                    'meal_id' => $item->meals_id,
                    'meal_name' => $item->meal->name,
                    'meal_photo' => $item->meal->photo_url,
                    'meal_price' => $item->meal->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->meal->price,
                ];
            });

            $subtotal = $items->sum('subtotal');
            $tax = $subtotal * 0.15; // نسبة الضريبة 15%
            $total = $subtotal + $tax;

            $cartSummary = [
                'cart_id' => $cart->cart_id,
                'restaurant' => $cart->restaurant ? [
                    'id' => $cart->restaurant->restaurants_id,
                    'name' => $cart->restaurant->name,
                    'photo_url' => $cart->restaurant->photo_url,
                ] : null,
                'items' => $items,
                'summary' => [
                    'items_count' => $items->count(),
                    'subtotal' => round($subtotal, 2),
                    'tax' => round($tax, 2),
                    'total' => round($total, 2),
                ],
                'state' => $cart->state,
                'created_at' => $cart->created_at,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب محتويات السلة بنجاح',
                'data' => $cartSummary,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب محتويات السلة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * API 10.5: ملخص القيمة الغذائية للسلة
     * GET /api/cart/nutrition
     */
    public function getCartNutrition(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // الحصول على السلة النشطة للمستخدم
            $cart = Cart::with(['items.meal'])
                ->where('clients_id', $user->user_id)
                ->where('state', 'pending')
                ->first();
            
            // قيم افتراضية
            $nutrition = [
                'calories' => 0,
                'carbs_g' => 0,
                'fat_g' => 0,
                'protein_g' => 0,
            ];

            if ($cart) {
                foreach ($cart->items as $item) {
                     if ($item->meal) {
                         $quantity = $item->quantity;
                         $nutrition['calories'] += ($item->meal->calories ?? 0) * $quantity;
                         $nutrition['carbs_g'] += ($item->meal->carbs_g ?? 0) * $quantity;
                         $nutrition['fat_g'] += ($item->meal->fat_g ?? 0) * $quantity;
                         $nutrition['protein_g'] += ($item->meal->protein_g ?? 0) * $quantity;
                     }
                }
            }
            
            // Round values
            $nutrition['calories'] = round($nutrition['calories']);
            $nutrition['carbs_g'] = round($nutrition['carbs_g'], 1);
            $nutrition['fat_g'] = round($nutrition['fat_g'], 1);
            $nutrition['protein_g'] = round($nutrition['protein_g'], 1);

            return response()->json([
                'status' => 'success',
                'message' => 'تم حساب القيمة الغذائية بنجاح',
                'data' => [
                    'cart_id' => $cart ? $cart->cart_id : null,
                    'summary' => $nutrition
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في حساب القيمة الغذائية',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 11: إضافة وجبة إلى السلة
     * POST /api/cart/add-item
     */
    public function addItem(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // التحقق من البيانات
            $request->validate([
                'meal_id' => 'required|exists:meals,meals_id',
                'quantity' => 'required|integer|min:1|max:20',
                'restaurant_id' => 'required|exists:restaurants,restaurants_id',
            ]);

            // التحقق من توفر الوجبة
            $meal = Meal::where('meals_id', $request->meal_id)
                ->where('state', 'approved')
                ->first();

            if (! $meal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الوجبة غير متاحة للطلب',
                ], 400);
            }

            // التحقق من أن الوجبة تابعة للمطعم المحدد
            if ($meal->restaurant_id != $request->restaurant_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذه الوجبة لا تنتمي إلى المطعم المحدد',
                ], 400);
            }

            // البحث عن سلة نشطة للمستخدم في نفس المطعم
            $cart = Cart::where('clients_id', $user->user_id)
                ->where('state', 'pending')
                ->where('restaurants_id', $request->restaurant_id)
                ->first();

            DB::beginTransaction();

            // إنشاء سلة جديدة إذا لم توجد
            if (! $cart) {
                $cart = Cart::create([
                    'clients_id' => $user->user_id,
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                    'total_price' => 0,
                    'state' => 'pending',
                    'restaurants_id' => $request->restaurant_id,
                ]);
            }

            // التحقق مما إذا كانت الوجبة موجودة بالفعل في السلة
            $existingItem = CartItem::where('cart_id', $cart->cart_id)
                ->where('meals_id', $request->meal_id)
                ->first();

            if ($existingItem) {
                // تحديث الكمية إذا كانت موجودة
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
            } else {
                // إضافة عنصر جديد
                CartItem::create([
                    'cart_id' => $cart->cart_id,
                    'meals_id' => $request->meal_id,
                    'quantity' => $request->quantity,
                ]);
            }

            // تحديث السعر الإجمالي للسلة
            $this->updateCartTotal($cart);

            DB::commit();

            // تجهيز ملخص سريع للرد
            $subtotal = $cart->total_price;
            $tax = round($subtotal * 0.15, 2);
            $total = round($subtotal + $tax, 2);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إضافة الوجبة إلى السلة بنجاح',
                'data' => [
                    'cart_id' => $cart->cart_id,
                    'meal_id' => $request->meal_id,
                    'quantity' => $existingItem ? $existingItem->quantity : $request->quantity,
                    'summary' => [
                        'subtotal' => $subtotal,
                        'tax' => $tax,
                        'total' => $total,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء إضافة الوجبة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 12: تحديث كمية عنصر في السلة
     * PUT /api/cart/update-item/{cartItemId}
     */
    public function updateItem(Request $request, $cartItemId): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $request->validate([
                'quantity' => 'required|integer|min:1|max:20',
            ]);

            $cartItem = CartItem::with('cart')
                ->where('cart_item_id', $cartItemId)
                ->first();

            if (! $cartItem) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'العنصر غير موجود في السلة',
                ], 404);
            }

            // التحقق من ملكية السلة
            if ($cartItem->cart->clients_id !== $user->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'غير مصرح لك بتعديل هذه السلة',
                ], 403);
            }

            DB::beginTransaction();

            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            // تحديث السعر الإجمالي للسلة
            $this->updateCartTotal($cartItem->cart);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث الكمية بنجاح',
                'data' => [
                    'cart_item_id' => $cartItem->cart_item_id,
                    'meal_id' => $cartItem->meals_id,
                    'quantity' => $cartItem->quantity,
                    'updated_at' => $cartItem->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تحديث الكمية',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 13: حذف عنصر من السلة
     * DELETE /api/cart/remove-item/{cartItemId}
     */
    public function removeItem($cartItemId): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $cartItem = CartItem::with('cart')
                ->where('cart_item_id', $cartItemId)
                ->first();

            if (! $cartItem) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'العنصر غير موجود في السلة',
                ], 404);
            }

            // التحقق من ملكية السلة
            if ($cartItem->cart->clients_id !== $user->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'غير مصرح لك بحذف هذا العنصر',
                ], 403);
            }

            DB::beginTransaction();

            $cart = $cartItem->cart;
            $mealId = $cartItem->meals_id;
            $cartItem->delete();

            // تحديث السعر الإجمالي للسلة
            $this->updateCartTotal($cart);

            // التحقق مما إذا كانت السلة فارغة وحذفها إذا لزم الأمر
            if ($cart->items()->count() === 0) {
                $cart->delete();
                $cart = null;
            }

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'تم حذف العنصر من السلة بنجاح',
                'data' => [
                    'cart_item_id' => $cartItemId,
                    'meal_id' => $mealId,
                    'cart_status' => $cart ? 'active' : 'deleted',
                ],
            ];

            if ($cart) {
                $response['data']['cart_id'] = $cart->cart_id;
                $response['data']['total_price'] = $cart->total_price;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حذف العنصر',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 14: تفريغ السلة
     * DELETE /api/cart/clear
     */
    public function clearCart(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $request->validate([
                'cart_id' => 'required|exists:cart,cart_id',
            ]);

            $cart = Cart::where('cart_id', $request->cart_id)
                ->where('clients_id', $user->user_id)
                ->where('state', 'pending')
                ->first();

            if (! $cart) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'السلة فارغة بالفعل',
                ]);
            }

            DB::beginTransaction();

            // حذف جميع العناصر
            CartItem::where('cart_id', $cart->cart_id)->delete();

            // حذف السلة نفسها
            $cart->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تفريغ السلة بنجاح',
                'data' => [
                    'cart_id' => $cart->cart_id,
                    'items_removed' => true,
                    'cart_removed' => true,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تفريغ السلة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 15: تأكيد الطلب (مع WhatsApp Integration)
     * POST /api/cart/confirm
     */
    public function confirmOrder(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $request->validate([
                'cart_id' => 'required|exists:cart,cart_id',
                'location_id' => 'nullable|exists:locations,location_id',
                'special_instructions' => 'nullable|string|max:500',
                'notification_method' => 'nullable|in:whatsapp,sms,both',
            ]);

            $cart = Cart::with(['items.meal', 'restaurant', 'client.user'])
                ->where('cart_id', $request->cart_id)
                ->where('clients_id', $user->user_id)
                ->where('state', 'pending')
                ->first();

            if (! $cart || $cart->items()->count() === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'السلة فارغة، لا يمكن تأكيد الطلب',
                ], 400);
            }

            DB::beginTransaction();

            // تحديث حالة السلة
            $cart->state = 'processing';

            if ($request->has('location_id')) {
                $cart->location_id = $request->location_id;
            }

            $cart->save();

            // Notify Restaurant Manager
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->notifyRestaurantManager($cart);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send order notification: '.$e->getMessage());
            }

            DB::commit();

            // حساب الملخص المالي
            $subtotal = $cart->total_price;
            $tax = round($subtotal * 0.15, 2);
            $total = round($subtotal + $tax, 2);

            // تحضير بيانات الطلب
            $orderData = [
                'order_id' => $cart->cart_id,
                'cart_id' => $cart->cart_id,
                'state' => $cart->state,
                'restaurant' => $cart->restaurant ? [
                    'id' => $cart->restaurant->restaurants_id,
                    'name' => $cart->restaurant->name,
                    'phone' => $this->getRestaurantPhone($cart->restaurant),
                ] : null,
                'customer' => [
                    'name' => $user->Fname.' '.$user->Lname,
                    'phone' => $user->phone ?? 'غير متوفر',
                ],
                'items' => $cart->items->map(function ($item) {
                    $mealName = $item->meal ? $item->meal->name : 'وجبة محذوفة';
                    $mealPrice = $item->meal ? $item->meal->price : 0;

                    return [
                        'meal_name' => $mealName,
                        'quantity' => $item->quantity,
                        'price' => $mealPrice,
                        'subtotal' => $item->quantity * $mealPrice,
                    ];
                }),
                'summary' => [
                    'items_count' => $cart->items->count(),
                    'subtotal' => round($subtotal, 2),
                    'tax_rate' => 0.15,
                    'tax' => round($tax, 2),
                    'total' => round($total, 2),
                ],
                'location_id' => $cart->location_id,
                'special_instructions' => $request->special_instructions,
                'order_date' => $cart->date,
                'order_time' => $cart->time,
                'estimated_preparation_time' => $this->calculatePreparationTime($cart),
            ];

            // إضافة معلومات الإشعارات
            $notificationMethod = $request->notification_method ?? 'whatsapp';
            $orderData['notification'] = [
                'method' => $notificationMethod,
            ];

            // إنشاء رسالة ورابط WhatsApp
            if (in_array($notificationMethod, ['whatsapp', 'both'])) {
                $whatsappMessage = $this->prepareWhatsAppMessage($orderData);
                $restaurantPhone = $this->getRestaurantPhone($cart->restaurant);

                if ($restaurantPhone) {
                    $whatsappUrl = $this->generateWhatsAppUrl($restaurantPhone, $whatsappMessage);

                    $orderData['notification']['whatsapp'] = [
                        'phone' => $restaurantPhone,
                        'message' => $whatsappMessage,
                        'url' => $whatsappUrl,
                    ];
                }
            }

            // SMS (اختياري - يمكن تطويره لاحقاً)
            if (in_array($notificationMethod, ['sms', 'both'])) {
                $orderData['notification']['sms'] = [
                    'status' => 'not_implemented',
                    'message' => 'خدمة SMS غير متوفرة حالياً',
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'تم تأكيد الطلب بنجاح',
                'data' => $orderData,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تأكيد الطلب',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحضير رسالة WhatsApp للمطعم
     */
    private function prepareWhatsAppMessage(array $orderData): string
    {
        $restaurant = $orderData['restaurant'];
        $customer = $orderData['customer'];
        $items = $orderData['items'];
        $summary = $orderData['summary'];

        $message = "مرحباً {$restaurant['name']} 🍽️\n\n";
        $message .= "لديك طلب جديد من تطبيق MealMate:\n\n";
        $message .= "📋 رقم الطلب: #{$orderData['order_id']}\n";
        $message .= "👤 الاسم: {$customer['name']}\n";
        $message .= "📞 الهاتف: {$customer['phone']}\n\n";

        $message .= "🍔 الطلبات:\n";
        foreach ($items as $index => $item) {
            $num = $index + 1;
            $message .= "{$num}. {$item['meal_name']} × {$item['quantity']} = {$item['subtotal']} ريال\n";
        }

        $message .= "\n💰 المجموع الفرعي: {$summary['subtotal']} ريال\n";
        $message .= "💰 الضريبة (15%): {$summary['tax']} ريال\n";
        $message .= "💰 الإجمالي: {$summary['total']} ريال\n\n";

        if (! empty($orderData['location_id'])) {
            $message .= "📍 رقم الموقع: {$orderData['location_id']}\n";
        }

        if (! empty($orderData['special_instructions'])) {
            $message .= "📝 ملاحظات: {$orderData['special_instructions']}\n";
        }

        $message .= "\n⏰ الوقت: ".now()->format('H:i')."\n";
        $message .= "⏱️ وقت التحضير المتوقع: {$orderData['estimated_preparation_time']} دقيقة\n\n";
        $message .= 'شكراً لكم 🙏';

        return $message;
    }

    /**
     * الحصول على رقم هاتف المطعم
     */
    private function getRestaurantPhone($restaurant): ?string
    {
        if (! $restaurant) {
            return null;
        }

        // محاولة الحصول على الرقم من جدول phone
        $phone = \App\Models\Phone::where('restaurants_id', $restaurant->restaurants_id)
            ->first();

        if ($phone && $phone->phone_number) {
            // تنظيف الرقم وتحويله للصيغة الدولية
            $phoneNumber = preg_replace('/[^0-9]/', '', $phone->phone_number);

            // إذا كان الرقم يبدأ بـ 0، استبدله بـ 966 (السعودية)
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '966'.substr($phoneNumber, 1);
            }
            // إذا لم يبدأ بـ 966، أضفها
            elseif (substr($phoneNumber, 0, 3) !== '966') {
                $phoneNumber = '966'.$phoneNumber;
            }

            return $phoneNumber;
        }

        return null;
    }

    /**
     * توليد رابط WhatsApp
     */
    private function generateWhatsAppUrl(string $phone, string $message): string
    {
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }

    /**
     * دالة مساعدة لتحديث السعر الإجمالي للسلة
     */
    private function updateCartTotal(Cart $cart): void
    {
        $total = 0;

        $items = CartItem::with('meal')
            ->where('cart_id', $cart->cart_id)
            ->get();

        foreach ($items as $item) {
            if ($item->meal) {
                $total += $item->quantity * $item->meal->price;
            }
        }

        $cart->total_price = $total;
        $cart->save();
    }

    /**
     * دالة مساعدة لحساب وقت التحضير التقديري
     */
    private function calculatePreparationTime(Cart $cart): int
    {
        $maxPreparationTime = 0;

        foreach ($cart->items as $item) {
            if ($item->meal && $item->meal->preparation_time > $maxPreparationTime) {
                $maxPreparationTime = $item->meal->preparation_time;
            }
        }

        return $maxPreparationTime + 10; // إضافة 10 دقائق كهامش
    }

    /**
     * API جديد: عرض جميع السلات للمستخدم
     * GET /api/carts
     */
    public function getAllCarts(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            // بناء الاستعلام
            $query = Cart::with(['restaurant', 'location', 'items.meal'])
                ->where('clients_id', $user->user_id);

            // فلترة حسب الحالة
            if ($request->has('state')) {
                // إذا تم تمرير حالة محددة
                $query->where('state', $request->state);
            } elseif ($request->has('type')) {
                // إذا تم طلب نوع محدد (active أو history)
                if ($request->type === 'active') {
                    $query->where('state', 'pending');
                } elseif ($request->type === 'history') {
                    $query->where('state', '!=', 'pending');
                }
            }

            // ترتيب حسب آخر تحديث
            $query->orderBy('updated_at', 'desc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $carts = $query->paginate($perPage);

            // تنسيق البيانات
            $cartsData = $carts->map(function ($cart) {
                return [
                    'cart_id' => $cart->cart_id,
                    'id' => $cart->cart_id, // For backward compatibility if needed
                    'date' => $cart->date,
                    'time' => $cart->time,
                    'total_price' => $cart->total_price,
                    'state' => $cart->state,
                    'status' => $cart->state, // For backward compatibility
                    'restaurant' => $cart->restaurant ? [
                        'id' => $cart->restaurant->restaurants_id,
                        'name' => $cart->restaurant->name,
                        'photo_url' => $cart->restaurant->photo_url,
                    ] : null,
                    'location' => $cart->location ? [
                        'id' => $cart->location->location_id,
                        'description' => $cart->location->description,
                        'latitude' => $cart->location->latitude_x,
                        'longitude' => $cart->location->longitude_y,
                    ] : null,
                    'items_count' => $cart->items->count(),
                    'items' => $cart->items->map(function ($item) {
                        return [
                            'cart_item_id' => $item->cart_item_id,
                            'meal_name' => $item->meal ? $item->meal->name : 'وجبة محذوفة',
                            'meal_price' => $item->meal ? $item->meal->price : 0,
                            'quantity' => $item->quantity,
                            'subtotal' => $item->quantity * ($item->meal ? $item->meal->price : 0),
                        ];
                    }),
                    'summary' => [
                        'subtotal' => $cart->total_price,
                        'tax' => round($cart->total_price * 0.15, 2),
                        'total' => round($cart->total_price * 1.15, 2),
                    ],
                    'created_at' => $cart->created_at,
                    'updated_at' => $cart->updated_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب السلات بنجاح',
                'data' => [
                    'carts' => $cartsData,
                    'pagination' => [
                        'current_page' => $carts->currentPage(),
                        'total_pages' => $carts->lastPage(),
                        'total_carts' => $carts->total(),
                        'per_page' => $carts->perPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب السلات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API جديد: عرض تفاصيل سلة محددة
     * GET /api/carts/{cartId}
     */
    public function getCartDetails($cartId): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $cart = Cart::with(['items.meal', 'restaurant', 'location'])
                ->where('cart_id', $cartId)
                ->first();

            if (! $cart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'السلة غير موجودة',
                ], 404);
            }

            // التحقق من ملكية السلة
            if ($cart->clients_id !== $user->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'غير مصرح لك بعرض هذه السلة',
                ], 403);
            }

            // تنسيق الوجبات
            $items = $cart->items->map(function ($item) {
                return [
                    'cart_item_id' => $item->cart_item_id,
                    'meal_id' => $item->meals_id,
                    'meal_name' => $item->meal->name,
                    'meal_photo' => $item->meal->photo_url,
                    'meal_price' => $item->meal->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->meal->price,
                ];
            });

            $subtotal = $items->sum('subtotal');
            $tax = $subtotal * 0.15;
            $total = $subtotal + $tax;

            $cartDetails = [
                'cart_id' => $cart->cart_id,
                'date' => $cart->date,
                'time' => $cart->time,
                'total_price' => $cart->total_price,
                'state' => $cart->state,
                'clients_id' => $cart->clients_id,
                'location' => $cart->location ? [
                    'location_id' => $cart->location->location_id,
                    'description' => $cart->location->description,
                    'latitude' => $cart->location->latitude_x,
                    'longitude' => $cart->location->longitude_y,
                ] : null,
                'restaurant' => $cart->restaurant ? [
                    'id' => $cart->restaurant->restaurants_id,
                    'name' => $cart->restaurant->name,
                    'photo_url' => $cart->restaurant->photo_url,
                    'phone' => $this->getRestaurantPhone($cart->restaurant),
                ] : null,
                'items' => $items,
                'summary' => [
                    'items_count' => $items->count(),
                    'subtotal' => round($subtotal, 2),
                    'tax' => round($tax, 2),
                    'total' => round($total, 2),
                ],
                'created_at' => $cart->created_at,
                'updated_at' => $cart->updated_at,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب تفاصيل السلة بنجاح',
                'data' => $cartDetails,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب تفاصيل السلة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API جديد: تحديث موقع السلة
     * PUT /api/carts/{cartId}/location
     */
    public function updateCartLocation(Request $request, $cartId): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ], 401);
            }

            $request->validate([
                'location_id' => 'required|exists:locations,location_id',
            ]);

            $cart = Cart::where('cart_id', $cartId)->first();

            if (! $cart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'السلة غير موجودة',
                ], 404);
            }

            // التحقق من ملكية السلة
            if ($cart->clients_id !== $user->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'غير مصرح لك بتعديل هذه السلة',
                ], 403);
            }

            // التحقق من ملكية الموقع
            $locationExists = DB::table('client_addresses')
                ->where('clients_id', $user->user_id)
                ->where('location_id', $request->location_id)
                ->exists();

            if (! $locationExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذا الموقع غير مرتبط بحسابك',
                ], 403);
            }

            $cart->location_id = $request->location_id;
            $cart->save();

            $location = \App\Models\Location::find($request->location_id);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث موقع التسليم بنجاح',
                'data' => [
                    'cart_id' => $cart->cart_id,
                    'location_id' => $cart->location_id,
                    'location' => [
                        'description' => $location->description,
                        'latitude' => $location->latitude_x,
                        'longitude' => $location->longitude_y,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في تحديث موقع السلة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
