<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
        ]);

        try {
            Supplier::create($validated);
            session()->flash('success', 'Supplier created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create supplier: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('suppliers.index');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
        ]);

        try {
            $supplier->update($validated);
            session()->flash('success', 'Supplier updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update supplier: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            session()->flash('success', 'Supplier deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
        return redirect()->route('suppliers.index');
    }
}
