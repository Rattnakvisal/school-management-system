@extends('website.home.partials.website')

@section('content')
    <main id="home" class="relative z-10" data-page-animate>
        @include('website.home.partials.hero')
        @include('website.home.partials.about')
        @include('website.home.partials.features')
        @include('website.home.partials.programs')
        @include('website.home.partials.facilities')
        @include('website.home.partials.admission')
        @include('website.home.partials.faq')
        @include('website.home.partials.contact')
    </main>
@endsection
