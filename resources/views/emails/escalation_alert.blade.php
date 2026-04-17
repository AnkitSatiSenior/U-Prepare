<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: {{ $action['type'] === 'alert' ? '#dc3545' : '#ffc107' }}; color: {{ $action['type'] === 'alert' ? '#fff' : '#000' }}; padding: 15px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .box { background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; margin: 20px 0; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $action['type'] === 'alert' ? 'Urgent Compliance Alert' : 'Compliance Reminder #' . $action['count_so_far'] }}</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>This is an automated system notification. A compliance violation requires your immediate attention (Hierarchy Level {{ $action['level'] }}).</p>
            
            <div class="box">
                <strong>Project Details:</strong><br>
                Sub-Package: <strong>{{ $project->name }}</strong><br>
                Compliance Type: <strong>{{ $compliance->name }}</strong><br>
                Time Overdue: <strong style="color: red;">{{ $daysViolated }} Days</strong>
            </div>
            
            <p><strong>Reason:</strong> The 'Pre-Construction' phase is currently incomplete, but system records indicate that 'During Construction' entries have already begun.</p>
            
            <p>Please log in to the portal and ensure the necessary safeguard forms are completed immediately to resolve this violation flag.</p>
            
            <p>Regards,<br>System Escalation Engine</p>
        </div>
        
        <div class="footer">
            This is an automated notification. Please do not reply to this email.
        </div>
    </div>
</body>
</html>