<?php

namespace App\Http\Controllers\NutritionManager;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index()
    {
        // Using MedicalRecord model as per user request for medications
        $medications = MedicalRecord::paginate(10);

        return view('nutrition-manager.medications.index', compact('medications'));
    }

    public function create()
    {
        return view('nutrition-manager.medications.create');
    }

    public function store(Request $request)
    {
        $messages = [
            'medical_record.required' => 'اسم الدواء مطلوب.',
            'medical_record.unique' => 'هذا الدواء مسجل مسبقاً.',
        ];

        $validatedData = $request->validate([
            'medical_record' => 'required|string|max:255|unique:medical_record,medical_record',
        ], $messages);

        MedicalRecord::create($validatedData);

        return redirect()->route('nutrition-manager.medications.index')
            ->with('success', 'تم إضافة الدواء بنجاح.');
    }

    public function edit($id)
    {
        $medication = MedicalRecord::findOrFail($id);

        return view('nutrition-manager.medications.edit', compact('medication'));
    }

    public function update(Request $request, $id)
    {
        $medication = MedicalRecord::findOrFail($id);

        $messages = [
            'medical_record.required' => 'اسم الدواء مطلوب.',
            'medical_record.unique' => 'هذا الدواء مسجل مسبقاً.',
        ];

        $validatedData = $request->validate([
            'medical_record' => 'required|string|max:255|unique:medical_record,medical_record,'.$medication->medical_record_id.',medical_record_id',
        ], $messages);

        $medication->update($validatedData);

        return redirect()->route('nutrition-manager.medications.index')
            ->with('success', 'تم تحديث الدواء بنجاح.');
    }

    public function destroy($id)
    {
        $medication = MedicalRecord::findOrFail($id);

        // Check if used by clients
        if ($medication->clients()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'لا يمكن حذف هذا الدواء لأنه مسجل في ملفات عملاء.']);
        }

        $medication->delete();

        return redirect()->route('nutrition-manager.medications.index')
            ->with('success', 'تم حذف الدواء بنجاح.');
    }
}
