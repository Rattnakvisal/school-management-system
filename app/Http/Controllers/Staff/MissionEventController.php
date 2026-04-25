<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MissionEvent;

class MissionEventController extends Controller
{
    public function index()
    {
        $missions = MissionEvent::query()
            ->visibleToRole('staff')
            ->with('creator:id,name,role')
            ->latest('starts_at')
            ->latest()
            ->paginate(12);

        return view('staff.missions', [
            'missions' => $missions,
        ]);
    }
}
