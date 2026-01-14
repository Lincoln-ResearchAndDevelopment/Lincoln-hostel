<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request {{ ucfirst($leaveRequest->status) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #cc0000 0%, #a30000 100%); padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">LincHostel</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0;">Leave Request Update</p>
    </div>
    
    <div style="background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 8px 8px;">
        @if($recipientType === 'student')
            <h2 style="color: {{ $leaveRequest->status == 'approved' ? '#28a745' : '#dc3545' }}; margin-top: 0;">
                Leave Request {{ ucfirst($leaveRequest->status) }}
            </h2>
            <p>Dear {{ $student->full_name }},</p>
        @else
            <h2 style="color: {{ $leaveRequest->status == 'approved' ? '#28a745' : '#dc3545' }}; margin-top: 0;">
                Leave Request Update for {{ $student->full_name }}
            </h2>
            <p>Dear {{ $student->parent_name ?? 'Parent/Guardian' }},</p>
        @endif

        @if($leaveRequest->status == 'approved')
            <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #155724;">
                    <strong>✓ Good News!</strong> The leave request has been <strong>APPROVED</strong>.
                </p>
            </div>
        @else
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #721c24;">
                    <strong>✗ Notice:</strong> The leave request has been <strong>REJECTED</strong>.
                </p>
            </div>
        @endif

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #cc0000; padding-bottom: 10px;">Leave Details</h3>
            
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
                    <td style="padding: 8px 0;">{{ ucfirst($leaveRequest->type) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Period:</td>
                    <td style="padding: 8px 0;">{{ $leaveRequest->start_date->format('M d') }} - {{ $leaveRequest->end_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Duration:</td>
                    <td style="padding: 8px 0;">{{ $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 }} day(s)</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Status:</td>
                    <td style="padding: 8px 0;">
                        <span style="background: {{ $leaveRequest->status == 'approved' ? '#28a745' : '#dc3545' }}; color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                            {{ ucfirst($leaveRequest->status) }}
                        </span>
                    </td>
                </tr>
            </table>

            @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <strong style="color: #dc3545;">Reason for Rejection:</strong>
                <p style="margin: 10px 0 0 0; background: white; padding: 10px; border-radius: 4px; border-left: 3px solid #dc3545;">
                    {{ $leaveRequest->rejection_reason }}
                </p>
            </div>
            @endif
        </div>

        @if($leaveRequest->status == 'approved')
            <p><strong>Important Notes:</strong></p>
            <ul style="color: #666;">
                <li>Please ensure to check out properly before leaving the hostel</li>
                <li>Return to the hostel on or before the approved end date</li>
                <li>Carry this approval notification for reference</li>
            </ul>
        @else
            <p>If you have any questions about this decision, please contact the hostel administration.</p>
        @endif

        @if($recipientType === 'student')
        <div style="text-align: center; margin-top: 25px;">
            <a href="{{ url('/student/leave') }}" style="background: #cc0000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">View My Leave Requests</a>
        </div>
        @endif

        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #666; font-size: 12px;">
            This is an automated message from LincHostel Management System.<br>
            Updated on: {{ now()->format('M d, Y \a\t h:i A') }}
        </p>
    </div>
</body>
</html>
