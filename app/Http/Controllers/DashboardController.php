<?php

namespace App\Http\Controllers;

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
}
