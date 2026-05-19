<x-mail::message>
# Leave Request Update - Rejected ❌

@if($recipientType === 'student')
Dear **{{ $student->full_name }}**,
@else
Dear **{{ $student->parent_name ?? 'Parent/Guardian' }}**,
@endif

We wish to inform you that the leave request for **{{ $student->full_name }}** from **{{ $leaveRequest->start_date->format('M d') }}** to **{{ $leaveRequest->end_date->format('M d, Y') }}** has been **Rejected**.

**Reason for Rejection:**
> {{ $leaveRequest->rejection_reason }}

@if($recipientType === 'student')
If you have further questions or need to clarify details, please visit the hostel administration office.

<x-mail::button :url="url('/student/leave')">
View My Requests
</x-mail::button>
@else
We have notified the student of this decision. If you have further questions or need to clarify details, please reach out to the hostel administration.
@endif

Thanks,<br>
{{ config('app.name') }} Administration
</x-mail::message>
