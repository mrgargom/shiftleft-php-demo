<?php
/**
 * Database Seeder
 * Creates sample data for testing the application
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Models/User.php';
require_once __DIR__ . '/../../app/Models/Student.php';
require_once __DIR__ . '/../../app/Models/Advisor.php';
require_once __DIR__ . '/../../app/Models/Administrator.php';
require_once __DIR__ . '/../../app/Models/Availability.php';
require_once __DIR__ . '/../../app/Models/Appointment.php';

echo "🌱 Seeding database...\n\n";

$userModel = new User();
$studentModel = new Student();
$advisorModel = new Advisor();
$adminModel = new Administrator();
$availabilityModel = new Availability();
$appointmentModel = new Appointment();

// Create Administrator
echo "Creating administrator...\n";
$adminId = $adminModel->create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => 'password',
    'admin_id' => 'ADM001'
]);
echo "✓ Administrator created (admin@example.com / password)\n\n";

// Create Advisors
echo "Creating advisors...\n";
$advisors = [
    [
        'name' => 'Dr. Sarah Johnson',
        'email' => 'advisor@example.com',
        'password' => 'password',
        'advisor_id' => 'ADV001',
        'department' => 'Computer Science',
        'office_location' => 'Building A, Room 201',
        'phone_number' => '555-0101'
    ],
    [
        'name' => 'Prof. Michael Chen',
        'email' => 'michael.chen@example.com',
        'password' => 'password',
        'advisor_id' => 'ADV002',
        'department' => 'Engineering',
        'office_location' => 'Building B, Room 305',
        'phone_number' => '555-0102'
    ],
    [
        'name' => 'Dr. Emily Rodriguez',
        'email' => 'emily.rodriguez@example.com',
        'password' => 'password',
        'advisor_id' => 'ADV003',
        'department' => 'Business Administration',
        'office_location' => 'Building C, Room 150',
        'phone_number' => '555-0103'
    ]
];

$advisorIds = [];
foreach ($advisors as $advisor) {
    $id = $advisorModel->create($advisor);
    $advisorIds[] = $id;
    echo "✓ Created advisor: {$advisor['name']} ({$advisor['email']} / password)\n";
}
echo "\n";

// Create Students
echo "Creating students...\n";
$students = [
    [
        'name' => 'John Doe',
        'email' => 'student@example.com',
        'password' => 'password',
        'student_id' => 'STU001',
        'major' => 'Computer Science',
        'year_level' => 'Junior',
        'gpa' => 3.75,
        'phone' => '555-1001'
    ],
    [
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'password' => 'password',
        'student_id' => 'STU002',
        'major' => 'Engineering',
        'year_level' => 'Sophomore',
        'gpa' => 3.50,
        'phone' => '555-1002'
    ],
    [
        'name' => 'Alice Williams',
        'email' => 'alice.williams@example.com',
        'password' => 'password',
        'student_id' => 'STU003',
        'major' => 'Business Administration',
        'year_level' => 'Senior',
        'gpa' => 3.90,
        'phone' => '555-1003'
    ]
];

$studentIds = [];
foreach ($students as $student) {
    $id = $studentModel->create($student);
    $studentIds[] = $id;
    echo "✓ Created student: {$student['name']} ({$student['email']} / password)\n";
}
echo "\n";

// Create Availabilities for advisors
echo "Creating advisor availabilities...\n";
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$nextWeek = date('Y-m-d', strtotime('+7 days'));

foreach ($advisorIds as $advisorId) {
    // This week
    $availabilityModel->create([
        'advisor_id' => $advisorId,
        'date' => $today,
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'is_available' => 1
    ]);
    
    $availabilityModel->create([
        'advisor_id' => $advisorId,
        'date' => $today,
        'start_time' => '14:00:00',
        'end_time' => '17:00:00',
        'is_available' => 1
    ]);
    
    // Tomorrow
    $availabilityModel->create([
        'advisor_id' => $advisorId,
        'date' => $tomorrow,
        'start_time' => '10:00:00',
        'end_time' => '15:00:00',
        'is_available' => 1
    ]);
    
    // Next week
    $availabilityModel->create([
        'advisor_id' => $advisorId,
        'date' => $nextWeek,
        'start_time' => '09:00:00',
        'end_time' => '16:00:00',
        'is_available' => 1
    ]);
}
echo "✓ Created availabilities for all advisors\n\n";

// Create sample appointments
echo "Creating sample appointments...\n";
$appointments = [
    [
        'student_id' => $studentIds[0],
        'advisor_id' => $advisorIds[0],
        'date' => $tomorrow,
        'time' => '10:00:00',
        'duration' => 30,
        'purpose' => 'Course selection guidance',
        'notes' => 'Need help choosing courses for next semester',
        'status' => 'pending'
    ],
    [
        'student_id' => $studentIds[1],
        'advisor_id' => $advisorIds[1],
        'date' => $nextWeek,
        'time' => '11:00:00',
        'duration' => 45,
        'purpose' => 'Career counseling',
        'notes' => 'Discuss internship opportunities',
        'status' => 'confirmed'
    ],
    [
        'student_id' => $studentIds[2],
        'advisor_id' => $advisorIds[2],
        'date' => $tomorrow,
        'time' => '14:00:00',
        'duration' => 30,
        'purpose' => 'Graduation requirements review',
        'notes' => 'Review remaining requirements for graduation',
        'status' => 'confirmed'
    ]
];

foreach ($appointments as $appointment) {
    $result = $appointmentModel->create($appointment);
    if ($result['success']) {
        echo "✓ Created appointment for student ID {$appointment['student_id']}\n";
    }
}

echo "\n✅ Database seeding completed successfully!\n\n";
echo "You can now login with:\n";
echo "  Admin: admin@example.com / password\n";
echo "  Advisor: advisor@example.com / password\n";
echo "  Student: student@example.com / password\n";
