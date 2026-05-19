<x-mail::message>
# Welcome to Lincoln Hostel! 🎓🎉

Dear {{ $student->full_name }},

We are thrilled to welcome you to the LincHostel community! An official student account has been successfully created for you by the hostel administration.

You can now log in to the Student Portal to browse available hostels, request rooms, and manage your accommodation status.

<x-mail::button :url="route('student.login')">
Go to Student Login Portal
</x-mail::button>

### **Your Login Credentials:**
- **Admission Number:** `{{ $student->admission_number }}`
@php
$phone = $student->contact_number ?? '';
if (strlen($phone) > 7) {
    $maskedPhone = substr($phone, 0, 4) . str_repeat('*', strlen($phone) - 7) . substr($phone, -3);
} else {
    $maskedPhone = $phone;
}
@endphp
- **Registered Contact Number:** `{{ $maskedPhone }}`

Use your **Admission Number** and the **Mobile Phone Number you registered with** as your login credentials. For security reasons, the full phone number is partially masked above. Keep these details secure!

---

### **Getting Started is Easy:**
1. Click the button above to visit the **Student Login Portal**.
2. Enter your **Admission Number** and **Registered Contact Number**.
3. Once logged in, you can book a room and view payment details!

> **Need Help?** If you have any questions or experience issues logging in, please contact our support team at [lincolnuninigeria@gmail.com](mailto:lincolnuninigeria@gmail.com)

Best wishes for the academic session,<br>
{{ config('app.name') }} Admissions Team
</x-mail::message>
