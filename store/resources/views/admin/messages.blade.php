@extends('appshell::layouts.private')

@section('title')
    پیام‌های مشتریان
@stop

@section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-body p-0">
                @if($messages->isEmpty())
                    <p class="p-3 mb-0 text-muted">پیامی ثبت نشده است.</p>
                @else
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:16%">فرستنده</th>
                                <th style="width:20%">ایمیل</th>
                                <th style="width:18%">موضوع</th>
                                <th style="width:28%">متن پیام</th>
                                <th>تاریخ</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr class="{{ $message->is_read ? '' : 'fw-bold' }}">
                                    <td>{{ $message->name }}</td>
                                    <td dir="ltr">{{ $message->email }}</td>
                                    <td>{{ $message->subject ?? '—' }}</td>
                                    <td>{{ Str::limit($message->message, 100) }}</td>
                                    <td>{{ $message->created_at->format('Y/m/d H:i') }}</td>
                                    <td>
                                        {{ $message->is_read ? 'خوانده‌شده' : 'جدید' }}
                                    </td>
                                    <td class="text-start">
                                        @unless($message->is_read)
                                            <form action="{{ route('admin.custom.messages.read', $message) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-secondary">علامت‌گذاری خوانده‌شده</button>
                                            </form>
                                        @endunless
                                        <form action="{{ route('admin.custom.messages.destroy', $message) }}" method="post" class="d-inline"
                                              onsubmit="return confirm('این پیام حذف شود؟')">
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
