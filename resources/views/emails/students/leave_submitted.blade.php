<x-mail::message>
# Leave Request Submitted

@if($recipientType === 'admin')
# New Leave Request Notification
@elseif($recipientType === 'student')
# Leave Request Submission Confirmation
@else
# Your Ward's Leave Request Notification
@endif

Dear {{ $recipientType === 'admin' ? 'Administrator' : ($recipientType === 'student' ? $student->full_name : ($student->parent_name ?? 'Parent/Guardian')) }},

@if($recipientType === 'student')
Your leave request has been successfully submitted and is currently undergoing administrative review. Below are the details of your application:
@else
We wish to inform you that a new leave request has been submitted by **{{ $student->full_name }}**.
@endif

**Leave Request Summary:**
- **Student:** {{ $student->full_name }} ({{ $student->admission_number }})
- **Type:** {{ ucfirst($leaveRequest->type) }}
- **Dates:** {{ $leaveRequest->start_date->format('M d') }} to {{ $leaveRequest->end_date->format('M d, Y') }}
- **Reason:** {{ $leaveRequest->reason }}
- **Destination:** {{ $leaveRequest->destination ?: 'Not Specified' }}

@if($recipientType === 'admin')
Please review this request in the admin dashboard to approve or reject it.

<x-mail::button :url="url('/admin/leave/' . $leaveRequest->id)">
Review Request
</x-mail::button>
@elseif($recipientType === 'student')
The hostel administration is currently reviewing your request. You will be notified via email and in-app alert immediately upon status change.
@else
The hostel administration is currently reviewing this request. You will receive another notification once a decision has been made.
@endif

Thanks,<br>
{{ config('app.name') }} Management
</x-mail::message>
