<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Actions\Feedback\StoreFeedbackAction;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    public function store(StoreFeedbackRequest $request, StoreFeedbackAction $action): RedirectResponse
    {
        $action->execute(
            data: $request->validated(),
            ipAddress: $request->ip()
        );

        return redirect()->back()->with('success', 'Thank you! Your feedback has been submitted.');
    }
}