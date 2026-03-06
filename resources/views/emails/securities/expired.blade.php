<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Expiration Notice</title>
    <style>
        /* Base resets for email client compatibility */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding-bottom: 60px;
        }
        .main {
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 40px;
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .content {
            padding: 32px 24px;
        }
        .alert {
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 15px;
            border-left: 4px solid;
        }
        /* Reminder State (Warning UI) */
        .alert-reminder {
            background-color: #fffbeb;
            border-color: #f59e0b;
            color: #b45309;
        }
        /* First Notice State (Danger UI) */
        .alert-danger {
            background-color: #fef2f2;
            border-color: #ef4444;
            color: #b91c1c;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            background-color: #f8fafc;
            border-radius: 6px;
        }
        .data-table th, .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 14px;
        }
        .data-table th {
            width: 40%;
            color: #64748b;
            font-weight: 500;
        }
        .data-table td {
            color: #0f172a;
            font-weight: 600;
        }
        .text-danger {
            color: #dc2626;
        }
        .footer {
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <div class="main">
            <div class="header">
                <h2>Contract Security Status</h2>
            </div>
            
            <div class="content">
                @if($isReminder)
                    <div class="alert alert-reminder">
                        ⚠️ REMINDER: Follow-up regarding an expired contract security.
                    </div>
                @else
                    <div class="alert alert-danger">
                        🚨 ACTION REQUIRED: A contract security has expired.
                    </div>
                @endif

                <p>Dear {{ $userName }},</p>

                <p>Please be advised that the following contract security requires immediate attention as its validity period has elapsed.</p>

                <table class="data-table">
                    <tr>
                        <th>Contract Number</th>
                        <td>{{ $contractNumber }}</td>
                    </tr>
                    <tr>
                        <th>Security Type</th>
                        <td>{{ $securityName }}</td>
                    </tr>
                    <tr>
                        <th>Expiration Date</th>
                        <td class="text-danger">{{ $endDate }}</td>
                    </tr>
                </table>

                <p style="margin-top: 24px; font-size: 14px; color: #475569;">
                    Please access the system to review this contract and take the necessary steps to renew or process the security documentation.
                </p>
            </div>

            <div class="footer">
                <p style="margin: 0;">This is an automated notification from the Contract Management System.</p>
                <p style="margin: 4px 0 0 0;">Please do not reply directly to this email.</p>
            </div>
        </div>
    </center>
</body>
</html>