<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Show the open-shift form (if no shift is open) or the active shift status.
     */
    public function status()
    {
        $activeShift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        return response()->json([
            'has_open_shift' => (bool) $activeShift,
            'shift' => $activeShift ? [
                'id'             => $activeShift->id,
                'opening_float'  => (float) $activeShift->opening_float,
                'opened_at'      => $activeShift->opened_at->format('H:i'),
                'sales_count'    => $activeShift->sales_count,
                'total_sales'    => (float) $activeShift->total_sales,
            ] : null,
        ]);
    }

    /**
     * Open a new shift.
     */
    public function open(Request $request)
    {
        // Prevent opening multiple shifts
        $existingShift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->exists();

        if ($existingShift) {
            return back()->with('error', 'You already have an open shift. Close it before opening a new one.');
        }

        $validated = $request->validate([
            'opening_float' => 'required|numeric|min:0',
        ]);

        $branchId = Auth::user()->branch_id
            ?? Branch::first()?->id
            ?? null;

        Shift::create([
            'user_id'        => Auth::id(),
            'branch_id'      => $branchId,
            'opening_float'  => $validated['opening_float'],
            'status'         => 'open',
            'opened_at'      => now(),
        ]);

        return redirect()->route('sales.create')
            ->with('success', 'Shift opened with $' . number_format($validated['opening_float'], 2) . ' float. Good luck!');
    }

    /**
     * Close the active shift and generate Z-Report data.
     */
    public function close(Request $request)
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->firstOrFail();

        $validated = $request->validate([
            'closing_float' => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);

        // Calculate expected cash: opening float + all cash sales during shift
        $cashSales = $shift->sales()
            ->where('payment_method', 'cash')
            ->sum('total');

        $expectedCash   = (float) $shift->opening_float + $cashSales;
        $closingFloat   = (float) $validated['closing_float'];
        $variance       = $closingFloat - $expectedCash;

        $shift->update([
            'closing_float' => $closingFloat,
            'expected_cash' => $expectedCash,
            'cash_variance' => $variance,
            'status'        => 'closed',
            'notes'         => $validated['notes'] ?? null,
            'closed_at'     => now(),
        ]);

        return redirect()->route('reports.z-report', ['shift_id' => $shift->id])
            ->with('success', 'Shift closed. ' . ($variance >= 0
                ? 'Cash is over by $' . number_format(abs($variance), 2) . '.'
                : 'Cash is short by $' . number_format(abs($variance), 2) . '.'));
    }

    /**
     * Show the shift history for admin users.
     */
    public function index()
    {
        $shifts = Shift::with('user', 'branch')
            ->latest('opened_at')
            ->paginate(20);

        return view('shifts.index', compact('shifts'));
    }

    /**
     * Show a specific closed shift's Z-Report.
     */
    public function show(Shift $shift)
    {
        $shift->load('user', 'branch', 'sales.items.product');
        return view('shifts.show', compact('shift'));
    }
}
