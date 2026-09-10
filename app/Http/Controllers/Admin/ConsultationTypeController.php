<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationType;
use Illuminate\Http\Request;

class ConsultationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = ConsultationType::paginate(10);
        return view('admin.consultation_types.index', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ]);

        ConsultationType::create($validated);

        return redirect()->route('consultation-types.index')->with('success', 'تم إضافة نوع الاستشارة بنجاح');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $type = ConsultationType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ]);

        $type->update($validated);

        return redirect()->route('consultation-types.index')->with('success', 'تم تحديث نوع الاستشارة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $type = ConsultationType::findOrFail($id);
        $type->delete();

        return redirect()->route('consultation-types.index')->with('success', 'تم حذف نوع الاستشارة بنجاح');
    }
}
