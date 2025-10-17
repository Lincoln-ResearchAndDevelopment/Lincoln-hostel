<h2>New Hostel Application Received</h2>

<p><strong>Academic Year:</strong> {{ $applicationData['academic_year'] }}</p>
<p><strong>Passport:</strong> {{ $applicationData['passport_photo'] }}</p>
<p>
    <img src="{{ $message->embed(public_path(parse_url($applicationData['passport_photo'], PHP_URL_PATH))) }}" alt="Passport Photo" width="150" />
</p>

<p><strong>Application Form Payment Receipt (#2000):</strong> {{ $applicationData['applicationform_receipt'] }}</p>
<p>
    <img src="{{ $message->embed(public_path(parse_url($applicationData['applicationform_receipt'], PHP_URL_PATH))) }}" alt="Application Form Receipt" width="150" />
</p>

<p><strong>Hostel Fees Payment Receipt (#180,000 or #400,000):</strong> {{ $applicationData['hostelfee_receipt'] }}</p>
<p>
    <img src="{{ $message->embed(public_path(parse_url($applicationData['hostelfee_receipt'], PHP_URL_PATH))) }}" alt="Hostel Fee Receipt" width="150" />
</p>

<p><strong>Name:</strong> {{ $applicationData['name'] }}</p>
<p><strong>Registration Number:</strong> {{ $applicationData['reg_number'] }}</p>
<p><strong>Intake:</strong> {{ $applicationData['intake'] }}</p>
<p><strong>Program:</strong> {{ $applicationData['program'] }}</p>
<p><strong>Department:</strong> {{ $applicationData['department'] }}</p>
<p><strong>Medical Condition:</strong> {{ $applicationData['medical_condition'] }}</p>
<p><strong>Emergency Contact:</strong> {{ $applicationData['emergency_contact'] }}</p>
<p><strong>Declaration:</strong> I, {{ $applicationData['declaration_name'] }}, declare that the information provided above is true to the best of my knowledge. I agree to abide by the hostel rules and regulations set by the institute.</p>
<p><strong>Applicant Signature (Applicant Name):</strong> {{ $applicationData['applicant_signature'] }}</p>
<p><strong>Date of Signature:</strong> {{ $applicationData['date'] }}</p>
<p><strong>Guardian Signature (Guardian Name):</strong> {{ $applicationData['guardian_signature'] }}</p>
<p><strong>Guardian Date of Signature:</strong> {{ $applicationData['guardian_date'] }}</p>
<p><strong>Amount Paid:</strong> {{ $applicationData['amount_paid'] }}</p>
