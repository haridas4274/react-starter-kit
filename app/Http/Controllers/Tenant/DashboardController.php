<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Inertia::render('Tenant/Dashboard');
        return 'tenant dashboard';
    }
}
