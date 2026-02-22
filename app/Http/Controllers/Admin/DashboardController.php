<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $studentsTotal = User::where('role', 'student')->count();
        $teachersTotal = User::where('role', 'teacher')->count();
        $latestContactMessages = ContactMessage::query()
            ->orderBy('is_read')
            ->latest()
            ->take(4)
            ->get();

        return view('admin.dashboard', [
            'studentsTotal' => $studentsTotal,
            'teachersTotal' => $teachersTotal,
            'latestContactMessages' => $latestContactMessages,
        ]);
    }
}
