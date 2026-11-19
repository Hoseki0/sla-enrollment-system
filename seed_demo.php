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
$enr8 = Enrollment::create([
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

// Q1 paid for Grade 8, rest pending
$fees8 = FeeSchedule::where('school_year_id', $sy->id)->where('grade_level', 'Grade 8')->get();
foreach ($fees8 as $fee) {
    QuarterlyPayment::create([
        'payment_ref'     => QuarterlyPayment::generatePaymentRef(),
        'student_id'      => $g8->id,
        'enrollment_id'   => $enr8->id,
        'school_year_id'  => $sy->id,
        'fee_schedule_id' => $fee->id,
        'quarter'         => $fee->quarter,
        'amount'          => $fee->amount,
        'payment_method'  => $fee->quarter === 1 ? 'gcash' : null,
        'status'          => $fee->quarter === 1 ? 'paid' : 'pending',
        'gateway_ref'     => $fee->quarter === 1 ? 'DEMO-GCASH-JHS-001' : null,
        'paid_at'         => $fee->quarter === 1 ? now()->subDays(10) : null,
    ]);
}

// Sample grades for Grade 8 Q1
$subjects8 = Subject::forEnrollment($enr8)->get();
$grades8 = [89, 91, 87, 90, 88, 92, 86, 93];
foreach ($subjects8->take(8) as $i => $subject) {
    GradeEntry::create([
        'student_id'     => $g8->id,
        'enrollment_id'  => $enr8->id,
        'subject_id'     => $subject->id,
        'school_year_id' => $sy->id,
        'quarter'        => 1,
        'semester'       => null,
        'grade'          => $grades8[$i] ?? 88,
        'status'         => 'published',
        'encoded_by'     => 1,
    ]);
}
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
    'semester'        => '1st Semester',
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
    'semester'        => '1st Semester',
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

// ── Grade 7 Student (Enrolled) ───────────────────────────────
Student::where('student_id', '2025-0004')->delete();
$g7 = Student::create([
    'student_id'           => '2025-0004',
    'lrn'                  => '456789012345',
    'last_name'            => 'Garcia',
    'first_name'           => 'Ana',
    'middle_name'          => 'Villanueva',
    'birthdate'            => '2012-05-14',
    'gender'               => 'Female',
    'address'              => '12 Luna Street',
    'barangay'             => 'Centro',
    'municipality'         => 'Santa Lucia',
    'province'             => 'Isabela',
    'civil_status'         => 'Single',
    'religion'             => 'Roman Catholic',
    'nationality'          => 'Filipino',
    'contact_number'       => '09172345678',
    'email'                => 'ana.garcia@email.com',
    'father_name'          => 'Ramon Garcia',
    'mother_name'          => 'Lorna Villanueva Garcia',
    'last_school_attended' => 'Santa Lucia Elementary School',
    'school_type'          => 'Public',
    'password'             => Hash::make('garcia2025'),
]);
$sec7 = Section::where('grade_level', 'Grade 7')->where('section_name', 'Section 1')->first();
$enr7 = Enrollment::create([
    'enrollment_no'   => 'ENR-20252026-00004',
    'student_id'      => $g7->id,
    'school_year_id'  => $sy->id,
    'section_id'      => $sec7?->id,
    'level_type'      => 'JHS',
    'grade_level'     => 'Grade 7',
    'enrollment_type' => 'New',
    'status'          => 'Enrolled',
    'enrollment_date' => now()->subDays(3)->toDateString(),
    'encoded_by'      => 1,
]);
foreach (FeeSchedule::where('school_year_id', $sy->id)->where('grade_level', 'Grade 7')->get() as $fee) {
    QuarterlyPayment::create([
        'payment_ref'     => QuarterlyPayment::generatePaymentRef(),
        'student_id'      => $g7->id,
        'enrollment_id'   => $enr7->id,
        'school_year_id'  => $sy->id,
        'fee_schedule_id' => $fee->id,
        'quarter'         => $fee->quarter,
        'amount'          => $fee->amount,
        'payment_method'  => $fee->quarter === 1 ? 'gcash' : null,
        'status'          => $fee->quarter === 1 ? 'paid' : 'pending',
        'gateway_ref'     => $fee->quarter === 1 ? 'DEMO-GCASH-G7-001' : null,
        'paid_at'         => $fee->quarter === 1 ? now()->subDays(2) : null,
    ]);
}
foreach (Subject::forEnrollment($enr7)->get()->take(8) as $i => $subject) {
    GradeEntry::create([
        'student_id'     => $g7->id,
        'enrollment_id'  => $enr7->id,
        'subject_id'     => $subject->id,
        'school_year_id' => $sy->id,
        'quarter'        => 1,
        'semester'       => null,
        'grade'          => 86 + ($i % 7),
        'status'         => 'published',
        'encoded_by'     => 1,
    ]);
}
echo "✓ Grade 7 student: LRN 456789012345 / password: garcia2025\n";

// ── Grade 9 Student (Enrolled) ───────────────────────────────
Student::where('student_id', '2025-0005')->delete();
$g9 = Student::create([
    'student_id'           => '2025-0005',
    'lrn'                  => '567890123456',
    'last_name'            => 'Mendoza',
    'first_name'           => 'Miguel',
    'middle_name'          => 'Torres',
    'birthdate'            => '2010-09-20',
    'gender'               => 'Male',
    'address'              => '89 Del Pilar Street',
    'barangay'             => 'San Isidro',
    'municipality'         => 'Santa Lucia',
    'province'             => 'Isabela',
    'civil_status'         => 'Single',
    'religion'             => 'Roman Catholic',
    'nationality'          => 'Filipino',
    'contact_number'       => '09183456789',
    'email'                => 'miguel.mendoza@email.com',
    'father_name'          => 'Jose Mendoza',
    'mother_name'          => 'Teresa Torres Mendoza',
    'last_school_attended' => 'Sta. Lucia Academy',
    'school_type'          => 'Private',
    'password'             => Hash::make('mendoza2025'),
]);
$sec9 = Section::where('grade_level', 'Grade 9')->where('section_name', 'Section 1')->first();
$enr9 = Enrollment::create([
    'enrollment_no'   => 'ENR-20252026-00005',
    'student_id'      => $g9->id,
    'school_year_id'  => $sy->id,
    'section_id'      => $sec9?->id,
    'level_type'      => 'JHS',
    'grade_level'     => 'Grade 9',
    'enrollment_type' => 'Old',
    'status'          => 'Enrolled',
    'enrollment_date' => now()->subDays(4)->toDateString(),
    'encoded_by'      => 1,
]);
foreach (FeeSchedule::where('school_year_id', $sy->id)->where('grade_level', 'Grade 9')->get() as $fee) {
    QuarterlyPayment::create([
        'payment_ref'     => QuarterlyPayment::generatePaymentRef(),
        'student_id'      => $g9->id,
        'enrollment_id'   => $enr9->id,
        'school_year_id'  => $sy->id,
        'fee_schedule_id' => $fee->id,
        'quarter'         => $fee->quarter,
        'amount'          => $fee->amount,
        'payment_method'  => null,
        'status'          => 'pending',
    ]);
}
echo "✓ Grade 9 student: LRN 567890123456 / password: mendoza2025\n";

// ── Grade 11 HUMSS Student (Pending Approval) ────────────────
Student::where('student_id', '2025-0006')->delete();
$g11h = Student::create([
    'student_id'           => '2025-0006',
    'lrn'                  => '678901234567',
    'last_name'            => 'Bautista',
    'first_name'           => 'Sofia',
    'middle_name'          => 'Ramos',
    'birthdate'            => '2008-02-11',
    'gender'               => 'Female',
    'address'              => '34 Burgos Street',
    'barangay'             => 'Poblacion',
    'municipality'         => 'Santa Lucia',
    'province'             => 'Isabela',
    'civil_status'         => 'Single',
    'religion'             => 'Roman Catholic',
    'nationality'          => 'Filipino',
    'contact_number'       => '09194567890',
    'email'                => 'sofia.bautista@email.com',
    'father_name'          => 'Eduardo Bautista',
    'mother_name'          => 'Clarissa Ramos Bautista',
    'last_school_attended' => 'Santa Lucia National High School',
    'school_type'          => 'Public',
    'password'             => Hash::make('bautista2025'),
]);
$sec11h = Section::where('grade_level', 'Grade 11')->where('strand', 'HUMSS')->where('section_name', 'Section 1')->first();
Enrollment::create([
    'enrollment_no'   => 'ENR-20252026-00006',
    'student_id'      => $g11h->id,
    'school_year_id'  => $sy->id,
    'section_id'      => $sec11h?->id,
    'level_type'      => 'SHS',
    'grade_level'     => 'Grade 11',
    'track'           => 'Academic',
    'strand'          => 'HUMSS',
    'semester'        => '1st Semester',
    'enrollment_type' => 'Transferee',
    'status'          => 'Pending',
    'enrollment_date' => now()->toDateString(),
    'encoded_by'      => 1,
]);
echo "✓ Grade 11 HUMSS (Pending) student: LRN 678901234567 / password: bautista2025\n";

// ── Sample Announcements ─────────────────────────────────────
\App\Models\Announcement::firstOrCreate(
    ['title' => 'SY 2025-2026 Enrollment Now Open'],
    [
        'admin_id'     => 1,
        'body'         => 'Welcome to Sta. Lucia Academy! Online enrollment and quarterly tuition payment via GCash/Maya are now open for Junior and Senior High School.',
        'audience'     => 'all',
        'priority'     => 'important',
        'is_published' => true,
        'published_at' => now()->subDays(7),
    ]
);
\App\Models\Announcement::firstOrCreate(
    ['title' => 'Quarter 1 Examination Schedule Released'],
    [
        'admin_id'     => 1,
        'body'         => 'Please check the Examinations tab for your Quarter 1 schedule and room assignments. Ensure Q1 tuition is settled before exam day.',
        'audience'     => 'all',
        'priority'     => 'urgent',
        'is_published' => true,
        'published_at' => now()->subDays(2),
    ]
);
\App\Models\Announcement::firstOrCreate(
    ['title' => 'SHS Strand Orientation for Grade 11'],
    [
        'admin_id'     => 1,
        'body'         => 'All incoming Grade 11 STEM, ABM, HUMSS, and GAS students are invited to the orientation at the SLA Gymnasium.',
        'audience'     => 'shs',
        'priority'     => 'normal',
        'is_published' => true,
        'published_at' => now()->subDays(1),
    ]
);
echo "✓ Sample announcements seeded.\n";

// ── Sample Document Requests ─────────────────────────────────
\App\Models\DocumentRequest::firstOrCreate(
    ['request_no' => 'DOC-20252026-0001'],
    [
        'student_id'    => $g8->id,
        'enrollment_id' => $enr8->id,
        'document_type' => 'Enrollment Certificate',
        'purpose'       => 'Scholarship application requirement',
        'copies'        => 1,
        'status'        => 'ready',
        'handled_by'    => 1,
        'ready_at'      => now()->subDay(),
    ]
);
\App\Models\DocumentRequest::firstOrCreate(
    ['request_no' => 'DOC-20252026-0002'],
    [
        'student_id'    => $g11->id,
        'enrollment_id' => $enr11->id,
        'document_type' => 'Good Moral Certificate',
        'purpose'       => 'Academic competition requirement',
        'copies'        => 1,
        'status'        => 'pending',
    ]
);
echo "✓ Sample document requests seeded.\n";

// ── Sample Parent Accounts & Attendance Sheet ────────────────
$parent1 = \App\Models\ParentUser::updateOrCreate(
    ['email' => 'pedro.delacruz@email.com'],
    [
        'name'           => 'Pedro Dela Cruz',
        'contact_number' => '09171112222',
        'relationship'   => 'Father',
        'password'       => Hash::make('parent123'),
    ]
);
$parent1->students()->syncWithoutDetaching([$g8->id => ['relationship' => 'Father']]);

$parent2 = \App\Models\ParentUser::updateOrCreate(
    ['email' => 'roberto.reyes@email.com'],
    [
        'name'           => 'Roberto Reyes',
        'contact_number' => '09182223333',
        'relationship'   => 'Father',
        'password'       => Hash::make('parent123'),
    ]
);
$parent2->students()->syncWithoutDetaching([$g11->id => ['relationship' => 'Father']]);

$sampleStatuses = ['Present', 'Present', 'Present', 'Late', 'Present', 'Present', 'Excused', 'Present', 'Absent', 'Present'];
foreach ([[$g8, $enr8], [$g11, $enr11], [$g12, $enr12], [$g7, $enr7]] as [$stu, $enr]) {
    for ($d = 0; $d < 10; $d++) {
        $date = now()->subDays($d)->toDateString();
        $st   = $sampleStatuses[$d];
        \App\Models\Attendance::updateOrCreate(
            ['student_id' => $stu->id, 'attendance_date' => $date],
            [
                'enrollment_id'  => $enr->id,
                'school_year_id' => $sy->id,
                'status'         => $st,
                'time_in'        => $st === 'Present' ? '07:25 AM' : ($st === 'Late' ? '08:10 AM' : null),
                'remarks'        => $st === 'Excused' ? 'Medical checkup' : ($st === 'Late' ? 'Heavy traffic' : null),
                'recorded_by'    => 1,
            ]
        );
    }
}
echo "✓ Parent accounts & 10-day attendance sheets seeded.\n";

echo "\n=== Test Accounts ===\n";
echo "Grade 7  — LRN: 456789012345 | Password: garcia2025  (Enrolled, Q1 paid)\n";
echo "Grade 8  — LRN: 123456789012 | Password: delacruz    (Enrolled, Q1 paid, grades seeded)\n";
echo "Grade 9  — LRN: 567890123456 | Password: mendoza2025 (Enrolled, pending fees)\n";
echo "Grade 11 — LRN: 234567890123 | Password: reyes2025   (STEM, Enrolled, Q1 paid, grades seeded)\n";
echo "Grade 11 — LRN: 678901234567 | Password: bautista2025 (HUMSS, Pending approval)\n";
echo "Grade 12 — LRN: 345678901234 | Password: santos2025  (ABM, Enrolled, Q1+Q2 paid, grades seeded)\n";
echo "Parent 1 — pedro.delacruz@email.com | Password: parent123 (Connected to Juan Dela Cruz - Grade 8)\n";
echo "Parent 2 — roberto.reyes@email.com  | Password: parent123 (Connected to Maria Reyes - Grade 11)\n";
echo "Admin    — admin@stalucia.edu.ph | Password: Admin@123\n";
echo "Teacher  — teacher@stalucia.edu.ph | Password: Teacher@123\n";
echo "\nDone!\n";
