{{-- نوار مراحل خرید — پارامتر: current = 1 (انتخاب کالا) | 2 (سبد خرید) | 3 (پرداخت) --}}
@php($stepLabels = ['انتخاب کالا', 'سبد خرید', 'پرداخت'])
<div class="steps">
    @foreach($stepLabels as $i => $label)
        @php($n = $i + 1)
        <div class="step {{ $n < $current ? 'done' : ($n === $current ? 'active' : '') }}">
            <div class="dot">{{ \App\Support\Persian::digits($n) }}</div>{{ $label }}
        </div>
        @if(! $loop->last)
            <div class="step-sep" aria-hidden="true"></div>
        @endif
    @endforeach
</div>
