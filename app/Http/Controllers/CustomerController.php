<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'balance' => 'nullable|numeric',
        ]);

        try {
            Customer::create($validated);
            session()->flash('success', 'Customer created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create customer: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('customers.index');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'balance' => 'nullable|numeric',
        ]);

        try {
            $customer->update($validated);
            session()->flash('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update customer: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('customers.index');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            session()->flash('success', 'Customer deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete customer: ' . $e->getMessage());
        }
        return redirect()->route('customers.index');
    }
}
