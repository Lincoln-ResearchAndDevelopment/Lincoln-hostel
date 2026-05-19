<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Student;

class StudentLoginTest extends TestCase
{
    public function test_student_can_login()
    {
        // 1. Get the first student
        $student = Student::first();
        
        $this->assertNotNull($student, 'No students found in DB');
        
        echo "Testing with admission_number: " . $student->admission_number . "\n";
        
        // 2. Make a request to the login endpoint
        $response = $this->post('/student/login', [
            'admission_number' => $student->admission_number,
            'contact_number' => $student->contact_number,
        ]);
        
        // 3. See what happened
        echo "Response Status: " . $response->getStatusCode() . "\n";
        
        if ($response->isRedirect()) {
            echo "Redirected to: " . $response->headers->get('Location') . "\n";
        } else {
            echo "Not a redirect. Response content:\n";
            echo substr($response->getContent(), 0, 500) . "...\n";
        }
        
        // 4. Check if authenticated
        if ($this->isAuthenticated('student')) {
            echo "SUCCESS: Authenticated as student!\n";
        } else {
            echo "FAIL: Not authenticated as student.\n";
            
            // Print session errors if any
            $errors = session('errors');
            if ($errors) {
                echo "Session Errors:\n";
                print_r($errors->all());
            }
        }
    }
}
