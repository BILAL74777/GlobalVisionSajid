<?php

namespace App\Http\Controllers;
use App\Models\FamilyVisa;
use App\Models\Visa;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    { 
        return Inertia::render('Dashboard/Index');
    }
    public function create()
    {
        return Inertia::render('Dashboard/Create', [
            'title' => "Create new Events",
        ]);
    }

    public function dashboard_fetch()
    {
        $visas = Visa::select('id', 'full_name', 'visa_fee', 'amount', 'date')
                    ->get();

        $familyVisas = FamilyVisa::select('id', 'visa_id', 'full_name', 'visa_fee', 'amount', 'date')
                    ->get();

        return response()->json([
            'visas' => $visas,
            'family_visas' => $familyVisas
        ]);
    }
}
