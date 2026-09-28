<?php

// app/Http/Requests/StoreFeedbackRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:150'],
            // Indian 10-digit format: Starts with 6-9, followed by 9 digits
            'phone_number' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            'type'         => ['required', 'in:inquiry,feedback,others'],
            'subject'      => ['nullable', 'string', 'max:200'],
            'message'      => ['required', 'string'],
            'h-captcha-response' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $secret = config('services.hcaptcha.secret');

                    if (! is_string($secret) || $secret === '') {
                        $fail('CAPTCHA verification is not configured. Please try again later.');

                        return;
                    }

                    try {
                        $response = Http::asForm()
                            ->timeout(5)
                            ->post('https://api.hcaptcha.com/siteverify', [
                                'secret' => $secret,
                                'response' => $value,
                                'remoteip' => $this->ip(),
                            ]);
                    } catch (ConnectionException) {
                        $fail('Unable to verify CAPTCHA right now. Please try again.');

                        return;
                    }

                    $hostname = config('services.hcaptcha.hostname');
                    $validHostname = ! is_string($hostname)
                        || $hostname === ''
                        || $response->json('hostname') === $hostname;

                    if (! $response->successful() || $response->json('success') !== true || ! $validHostname) {
                        $fail('Please complete the CAPTCHA verification.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Please enter a valid 10-digit Indian mobile number.',
            'h-captcha-response.required' => 'Please complete the CAPTCHA challenge.',
        ];
    }
}
