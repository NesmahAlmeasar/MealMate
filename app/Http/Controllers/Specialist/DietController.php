<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\Diet;
use App\Models\Meal;
use App\Models\Restriction;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DietController extends Controller
{
    /**
     * Display a listing of diets for the specialist
     */
    public function index()
    {
        $diets = Diet::with(['nutritionist.user', 'meals'])
            ->latest()
            ->get();

        return view('diets.index', compact('diets'));
    }

    /**
     * Show the form for creating a new diet
     */
    public function create(Request $request)
    {
        $meals = Meal::where('state', 'approved')->with('category')->get();

        // Get consultation_id and client_id from query parameters
        $consultationId = $request->query('consultation_id');
        $clientId = $request->query('client_id');

        // If client_id is provided, get client details
        $selectedClient = null;
        if ($clientId) {
            $selectedClient = \App\Models\Client::with('user')
                ->where('clients_id', $clientId)
                ->first();
        }

        // Get clients with open consultations for this specialist
        $nutritionistId = Auth::id();

        // Get all active consultations for this nutritionist
        $activeConsultations = \App\Models\Consultation::where('nutritionist_id', $nutritionistId)
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->with('client')
            ->get();

        // Extract unique client IDs (user_ids) from consultations
        $clientUserIds = $activeConsultations->pluck('client_id')->unique();

        // Get Client records for these user_ids
        $clients = \App\Models\Client::whereIn('clients_id', $clientUserIds)
            ->with('user')
            ->get();

        // Check if user can create public diets (Admin or Nutrition Manager)
        $canCreatePublic = Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Nutrition Manager');

        return view('diets.create', compact('meals', 'consultationId', 'clientId', 'selectedClient', 'clients', 'canCreatePublic'));
    }

    /**
     * Store a newly created diet in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'boolean',
            'warning' => 'nullable|string',
            'advice' => 'nullable|string',
            'meals' => 'nullable|array',
            'meals.*' => 'exists:meals,meals_id',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:clients,clients_id',
            'restrictions' => 'nullable|array',
            'restrictions.*.field_name' => 'required|string',
            'restrictions.*.operator' => 'required|string',
            'restrictions.*.value' => 'required|string',
            'restrictions.*.restriction' => 'nullable|string',
            'consultation_id' => 'nullable|exists:consultations,consultation_id',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('diets', 'public');
        }

        // Get current user's nutritionist_id
        $nutritionistId = Auth::id();

        // Ensure nutritionist record exists
        $nutritionist = \App\Models\Nutritionist::firstOrCreate(
            ['nutritionist_id' => $nutritionistId],
            [
                'Academic_level' => 'Specialist',
                'description' => 'Nutrition Specialist',
            ]
        );

        // Create the diet
        $diet = Diet::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $photoPath,
            'is_public' => $request->has('is_public') ? true : false,
            'warning' => $validated['warning'] ?? null,
            'advice' => $validated['advice'] ?? null,
            'nutritionist_id' => $nutritionistId,
        ]);

        // Attach meals
        if (! empty($validated['meals'])) {
            $diet->meals()->attach($validated['meals']);
        }

        // Create restrictions
        if (! empty($validated['restrictions'])) {
            foreach ($validated['restrictions'] as $restrictionData) {
                Restriction::create([
                    'diets_id' => $diet->diets_id,
                    'field_name' => $restrictionData['field_name'],
                    'operator' => $restrictionData['operator'],
                    'value' => $restrictionData['value'],
                    'restriction' => $restrictionData['restriction'] ?? null,
                ]);
            }
        }

        // Link to Consultation if provided
        if (! empty($validated['consultation_id'])) {
            $consultation = \App\Models\Consultation::find($validated['consultation_id']);
            if ($consultation) {
                // Link Diet to Consultation
                $consultation->update(['diet_id' => $diet->diets_id]);

                // Link Diet to Client (Private Diet)
                $clientRecord = \App\Models\Client::where('clients_id', $consultation->client_id)->first();
                if ($clientRecord) {
                    $clientRecord->diets()->syncWithoutDetaching([$diet->diets_id]);
                }
            }
        } else {
            // If no consultation_id, check for manually selected clients
            if ($request->has('clients') && ! empty($request->clients)) {
                $diet->clients()->attach($request->clients);
            }
        }

        return redirect()->route('specialist.diets.index')->with('success', 'تم إنشاء الحمية بنجاح!');
    }

    /**
     * Display the specified diet
     */
    public function show(string $id)
    {
        $diet = Diet::with(['nutritionist.user', 'meals.category', 'restrictions'])->findOrFail($id);

        return view('diets.show', compact('diet'));
    }

    /**
     * Show the form for editing the specified diet
     */
    public function edit(string $id)
    {
        $diet = Diet::with(['meals', 'restrictions', 'clients.user'])->findOrFail($id);
        $meals = Meal::where('state', 'approved')->with('category')->get();

        // Get all active consultations for this nutritionist
        $nutritionistId = Auth::id();
        $activeConsultations = \App\Models\Consultation::where('nutritionist_id', $nutritionistId)
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->get();

        // Extract unique client IDs (user_ids) from consultations
        $clientUserIds = $activeConsultations->pluck('client_id')->unique();

        // Get associated client IDs from the diet
        $associatedClientIds = $diet->clients->pluck('clients_id');

        // Merge to keep existing selections
        $allClientIds = $clientUserIds->merge($associatedClientIds)->unique();

        // Get Client records
        $clients = \App\Models\Client::whereIn('clients_id', $allClientIds)
            ->with('user')
            ->get();

        // Check if user can create public diets
        $canCreatePublic = Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Nutrition Manager');

        return view('diets.edit', compact('diet', 'meals', 'clients', 'canCreatePublic'));
    }

    /**
     * Update the specified diet in database
     */
    public function update(Request $request, string $id)
    {
        $diet = Diet::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'boolean',
            'warning' => 'nullable|string',
            'advice' => 'nullable|string',
            'meals' => 'nullable|array',
            'meals.*' => 'exists:meals,meals_id',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:clients,clients_id',
            'restrictions' => 'nullable|array',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($diet->photo_url) {
                Storage::disk('public')->delete($diet->photo_url);
            }
            $validated['photo_url'] = $request->file('photo')->store('diets', 'public');
        }

        // Update diet
        $diet->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $validated['photo_url'] ?? $diet->photo_url,
            'is_public' => $request->has('is_public') ? true : false,
            'warning' => $validated['warning'] ?? null,
            'advice' => $validated['advice'] ?? null,
        ]);

        // Sync meals
        if (isset($validated['meals'])) {
            $diet->meals()->sync($validated['meals']);
        } else {
            $diet->meals()->detach();
        }

        // Update restrictions
        if (isset($validated['restrictions'])) {
            $diet->restrictions()->delete();
            foreach ($validated['restrictions'] as $restrictionData) {
                Restriction::create([
                    'diets_id' => $diet->diets_id,
                    'field_name' => $restrictionData['field_name'],
                    'operator' => $restrictionData['operator'],
                    'value' => $restrictionData['value'],
                    'restriction' => $restrictionData['restriction'] ?? null,
                ]);
            }
        }

        // Update clients (for private diets)
        if (! $diet->is_public) {
            if ($request->has('clients') && ! empty($request->clients)) {
                $diet->clients()->sync($request->clients);
            } else {
                $diet->clients()->detach();
            }
        } else {
            // If diet is public, remove all client associations
            $diet->clients()->detach();
        }

        return redirect()->route('specialist.diets.index')->with('success', 'تم تحديث الحمية بنجاح!');
    }

    /**
     * Remove the specified diet from database
     */
    public function destroy(string $id)
    {
        $diet = Diet::findOrFail($id);

        if ($diet->photo_url) {
            Storage::disk('public')->delete($diet->photo_url);
        }

        $diet->delete();

        return redirect()->route('specialist.diets.index')->with('success', 'Diet deleted successfully!');
    }

    /**
     * AI Suggest Meals for Diet (AJAX Endpoint)
     *
     * يستقبل بيانات الحمية والقيود ويرسلها لـ Gemini AI
     * ويعيد قائمة بالوجبات المناسبة
     */
    public function aiSuggestMeals(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'restrictions' => 'nullable|array',
                'restrictions.*.field_name' => 'required|string',
                'restrictions.*.operator' => 'required|string',
                'restrictions.*.value' => 'required|numeric',
                'restrictions.*.restriction' => 'nullable|string',
            ]);

            // جلب جميع الوجبات المعتمدة
            $meals = Meal::select('meals_id', 'name', 'calories', 'protein_g', 'fat_g', 'carbs_g')
                ->where('state', 'approved')
                ->get();

            if ($meals->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد وجبات معتمدة في النظام',
                ], 400);
            }

            // استدعاء GeminiService
            $geminiService = new GeminiService;
            $result = $geminiService->suggestMealsForDiet(
                $request->only(['name', 'description']),
                $request->restrictions ?? [],
                $meals
            );

            if (empty($result['meals'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتمكن الذكاء الاصطناعي من إيجاد وجبات مناسبة للقيود المحددة',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم اقتراح الوجبات بنجاح',
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
