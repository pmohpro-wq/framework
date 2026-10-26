<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactMessage;
use Konekt\AppShell\Http\Controllers\BaseController;

class MessageAdminController extends BaseController
{
    public function index()
    {
        return view('admin.messages', [
            'messages' => ContactMessage::orderByDesc('created_at')->take(100)->get(),
        ]);
    }

    public function markRead(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return back()->with('success', __('پیام خوانده‌شده علامت‌گذاری شد.'));
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return back()->with('success', __('پیام حذف شد.'));
    }
}
