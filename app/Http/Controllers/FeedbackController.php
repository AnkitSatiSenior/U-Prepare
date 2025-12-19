<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Store new feedback (Public).
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name'    => 'required|string|max:100',
        'email'   => 'required|email|max:150',
        'type'    => 'required|in:inquiry,feedback,others',
        'subject' => 'nullable|string|max:200',
        'message' => 'required|string',
    ]);

    $validated['ip_address'] = $request->ip();

    Feedback::create($validated);

    return redirect()->back()->with('success', 'Thank you! Your feedback has been submitted.');
}


}
