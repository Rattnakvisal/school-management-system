@extends('layout.teacher.navbar')

@section('page')
    @include('shared.mission-events', [
        'missions' => $missions,
        'title' => 'Mission Events',
        'subtitle' => 'Active mission events sent to teachers.',
        'emptyRoute' => route('teacher.dashboard'),
    ])
@endsection
