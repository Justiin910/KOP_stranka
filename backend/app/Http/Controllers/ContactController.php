<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            // use RFC + DNS checks for email format
            'email' => 'required|email:rfc,dns|max:100',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'name.required' => 'Vaše meno je povinné.',
            'name.max' => 'Meno nesmie presiahnuť 100 znakov.',
            'email.required' => 'Prosím, uveďte platnú e-mailovú adresu.',
            'email.email' => 'Formát e-mailovej adresy je neplatný alebo doména neexistuje.',
            'email.max' => 'E-mail nesmie presiahnuť 100 znakov.',
            'message.required' => 'Prosím, napíšte správu pred odoslaním.',
            'message.min' => 'Vaša správa musí obsahovať aspoň 10 znakov.',
            'message.max' => 'Vaša správa nesmie presiahnuť 5000 znakov.',
        ]);

        // Additional defensive DNS check (in case the email rule misses some edge cases)
        $email = $validated['email'] ?? null;
        if ($email) {
            $domain = substr(strrchr($email, '@'), 1);
            if ($domain) {
                $hasMx = function_exists('checkdnsrr') && checkdnsrr($domain, 'MX');
                if (!$hasMx) {
                    return response()->json([
                        'success' => false,
                        'message' => 'E-mailová doména sa javí ako neplatná alebo nemá MX záznamy.',
                    ], 422);
                }
            }
        }

        try {
            $recipientEmail = 'techstore99x@gmail.com';
            
            try {
                $htmlBody = view('emails.contact', [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'subject' => $validated['subject'],
                    'message' => $validated['message']
                ])->render();

                Mail::html($htmlBody, function ($message) use ($validated, $recipientEmail) {
                    $message->to($recipientEmail)
                        ->subject('Nová správa z kontaktného formulára: ' . ($validated['subject'] ?? 'Bez predmetu'))
                        ->from(config('mail.from.address'), 'TechStore Support')
                        ->replyTo($validated['email'], $validated['name']);
                });
                
                \Log::info('Contact form email sent successfully to ' . $recipientEmail);
            } catch (\Exception $mailError) {
                \Log::error('Mail sending error: ' . $mailError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Vaša správa bola úspešne odoslaná. Odpovieme vám do 24 hodín.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Pri spracovaní vašej správy došlo k chybe. Skúste prosím neskôr alebo nás kontaktujte priamo.'
            ], 500);
        }
    }
}




