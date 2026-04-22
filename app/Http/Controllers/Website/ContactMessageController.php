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
        $messages = [
            'required' => __('home.validation.required'),
            'string' => __('home.validation.string'),
            'email' => __('home.validation.email'),
            'max.string' => __('home.validation.max_string'),
            'min.string' => __('home.validation.min_string'),
        ];

        $attributes = [
            'name' => __('home.contact.form.name'),
            'email' => __('home.contact.form.email'),
            'phone' => __('home.contact.form.phone'),
            'subject' => __('home.contact.form.subject'),
            'message' => __('home.contact.form.message'),
        ];

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ], $messages, $attributes);

        if ($validator->fails()) {
            return redirect(route('home') . '#contact')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect(route('home') . '#contact')
            ->with('contact_success', __('home.contact.success'));
    }
}
