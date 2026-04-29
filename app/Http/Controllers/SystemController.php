<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class SystemController extends Controller
{
    public function up(): Response
    {
        Artisan::call('up');

        return response(Artisan::output());
    }

    public function sendTestMail(): Response
    {
        $toEmail = 'yuvrajkohli8090ylt@gmail.com';

        Mail::raw('Hello! This is a test email from Laravel using Zoho SMTP.', function ($message) use ($toEmail) {
            $message->to($toEmail)->subject('Laravel Zoho SMTP Test Mail');
        });

        return response('Test email sent to ' . $toEmail);
    }

    public function linkStorage(): RedirectResponse
    {
        Artisan::call('storage:link');

        return back()->with('success', "Storage linked successfully! \n" . Artisan::output());
    }
}
