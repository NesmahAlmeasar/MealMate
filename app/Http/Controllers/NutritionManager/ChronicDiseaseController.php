<?php

namespace App\Http\Controllers\NutritionManager;

use App\Http\Controllers\Controller;
use App\Models\ChronicDisease;
use Illuminate\Http\Request;

class ChronicDiseaseController extends Controller
{
    public function index()
    {
        $diseases = ChronicDisease::paginate(10);

        return view('nutrition-manager.chronic-diseases.index', compact('diseases'));
    }

    public function create()
    {
        return view('nutrition-manager.chronic-diseases.create');
    }

    public function store(Request $request)
    {
        $messages = [
            'chronic_diseases.required' => 'اسم المرض مطلوب.',
            'chronic_diseases.unique' => 'هذا المرض مسجل مسبقاً.',
        ];

        $validatedData = $request->validate([
            'chronic_diseases' => 'required|string|max:255|unique:chronic_diseases,chronic_diseases',
        ], $messages);

        ChronicDisease::create($validatedData);

        return redirect()->route('nutrition-manager.chronic-diseases.index')
            ->with('success', 'تم إضافة المرض المزمن بنجاح.');
    }

    public function edit(ChronicDisease $chronicDisease)
    {
        return view('nutrition-manager.chronic-diseases.edit', compact('chronicDisease'));
    }

    public function update(Request $request, ChronicDisease $chronicDisease)
    {
        $messages = [
            'chronic_diseases.required' => 'اسم المرض مطلوب.',
            'chronic_diseases.unique' => 'هذا المرض مسجل مسبقاً.',
        ];

        $validatedData = $request->validate([
            'chronic_diseases' => 'required|string|max:255|unique:chronic_diseases,chronic_diseases,'.$chronicDisease->chronic_diseases_id.',chronic_diseases_id',
        ], $messages);

        $chronicDisease->update($validatedData);

        return redirect()->route('nutrition-manager.chronic-diseases.index')
            ->with('success', 'تم تحديث المرض المزمن بنجاح.');
    }

    public function destroy(ChronicDisease $chronicDisease)
    {
        // Check if used by clients
        if ($chronicDisease->clients()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'لا يمكن حذف هذا المرض لأنه مسجل في ملفات عملاء.']);
        }

        $chronicDisease->delete();

        return redirect()->route('nutrition-manager.chronic-diseases.index')
            ->with('success', 'تم حذف المرض المزمن بنجاح.');
    }
}
