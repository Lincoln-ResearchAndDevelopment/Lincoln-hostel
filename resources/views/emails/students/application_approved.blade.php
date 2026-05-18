<x-mail::message>
# Congratulations! 🎉

Dear {{ $application->full_name }},

We are pleased to inform you that your hostel application (**{{ $application->application_number }}**) has been **Approved**!

You are now officially part of the LincHostel community. A student account has been created for you.

**How to Log In:**

Your Student Dashboard is accessible at:

<x-mail::button :url="route('student.login')">
Login to Student Dashboard
</x-mail::button>

**Your Login Credentials:**
- **Admission Number:** `{{ $application->student_id }}`
@php
$phone = $application->phone_number ?? '';
if (strlen($phone) > 7) {
    $maskedPhone = substr($phone, 0, 4) . str_repeat('*', strlen($phone) - 7) . substr($phone, -3);
} else {
    $maskedPhone = $phone;
}
@endphp
- **Contact Number:** `{{ $maskedPhone }}`

Use the **Admission Number** and the **Contact Number you registered with** to log in. For security, the full number is not displayed here.

**Next Steps:**
1. Log in to the Student Dashboard using your credentials above.
2. Browse available hostels and rooms.
3. Book your preferred room and upload your payment receipt.

> **Need Help?** Contact us at [lincolnuninigeria@gmail.com](mailto:lincolnuninigeria@gmail.com)

Thanks,<br>
{{ config('app.name') }} Admissions Team
</x-mail::message>
