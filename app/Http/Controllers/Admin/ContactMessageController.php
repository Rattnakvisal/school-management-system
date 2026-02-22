<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $messagesQuery = ContactMessage::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('subject', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%');
                });
            });

        if ($status === 'read') {
            $messagesQuery->where('is_read', true);
        } elseif ($status === 'unread') {
            $messagesQuery->where('is_read', false);
        }

        $messages = $messagesQuery
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $baseQuery = ContactMessage::query();
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'unread' => (clone $baseQuery)->where('is_read', false)->count(),
            'read' => (clone $baseQuery)->where('is_read', true)->count(),
        ];

        return view('admin.contact-messages', [
            'messages' => $messages,
            'search' => $search,
            'status' => in_array($status, ['all', 'read', 'unread'], true) ? $status : 'all',
            'stats' => $stats,
        ]);
    }

    public function markRead(ContactMessage $contactMessage)
    {
        if (!$contactMessage->is_read) {
            $contactMessage->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact message marked as read.');
    }

    public function markAllRead()
    {
        ContactMessage::query()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'All contact messages have been marked as read.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact message deleted successfully.');
    }
}
