<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSubmitted;
class ContactController extends Controller
{
    public function store(Request $request)
    {
        // 🔒 honeypot check
        if ($request->filled('website')) {
            return response()->json(['success' => false], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string',
        ]);

        $contact = Contact::create($validated);

        try {
            $to = config('mail.to.address', env('CONTACT_MAIL_TO', 'safetechcomge@gmail.com'));
            Mail::to($to)->queue(new ContactSubmitted($contact));
        } catch (\Throwable $e) {
            Log::warning('Contact email queue failed', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
        ]);
    }


}
