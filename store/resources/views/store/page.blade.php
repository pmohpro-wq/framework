@extends('layouts.store')

@section('title', $title . ' — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => $title]]])

        <div class="page page-narrow">
            <h1>{{ $title }}</h1>
            @if(!empty($lead))
                <p class="lead">{{ $lead }}</p>
            @endif

            @foreach($sections as $section)
                <div class="page-section">
                    <h2>{{ $section[0] }}</h2>
                    <p>{{ $section[1] }}</p>
                </div>
            @endforeach

            <div class="page-cta">
                @if(!empty($showContact))
                    <a href="{{ route('pages.contact') }}" class="btn btn-primary">تماس با پشتیبانی</a>
                @endif
                <a href="{{ route('pages.faq') }}" class="btn btn-secondary">سوالات متداول</a>
            </div>
        </div>
    </div>
@endsection
