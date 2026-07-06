<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OutletSwitcherController extends Controller
{
    public function switch(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        session(['active_outlet_id' => $request->outlet_id]);

        return back()->with('success', 'Outlet switched successfully.');
    }
}
