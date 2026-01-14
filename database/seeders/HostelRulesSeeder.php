<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HostelRule;

class HostelRulesSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            // General Rules
            [
                'title' => 'Room Allocation',
                'description' => 'Students must occupy only the room and bed assigned to them. Unauthorized room changes are strictly prohibited.',
                'category' => 'general',
                'order' => 1,
            ],
            [
                'title' => 'Identity Card',
                'description' => 'Students must carry their hostel ID card at all times within the hostel premises and present it when requested by hostel staff.',
                'category' => 'general',
                'order' => 2,
            ],
            [
                'title' => 'Registration',
                'description' => 'All students must complete the hostel registration process before occupying their rooms.',
                'category' => 'general',
                'order' => 3,
            ],

            // Safety Rules
            [
                'title' => 'Fire Safety',
                'description' => 'Students must familiarize themselves with fire exits and assembly points. Fire drills must be taken seriously. Do not tamper with fire safety equipment.',
                'category' => 'safety',
                'order' => 1,
            ],
            [
                'title' => 'Electrical Safety',
                'description' => 'Do not overload electrical sockets. Report any faulty electrical equipment immediately. Personal high-wattage appliances are not permitted.',
                'category' => 'safety',
                'order' => 2,
            ],
            [
                'title' => 'Emergency Procedures',
                'description' => 'In case of emergency, contact the porter\'s lodge immediately. Emergency contact numbers are displayed on notice boards.',
                'category' => 'safety',
                'order' => 3,
            ],

            // Conduct Rules
            [
                'title' => 'Noise Levels',
                'description' => 'Maintain reasonable noise levels at all times. Quiet hours are from 10:00 PM to 7:00 AM. Loud music and disturbances are prohibited.',
                'category' => 'conduct',
                'order' => 1,
            ],
            [
                'title' => 'Respect for Others',
                'description' => 'Treat fellow residents and staff with respect. Harassment, bullying, or discrimination of any kind will not be tolerated.',
                'category' => 'conduct',
                'order' => 2,
            ],
            [
                'title' => 'Dress Code',
                'description' => 'Dress appropriately in common areas. Indecent exposure is prohibited.',
                'category' => 'conduct',
                'order' => 3,
            ],

            // Facilities Rules
            [
                'title' => 'Common Areas',
                'description' => 'Keep common areas clean and tidy. Return furniture to its original position after use. Report any damages immediately.',
                'category' => 'facilities',
                'order' => 1,
            ],
            [
                'title' => 'Kitchen Usage',
                'description' => 'Clean up after using the kitchen. Do not leave food unattended while cooking. Dispose of waste properly.',
                'category' => 'facilities',
                'order' => 2,
            ],
            [
                'title' => 'Laundry Facilities',
                'description' => 'Use laundry facilities during designated hours only. Remove clothes promptly after washing.',
                'category' => 'facilities',
                'order' => 3,
            ],

            // Visitor Rules
            [
                'title' => 'Visitor Registration',
                'description' => 'All visitors must be registered at the porter\'s lodge. Visitors must present valid identification.',
                'category' => 'visitors',
                'order' => 1,
            ],
            [
                'title' => 'Visiting Hours',
                'description' => 'Visitors are allowed between 8:00 AM and 8:00 PM only. Overnight visitors are strictly prohibited.',
                'category' => 'visitors',
                'order' => 2,
            ],
            [
                'title' => 'Visitor Responsibility',
                'description' => 'Students are responsible for the conduct of their visitors. Visitors must be accompanied at all times.',
                'category' => 'visitors',
                'order' => 3,
            ],

            // Curfew Rules
            [
                'title' => 'Curfew Hours',
                'description' => 'All students must be inside the hostel by 10:00 PM on weekdays and 11:00 PM on weekends.',
                'category' => 'curfew',
                'order' => 1,
            ],
            [
                'title' => 'Late Entry',
                'description' => 'Students returning after curfew must sign the late entry register. Repeated violations will result in disciplinary action.',
                'category' => 'curfew',
                'order' => 2,
            ],
            [
                'title' => 'Overnight Leave',
                'description' => 'Students planning to stay out overnight must submit a leave request at least 24 hours in advance.',
                'category' => 'curfew',
                'order' => 3,
            ],

            // Cleanliness Rules
            [
                'title' => 'Room Cleanliness',
                'description' => 'Keep your room clean and tidy at all times. Room inspections may be conducted periodically.',
                'category' => 'cleanliness',
                'order' => 1,
            ],
            [
                'title' => 'Waste Disposal',
                'description' => 'Dispose of waste in designated bins only. Separate recyclable materials where applicable.',
                'category' => 'cleanliness',
                'order' => 2,
            ],
            [
                'title' => 'Bathroom Hygiene',
                'description' => 'Keep bathrooms clean after use. Report any plumbing issues immediately.',
                'category' => 'cleanliness',
                'order' => 3,
            ],

            // Prohibited Items
            [
                'title' => 'Alcohol and Drugs',
                'description' => 'Possession, consumption, or distribution of alcohol and illegal drugs is strictly prohibited.',
                'category' => 'prohibited',
                'order' => 1,
            ],
            [
                'title' => 'Weapons',
                'description' => 'Possession of weapons of any kind, including knives and firearms, is strictly prohibited.',
                'category' => 'prohibited',
                'order' => 2,
            ],
            [
                'title' => 'Pets',
                'description' => 'Pets are not allowed in the hostel premises.',
                'category' => 'prohibited',
                'order' => 3,
            ],
            [
                'title' => 'Cooking Appliances',
                'description' => 'Personal cooking appliances such as hot plates, electric kettles, and rice cookers are not allowed in rooms.',
                'category' => 'prohibited',
                'order' => 4,
            ],
        ];

        foreach ($rules as $rule) {
            HostelRule::create(array_merge($rule, [
                'is_active' => true,
                'hostel_id' => null, // Global rule - applies to all hostels
            ]));
        }
    }
}
