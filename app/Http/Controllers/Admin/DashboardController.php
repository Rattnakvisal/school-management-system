<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminDashboardViewData;

class DashboardController extends Controller
{
    public function __invoke(AdminDashboardViewData $viewData)
    {
        if (strtolower(trim((string) (auth()->user()?->role ?? ''))) === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        return view('admin.dashboard', $viewData->make());
    }
}
