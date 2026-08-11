<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->query('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate($request->query('per_page', 15));

        return CustomerResource::collection($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'balance' => 'nullable|numeric',
        ]);

        return new CustomerResource(Customer::create($validated));
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:50',
            'balance' => 'nullable|numeric',
        ]);

        $customer->update($validated);

        return new CustomerResource($customer->fresh());
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted.'], 200);
    }
}
