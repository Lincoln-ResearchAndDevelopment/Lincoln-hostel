<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['Male', 'Female']);
        $firstName = $gender === 'Male' ? $this->faker->firstNameMale() : $this->faker->firstNameFemale();
        $lastName = $this->faker->lastName();
        $fullName = "$firstName $lastName";
        $dept = $this->faker->randomElement(['Computer Science', 'Business Administration', 'Mass Communication', 'Accounting']);
        $year = $this->faker->randomElement(['2024', '2025', '2026']);
        
        // Match real DB formats: numeric strings OR 'LUC-NGA-...'
        $format = $this->faker->randomElement(['numeric', 'structured']);
        if ($format === 'numeric') {
            $admissionNumber = '010' . $this->faker->unique()->numberBetween(10000000, 99999999);
        } else {
            $admissionNumber = 'LUC-NGA-' . str_pad($this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT) . '-ADM-' . $this->faker->unique()->numberBetween(100000, 999999);
        }

        return [
            'admission_number' => $admissionNumber,
            'full_name' => $fullName,
            'email' => strtolower($firstName . '.' . $lastName . '@lincoln.edu.ng'),
            'gender' => $gender,
            'date_of_birth' => $this->faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            'program' => $dept,
            'department' => '',
            'semester' => $this->faker->numberBetween(1, 8),
            'intake' => 'July 2026',
            'contact_number' => '0' . $this->faker->numberBetween(7000000000, 9999999999),
            'emergency_contact' => '0' . $this->faker->numberBetween(7000000000, 9999999999),
            'address' => $this->faker->address(),
            'parent_name' => $this->faker->name($gender),
            'parent_relationship' => 'Parent',
            'parent_phone' => '0' . $this->faker->numberBetween(7000000000, 9999999999),
            'hostel_fee_amount' => 0,
            'hostel_fee_paid' => 0,
            'hostel_fee_status' => 'unpaid',
            'status' => 'active',
            'password' => bcrypt('welcome123'),
        ];
    }
}
