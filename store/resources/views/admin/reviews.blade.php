@extends('appshell::layouts.private')

@section('title')
    نظرات محصولات
@stop

@section('content')
    <div class="container-fluid">

        <div class="card mb-4">
            <div class="card-header">در انتظار تأیید ({{ $pending->count() }})</div>
            <div class="card-body p-0">
                @if($pending->isEmpty())
                    <p class="p-3 mb-0 text-muted">نظری در انتظار تأیید نیست.</p>
                @else
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>محصول</th>
                                <th>نویسنده</th>
                                <th>امتیاز</th>
                                <th style="width:45%">متن نظر</th>
                                <th>تاریخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending as $review)
                                <tr>
                                    <td>{{ $review->product?->name ?? '—' }}</td>
                                    <td>{{ $review->name }}</td>
                                    <td>{{ str_repeat('★', $review->rating) }}</td>
                                    <td>{{ Str::limit($review->comment, 120) }}</td>
                                    <td>{{ $review->created_at->format('Y/m/d') }}</td>
                                    <td class="text-left">
                                        <form action="{{ route('admin.custom.reviews.approve', $review) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success">تأیید و انتشار</button>
                                        </form>
                                        <form action="{{ route('admin.custom.reviews.destroy', $review) }}" method="post" class="d-inline"
                                              onsubmit="return confirm('این نظر حذف شود؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">نظرات منتشرشده (آخرین ۵۰ مورد)</div>
            <div class="card-body p-0">
                @if($approved->isEmpty())
                    <p class="p-3 mb-0 text-muted">هنوز نظری منتشر نشده است.</p>
                @else
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>محصول</th>
                                <th>نویسنده</th>
                                <th>امتیاز</th>
                                <th style="width:45%">متن نظر</th>
                                <th>تاریخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approved as $review)
                                <tr>
                                    <td>{{ $review->product?->name ?? '—' }}</td>
                                    <td>{{ $review->name }}</td>
                                    <td>{{ str_repeat('★', $review->rating) }}</td>
                                    <td>{{ Str::limit($review->comment, 120) }}</td>
                                    <td>{{ $review->created_at->format('Y/m/d') }}</td>
                                    <td class="text-left">
                                        <form action="{{ route('admin.custom.reviews.destroy', $review) }}" method="post"
                                              onsubmit="return confirm('این نظر حذف شود؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
