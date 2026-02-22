<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        if ($validator->fails()) {
            return redirect(route('home') . '#contact')
                ->withErrors($validator)
                ->withInput();
        }

        ContactMessage::create([
            'name' => $validator->validated()['name'],
            'email' => $validator->validated()['email'],
            'phone' => $validator->validated()['phone'] ?? null,
            'subject' => $validator->validated()['subject'],
            'message' => $validator->validated()['message'],
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect(route('home') . '#contact')
            ->with('contact_success', 'Your message has been sent successfully. Our team will contact you soon.');
    }
}
