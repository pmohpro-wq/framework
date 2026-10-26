@extends('layouts.store')

@section('title', 'سوالات متداول — فروشگاه من')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <span>سوالات متداول</span>
        </div>

        <div class="page page-narrow">
            <h1>سوالات متداول</h1>
            <p class="lead">پاسخ پرتکرارترین سوالات مشتریان؛ اگر پاسخ سوال خود را نیافتید از <a href="{{ route('pages.contact') }}" style="color:var(--primary)">صفحهٔ تماس با ما</a> بپرسید.</p>

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
