{{-- مسیر راهنمای صفحه — پارامتر: items = [['label' => ..., 'url' => اختیاری], ...] --}}
<nav class="breadcrumb" aria-label="مسیر صفحه">
    <a href="{{ route('home') }}">خانه</a>
    @foreach(($items ?? []) as $item)
        <span aria-hidden="true">/</span>
        @if(!empty($item['url']))
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
            <span>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
