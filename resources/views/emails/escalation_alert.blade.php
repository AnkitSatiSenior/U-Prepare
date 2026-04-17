<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body        { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
        .wrapper    { max-width: 640px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header     { padding: 22px 30px; text-align: center; }
        .header.alert    { background: #dc3545; color: #fff; }
        .header.reminder { background: #ffc107; color: #212529; }
        .header h2  { margin: 0; font-size: 20px; }
        .header p   { margin: 4px 0 0; font-size: 13px; opacity: .85; }
        .category-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: .5px;
            background: rgba(255,255,255,.25);
        }
        .content    { padding: 28px 30px; }
        .info-box   { background: #f8f9fa; border-left: 4px solid #0d6efd; border-radius: 4px; padding: 16px 20px; margin: 18px 0; }
        .info-box p { margin: 5px 0; font-size: 14px; }
        .info-box strong { min-width: 130px; display: inline-block; }
        .overdue    { color: #dc3545; font-weight: bold; font-size: 16px; }
        .reason-box { background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px; padding: 14px 20px; margin: 18px 0; font-size: 14px; }
        .cta        { text-align: center; margin: 24px 0 10px; }
        .cta a      { background: #0d6efd; color: #fff; padding: 12px 28px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .footer     { font-size: 11px; color: #999; text-align: center; padding: 16px 30px; border-top: 1px solid #eee; }
        .level-pill { display: inline-block; background: #6c757d; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 12px; margin-left: 4px; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- ── HEADER ─────────────────────────────────────────────────────── --}}
    <div class="header {{ $isAlert ? 'alert' : 'reminder' }}">
        <div class="category-badge">{{ strtoupper($categoryLabel) }}</div>
        <h2>
            @if($isAlert)
                🚨 Urgent Alert: {{ $categoryLabel }} Violation
            @else
                ⏰ Reminder #{{ $action['count_so_far'] }}: {{ $categoryLabel }} Violation
            @endif
        </h2>
        <p>Hierarchy Level <span class="level-pill">{{ $action['level'] }}</span></p>
    </div>

    {{-- ── BODY ──────────────────────────────────────────────────────── --}}
    <div class="content">
        <p>Dear <strong>{{ $user->name }}</strong>,</p>
        <p>
            This is an automated system notification requiring your attention
            as a Level {{ $action['level'] }} stakeholder.
        </p>

        {{-- Info box --}}
        <div class="info-box">
            <p><strong>Category:</strong> {{ $categoryLabel }}</p>
            <p><strong>Project / Item:</strong> {{ $entityName }}</p>

            @if($compliance)
                <p><strong>Compliance Type:</strong> {{ $compliance->name }}</p>
            @endif

            <p><strong>Time Overdue:</strong>
                <span class="overdue">{{ $daysViolated }} {{ Str::plural('day', $daysViolated) }}</span>
            </p>
        </div>

        {{-- Reason / action description --}}
        <div class="reason-box">
            <strong>⚠️ Reason:</strong>
            @switch($category)
                @case('social_safeguard')
                    The <em>Pre-Construction</em> safeguard phase is currently incomplete,
                    but <em>During Construction</em> entries have already been recorded.
                    Please ensure all Pre-Construction compliance forms are completed.
                    @break
                @case('physical_progress')
                    No physical progress (BOQ / EPC) has been recorded for this sub-project
                    within the expected timeframe. Please submit the latest progress data
                    to the portal immediately.
                    @break
                @case('financial_progress')
                    No financial bill submission has been recorded within the expected
                    billing interval. Please ensure billing data is submitted to the portal
                    without further delay.
                    @break
                @case('contract_security')
                    A contract security certificate is <strong>near expiry or has already expired</strong>.
                    Please arrange for immediate renewal to maintain contract compliance.
                    @break
                @default
                    A compliance violation has been detected. Please take immediate action.
            @endswitch
        </div>

        <p>Please log in to the portal and take the necessary corrective action.</p>

        <div class="cta">
            <a href="{{ config('app.url') }}/admin/dashboard">Open Portal →</a>
        </div>
    </div>

    {{-- ── FOOTER ─────────────────────────────────────────────────────── --}}
    <div class="footer">
        This is an automated notification from the Escalation Engine.<br>
        Please do not reply to this email directly.
    </div>

</div>
</body>
</html>