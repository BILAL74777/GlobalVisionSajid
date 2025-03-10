<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class EventsController extends Controller
{
    public function index()
    {
        return Inertia::render('Events/Index');
    }
    public function create()
    {
        return Inertia::render('Events/Create', [
            'title' => "Create new Events",
        ]);
    }
}
