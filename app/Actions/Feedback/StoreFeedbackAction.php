<?php

// app/Actions/Feedback/StoreFeedbackAction.php

namespace App\Actions\Feedback;

use App\Models\Feedback;
use App\Mail\FeedbackReceivedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StoreFeedbackAction
{
    public function execute(array $data, string $ipAddress): Feedback
    {
        $data['ip_address'] = $ipAddress;

        // 1. Store in Database
        $feedback = Feedback::create($data);

        // 2. Dispatch Email
        try {
            Mail::to($feedback->email)->send(new FeedbackReceivedMail($feedback));
        } catch (\Exception $e) {
            // Log the error but don't break the user experience
            Log::error('Failed to send feedback confirmation email: ' . $e->getMessage(), [
                'feedback_id' => $feedback->id
            ]);
        }

        return $feedback;
    }
}