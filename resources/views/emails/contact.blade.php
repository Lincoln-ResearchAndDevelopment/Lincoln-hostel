<x-mail::message>
# New Contact Inquiry Received ✉️

You have received a new contact inquiry from the LincHostel public website.

<x-mail::panel>
**Inquiry Details:**
- **Sender Name:** {{ $details['name'] }}
- **Sender Email:** [{{ $details['email'] }}](mailto:{{ $details['email'] }})
- **Subject:** {{ $details['subject'] }}
</x-mail::panel>

### Message:
{{ $details['message'] }}

<x-mail::button :url="route('login')">
Go to Admin Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Automated Notification
</x-mail::message>
