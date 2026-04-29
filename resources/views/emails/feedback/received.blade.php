{{-- resources/views/emails/feedback/received.blade.php --}}
<x-mail::message>
# Hello {{ $feedback->name }},

Thank you for contacting us. We have received your {{ $feedback->type }}. 

**We will get back to you soon.**

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>