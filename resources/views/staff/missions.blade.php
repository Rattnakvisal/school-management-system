@extends('layout.staff.navbar')

@section('page')
    @include('shared.mission-events', [
        'missions' => $missions,
        'title' => 'Mission Events',
        'subtitle' => 'Active mission events sent to staff.',
        'emptyRoute' => route('staff.dashboard'),
    ])
@endsection
