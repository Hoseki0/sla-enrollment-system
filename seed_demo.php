<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\FeeSchedule;
use App\Models\QuarterlyPayment;
use App\Models\Subject;
use App\Models\GradeEntry;
use Illuminate\Support\Facades\Hash;

echo "=== SLA Enrollment Demo Seeder ===\n\n";

// Run payment/grade seeder first
$seeder = new \Database\Seeders\PaymentAndGradeSeeder();
$seeder->run();
echo "✓ Fee schedules, subjects, and examinations seeded.\n";

$sy = SchoolYear::where('is_active', true)->first();

// ── Grade 8 Student (existing demo) ──────────────────────────
Student::where('student_id', '2025-0001')->delete();

$g8 = Student::create([
    'student_id'           => '2025-0001',
    'lrn'                  => '123456789012',
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
    'mother_name'          => 'Maria Santos Dela Cruz',
    'last_school_attended' => 'Caloocan National High School',
    'school_type'          => 'Public',
    'password'             => Hash::make('delacruz'),
]);

$sec8 = Section::where('grade_level', 'Grade 8')->where('section_name', 'Section 1')->first();
Enrollment::create([
    'enrollment_no'   => 'ENR-20252026-00001',
    'student_id'      => $g8->id,
    'school_year_id'  => $sy->id,
    'section_id'      => $sec8?->id,
    'level_type'      => 'JHS',
    'grade_level'     => 'Grade 8',
    'enrollment_type' => 'Old',
    'status'          => 'Enrolled',
    'enrollment_date' => now()->toDateString(),
    'encoded_by'      => 1,
]);
echo "✓ Grade 8 student: LRN 123456789012 / password: delacruz\n";

// ── Grade 11 Incoming Student (STEM) ─────────────────────────
Student::where('student_id', '2025-0002')->delete();

$g11 = Student::create([
    'student_id'           => '2025-0002',
    'lrn'                  => '234567890123',
    'last_name'            => 'Reyes',
    'first_name'           => 'Maria',
    'middle_name'          => 'Lopez',
    'birthdate'            => '2008-07-22',
    'gender'               => 'Female',
    'address'              => '45 Rizal Avenue',
    'barangay'             => 'Poblacion',
    'municipality'         => 'Santa Lucia',
    'province'             => 'Isabela',
    'civil_status'         => 'Single',
    'religion'             => 'Roman Catholic',
    'nationality'          => 'Filipino',
    'contact_number'       => '09181234567',
    'email'                => 'maria.reyes@email.com',
    'father_name'          => 'Roberto Reyes',
    'mother_name'          => 'Carmen Lopez Reyes',
    'last_school_attended' => 'Santa Lucia National High School',
    'school_type'          => 'Public',
    'password'             => Hash::make('reyes2025'),
]);

$sec11 = Section::where('grade_level', 'Grade 11')->where('strand', 'STEM')->where('section_name', 'Section 1')->first();
$enr11 = Enrollment::create([
    'enrollment_no'   => 'ENR-20252026-00002',
    'student_id'      => $g11->id,
    'school_year_id'  => $sy->id,
    'section_id'      => $sec11?->id,
    'level_type'      => 'SHS',
    'grade_level'     => 'Grade 11',
    'track'           => 'Academic',
    'strand'          => 'STEM',
    'semester'        => 1,
    'enrollment_type' => 'New',
    'status'          => 'Enrolled',
    'enrollment_date' => now()->toDateString(),
    'encoded_by'      => 1,
]);

// Q1 paid for Grade 11, rest pending
$fees11 = FeeSchedule::where('school_year_id', $sy->id)->where('grade_level', 'Grade 11')->get();
foreach ($fees11 as $fee) {
    QuarterlyPayment::create([
        'payment_ref'     => QuarterlyPayment::generatePaymentRef(),
        'student_id'      => $g11->id,
        'enrollment_id'   => $enr11->id,
        'school_year_id'  => $sy->id,
        'fee_schedule_id' => $fee->id,
        'quarter'         => $fee->quarter,
        'amount'          => $fee->amount,
        'payment_method'  => $fee->quarter === 1 ? 'gcash' : null,
        'status'          => $fee->quarter === 1 ? 'paid' : 'pending',
        'gateway_ref'     => $fee->quarter === 1 ? 'DEMO-GCASH-001' : null,
        'paid_at'         => $fee->quarter === 1 ? now()->subDays(5) : null,
    ]);
}

