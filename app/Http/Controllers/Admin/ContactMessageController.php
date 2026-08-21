<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    /**
     * Contact messages listing
     */
    public function index()
    {
        $messages = ContactMessage::latest()
            ->paginate(20);

        return view(
            'admin.contact-messages.index',
            compact('messages')
        );
    }


    /**
     * Show single contact message
     */
    public function show(ContactMessage $contactMessage)
    {
        if (!$contactMessage->is_read) {

            $contactMessage->update([
                'is_read' => true,
            ]);

        }

        return view(
            'admin.contact-messages.show',
            compact('contactMessage')
        );
    }


    /**
     * Delete message
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with(
                'success',
                'Contact message deleted successfully.'
            );
    }


    /**
     * Unread count
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => ContactMessage::where(
                'is_read',
                false
            )->count(),
        ]);
    }
}
