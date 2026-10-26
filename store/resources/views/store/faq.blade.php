@extends('layouts.store')

@section('title', 'سوالات متداول — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => 'سوالات متداول']]])

        <div class="page page-narrow">
            <h1>سوالات متداول</h1>
            <p class="lead">پاسخ پرتکرارترین سوالات مشتریان؛ اگر پاسخ سوال خود را نیافتید از <a href="{{ route('pages.contact') }}" class="text-brand">صفحهٔ تماس با ما</a> بپرسید.</p>

            <div class="faq-list">
                @foreach($faqs as $i => [$question, $answer])
                    <details class="faq-item" {{ $i === 0 ? 'open' : '' }}>
                        <summary>
                            <span>{{ $question }}</span>
                            <span class="faq-arrow">▾</span>
                        </summary>
                        <p>{{ $answer }}</p>
                    </details>
                @endforeach
            </div>

            <div class="page-cta">
                <a href="{{ route('pages.contact') }}" class="btn btn-primary">پرسیدن سوال جدید</a>
                <a href="{{ route('track.show') }}" class="btn btn-secondary">پیگیری سفارش</a>
            </div>
        </div>
    </div>
@endsection