// Sample grades for Grade 11 Q1
$subjects11 = Subject::forEnrollment($enr11)->get();
$sampleGrades = [92, 88, 85, 90, 87, 91, 86, 89, 93];
foreach ($subjects11->take(9) as $i => $subject) {
    GradeEntry::create([
        'student_id'     => $g11->id,
        'enrollment_id'  => $enr11->id,
        'subject_id'     => $subject->id,
        'school_year_id' => $sy->id,
        'quarter'        => 1,
        'semester'       => 1,
        'grade'          => $sampleGrades[$i] ?? 85,
        'status'         => 'published',
        'encoded_by'     => 1,
    ]);
}
echo "✓ Grade 11 STEM student: LRN 234567890123 / password: reyes2025 (Q1 paid, grades seeded)\n";

// ── Grade 12 Incoming Student (ABM) ──────────────────────────
Student::where('student_id', '2025-0003')->delete();

$g12 = Student::create([
    'student_id'           => '2025-0003',
    'lrn'                  => '345678901234',
    'last_name'            => 'Santos',
    'first_name'           => 'Carlos',
    'middle_name'          => 'Garcia',
    'birthdate'            => '2007-11-08',
    'gender'               => 'Male',
    'address'              => '78 Bonifacio Street',
    'barangay'             => 'San Jose',
    'municipality'         => 'Santa Lucia',
    'province'             => 'Isabela',
    'civil_status'         => 'Single',
    'religion'             => 'Iglesia ni Cristo',
    'nationality'          => 'Filipino',
    'contact_number'       => '09191234567',
    'email'                => 'carlos.santos@email.com',
    'father_name'          => 'Miguel Santos',
    'mother_name'          => 'Elena Garcia Santos',
    'last_school_attended' => 'Sta. Lucia Academy',
    'school_type'          => 'Private',
    'password'             => Hash::make('santos2025'),
]);

$sec12 = Section::where('grade_level', 'Grade 12')->where('strand', 'ABM')->where('section_name', 'Section 1')->first();
$enr12 = Enrollment::create([
    'enrollment_no'   => 'ENR-20252026-00003',
    'student_id'      => $g12->id,
    'school_year_id'  => $sy->id,
    'section_id'      => $sec12?->id,
    'level_type'      => 'SHS',
    'grade_level'     => 'Grade 12',
    'track'           => 'Academic',
    'strand'          => 'ABM',
    'semester'        => 1,
    'enrollment_type' => 'Old',
    'status'          => 'Enrolled',
    'enrollment_date' => now()->toDateString(),
    'encoded_by'      => 1,
]);

// Q1 and Q2 paid for Grade 12
$fees12 = FeeSchedule::where('school_year_id', $sy->id)->where('grade_level', 'Grade 12')->get();
foreach ($fees12 as $fee) {
    $isPaid = in_array($fee->quarter, [1, 2]);
    QuarterlyPayment::create([
        'payment_ref'     => QuarterlyPayment::generatePaymentRef(),
        'student_id'      => $g12->id,
        'enrollment_id'   => $enr12->id,
        'school_year_id'  => $sy->id,
        'fee_schedule_id' => $fee->id,
        'quarter'         => $fee->quarter,
        'amount'          => $fee->amount,
        'payment_method'  => $isPaid ? ($fee->quarter === 1 ? 'gcash' : 'maya') : null,
        'status'          => $isPaid ? 'paid' : 'pending',
        'gateway_ref'     => $isPaid ? 'DEMO-' . strtoupper($fee->quarter === 1 ? 'GCASH' : 'MAYA') . '-00' . $fee->quarter : null,
        'paid_at'         => $isPaid ? now()->subDays(30 * (3 - $fee->quarter)) : null,
    ]);
}

// Sample grades for Grade 12 Q1 and Q2
$subjects12 = Subject::forEnrollment($enr12)->get();
$gradesQ1 = [90, 87, 92, 88, 85, 91, 89];
$gradesQ2 = [88, 90, 86, 91, 87, 93, 88];
foreach ($subjects12->take(7) as $i => $subject) {
    foreach ([1, 2] as $q) {
        $grades = $q === 1 ? $gradesQ1 : $gradesQ2;
        GradeEntry::create([
            'student_id'     => $g12->id,
            'enrollment_id'  => $enr12->id,
            'subject_id'     => $subject->id,
            'school_year_id' => $sy->id,
            'quarter'        => $q,
            'semester'       => 1,
            'grade'          => $grades[$i] ?? 85,
            'status'         => 'published',
            'encoded_by'     => 1,
        ]);
    }
}
echo "✓ Grade 12 ABM student: LRN 345678901234 / password: santos2025 (Q1+Q2 paid, grades seeded)\n";

echo "\n=== Test Accounts ===\n";
echo "Grade 8  — LRN: 123456789012 | Password: delacruz\n";
echo "Grade 11 — LRN: 234567890123 | Password: reyes2025  (STEM, Q1 paid)\n";
echo "Grade 12 — LRN: 345678901234 | Password: santos2025  (ABM, Q1+Q2 paid)\n";
echo "Admin    — admin@stalucia.edu.ph | Password: Admin@123\n";
echo "\nDone!\n";
