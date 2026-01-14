<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Submitted</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #cc0000 0%, #a30000 100%); padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">LincHostel</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0;">Leave Request Notification</p>
    </div>
    
    <div style="background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 8px 8px;">
        @if($recipientType === 'admin')
            <h2 style="color: #cc0000; margin-top: 0;">New Leave Request Submitted</h2>
            <p>A new leave request has been submitted and requires your attention.</p>
        @else
            <h2 style="color: #cc0000; margin-top: 0;">Leave Request Notification</h2>
            <p>Dear {{ $student->parent_name ?? 'Parent/Guardian' }},</p>
            <p>This is to inform you that your ward, <strong>{{ $student->full_name }}</strong>, has submitted a leave request.</p>
        @endif

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #cc0000; padding-bottom: 10px;">Leave Request Details</h3>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 40%;">Student Name:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Admission Number:</td>
                    <td style="padding: 8px 0;">{{ $student->admission_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Leave Type:</td>
                    <td style="padding: 8px 0;">
                        <span style="background: {{ $leaveRequest->type == 'medical' ? '#17a2b8' : ($leaveRequest->type == 'home' ? '#28a745' : '#6c757d') }}; color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                            {{ ucfirst($leaveRequest->type) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Start Date:</td>
                    <td style="padding: 8px 0;">{{ $leaveRequest->start_date->format('l, M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">End Date:</td>
                    <td style="padding: 8px 0;">{{ $leaveRequest->end_date->format('l, M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Duration:</td>
                    <td style="padding: 8px 0;">{{ $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 }} day(s)</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Status:</td>
                    <td style="padding: 8px 0;">
                        <span style="background: #ffc107; color: #000; padding: 3px 10px; border-radius: 12px; font-size: 12px;">Pending</span>
                    </td>
                </tr>
            </table>

            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <strong style="color: #666;">Reason:</strong>
                <p style="margin: 10px 0 0 0; background: white; padding: 10px; border-radius: 4px;">{{ $leaveRequest->reason }}</p>
            </div>
        </div>

        @if($recipientType === 'admin')
            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ url('/admin/leave/' . $leaveRequest->id) }}" style="background: #cc0000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Review Request</a>
            </div>
        @else
            <p style="margin-top: 20px;">The hostel administration will review this request and notify you of the decision.</p>
            <p>If you have any concerns about this leave request, please contact the hostel administration immediately.</p>
        @endif

        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #666; font-size: 12px;">
            This is an automated message from LincHostel Management System.<br>
            Submitted on: {{ $leaveRequest->created_at->format('M d, Y \a\t h:i A') }}
        </p>
    </div>
</body>
</html>
