<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

// Remove if already exists
Student::where('student_id', '2025-0001')->delete();

$s = Student::create([
    'student_id'           => '2025-0001',
    'last_name'            => 'Dela Cruz',
    'first_name'           => 'Juan',
    'middle_name'          => 'Santos',
    'birthdate'            => '2009-03-15',
    'gender'               => 'Male',
    'address'              => '123 Mabini Street',
    'barangay'             => 'Bagong Silang',
    'municipality'         => 'Caloocan City',
    'province'             => 'Metro Manila',
    'civil_status'         => 'Single',
    'religion'             => 'Roman Catholic',
    'nationality'          => 'Filipino',
    'contact_number'       => '09171234567',
    'email'                => 'juan.delacruz@email.com',
    'father_name'          => 'Pedro Dela Cruz',
    'father_occupation'    => 'Engineer',
    'father_contact'       => '09181234567',
    'mother_name'          => 'Maria Santos Dela Cruz',
    'mother_occupation'    => 'Teacher',
    'mother_contact'       => '09191234567',
    'last_school_attended' => 'Caloocan National High School',
    'school_type'          => 'Public',
    'password'             => Hash::make('delacruz'),
]);

$sy  = SchoolYear::where('is_active', true)->first();
$sec = Section::where('grade_level', 'Grade 8')->where('section_name', 'Section 1')->first();

Enrollment::create([
    'enrollment_no'   => 'ENR-20252026-00001',
    'student_id'      => $s->id,
    'school_year_id'  => $sy->id,
    'section_id'      => $sec?->id,
    'level_type'      => 'JHS',
    'grade_level'     => 'Grade 8',
    'enrollment_type' => 'Old',
    'status'          => 'Enrolled',
    'enrollment_date' => now()->toDateString(),
    'encoded_by'      => 1,
]);

echo "Done! Student ID: {$s->student_id}, Password: delacruz\n";
