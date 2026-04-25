<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MissionEvent;

class MissionEventController extends Controller
{
    public function index()
    {
        $missions = MissionEvent::query()
            ->visibleToRole('teacher')
            ->with('creator:id,name,role')
            ->latest('starts_at')
            ->latest()
            ->paginate(12);

        return view('teacher.missions', [
            'missions' => $missions,
        ]);
    }
}
