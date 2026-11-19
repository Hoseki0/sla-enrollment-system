<?php

namespace Database\Seeders;

use App\Models\Examination;
use App\Models\FeeSchedule;
use App\Models\GradeEntry;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class PaymentAndGradeSeeder extends Seeder
{
    public function run(): void
    {
        $sy = SchoolYear::where('is_active', true)->first();
        if (!$sy) {
            return;
        }

        $this->seedFeeSchedules($sy);
        $this->seedSubjects();
        $this->seedExaminations($sy);
    }

    protected function seedFeeSchedules(SchoolYear $sy): void
    {
        $fees = [
            'Grade 7'  => 500,
            'Grade 8'  => 500,
            'Grade 9'  => 500,
            'Grade 10' => 500,
            'Grade 11' => 500,
            'Grade 12' => 500,
        ];

        $dueDates = [
            1 => ['month' => 8,  'year_offset' => 0],
            2 => ['month' => 11, 'year_offset' => 0],
            3 => ['month' => 2,  'year_offset' => 1],
            4 => ['month' => 5,  'year_offset' => 1],
        ];

        $startYear = (int) explode('-', $sy->year_label)[0];

        foreach ($fees as $grade => $amount) {
            for ($q = 1; $q <= 4; $q++) {
                $due = $dueDates[$q];
                FeeSchedule::firstOrCreate(
                    ['school_year_id' => $sy->id, 'grade_level' => $grade, 'quarter' => $q],
                    [
                        'amount'      => $amount,
                        'due_date'    => sprintf('%d-%02d-15', $startYear + $due['year_offset'], $due['month']),
                        'description' => "Quarter {$q} tuition fee — {$grade}",
                    ]
                );
            }
        }
    }

    protected function seedSubjects(): void
    {
        // Junior High School (DepEd K-12 Grades 7-10)
        $jhsSubjects = [
            ['FIL',   'Filipino'],
            ['ENG',   'English'],
            ['MATH',  'Mathematics'],
            ['SCI',   'Science'],
            ['AP',    'Araling Panlipunan'],
            ['ESP',   'Edukasyon sa Pagpapakatao'],
            ['TLE',   'Technology and Livelihood Education'],
            ['MAPEH', 'MAPEH (Music, Arts, PE, and Health)'],
        ];

        foreach ([7, 8, 9, 10] as $g) {
            $grade = "Grade {$g}";
            foreach ($jhsSubjects as [$prefix, $name]) {
                Subject::firstOrCreate(
                    ['subject_code' => "{$prefix}{$g}", 'grade_level' => $grade, 'strand' => null, 'semester' => null],
                    ['subject_name' => "{$name} {$g}", 'units' => 1, 'is_active' => true]
                );
            }
        }

        // Senior High School Core & Applied Subjects (DepEd K-12 Grades 11-12)
        $coreSubjects = [
            // Grade 11 - 1st Semester
            ['ENG11',    'Oral Communication in Context', 1, 'Grade 11'],
            ['FIL11',    'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 1, 'Grade 11'],
            ['MATH11',   'General Mathematics', 1, 'Grade 11'],
            ['SCI11',    'Earth and Life Science', 1, 'Grade 11'],
            ['PERDEV11', 'Personal Development', 1, 'Grade 11'],
            ['PE11',     'Physical Education and Health 1', 1, 'Grade 11'],
            // Grade 11 - 2nd Semester
            ['RW11',     'Reading and Writing Skills', 2, 'Grade 11'],
            ['FIL11B',   "Pagbasa at Pagsusuri ng Iba't Ibang Teksto Tungo sa Pananaliksik", 2, 'Grade 11'],
            ['STAT11',   'Statistics and Probability', 2, 'Grade 11'],
            ['PSCI11',   'Physical Science', 2, 'Grade 11'],
            ['LIT11',    '21st Century Literature from the Philippines and the World', 2, 'Grade 11'],
            ['PE11B',    'Physical Education and Health 2', 2, 'Grade 11'],
            // Grade 12 - 1st Semester
            ['ENG12',    'English for Academic and Professional Purposes', 1, 'Grade 12'],
            ['FIL12',    'Pagsulat sa Filipino sa Piling Larangan', 1, 'Grade 12'],
            ['CPAR12',   'Contemporary Philippine Arts from the Regions', 1, 'Grade 12'],
            ['MIL12',    'Media and Information Literacy', 1, 'Grade 12'],
            ['PHILO12',  'Introduction to the Philosophy of the Human Person', 1, 'Grade 12'],
            ['PR1-12',   'Practical Research 1', 1, 'Grade 12'],
            ['PE12',     'Physical Education and Health 3', 1, 'Grade 12'],
            // Grade 12 - 2nd Semester
            ['PR2-12',   'Practical Research 2', 2, 'Grade 12'],
            ['ENTREP12', 'Entrepreneurship', 2, 'Grade 12'],
            ['EMPTECH',  'Empowerment Technologies', 2, 'Grade 12'],
            ['III12',    'Inquiries, Investigations, and Immersion', 2, 'Grade 12'],
            ['PE12B',    'Physical Education and Health 4', 2, 'Grade 12'],
        ];

        $strandSubjects = [
            'STEM' => [
                ['STEM11-C1', 'Pre-Calculus', 1, 'Grade 11'],
                ['STEM11-C2', 'General Chemistry 1', 1, 'Grade 11'],
                ['STEM11-C3', 'Basic Calculus', 2, 'Grade 11'],
                ['STEM11-C4', 'General Biology 1', 2, 'Grade 11'],
                ['STEM12-C1', 'General Physics 1', 1, 'Grade 12'],
                ['STEM12-C2', 'General Biology 2', 1, 'Grade 12'],
                ['STEM12-C3', 'General Chemistry 2', 2, 'Grade 12'],
                ['STEM12-C4', 'General Physics 2', 2, 'Grade 12'],
            ],
            'ABM' => [
                ['ABM11-C1', 'Applied Economics', 1, 'Grade 11'],
                ['ABM11-C2', 'Business Mathematics', 1, 'Grade 11'],
                ['ABM11-C3', 'Organization and Management', 2, 'Grade 11'],
                ['ABM11-C4', 'Fundamentals of Accountancy, Business and Management 1', 2, 'Grade 11'],
                ['ABM12-C1', 'Fundamentals of Accountancy, Business and Management 2', 1, 'Grade 12'],
                ['ABM12-C2', 'Business Finance', 1, 'Grade 12'],
                ['ABM12-C3', 'Business Ethics and Social Responsibility', 2, 'Grade 12'],
                ['ABM12-C4', 'Principles of Marketing', 2, 'Grade 12'],
            ],
            'HUMSS' => [
                ['HUM11-C1', 'Creative Writing', 1, 'Grade 11'],
                ['HUM11-C2', 'Introduction to World Religions and Belief Systems', 1, 'Grade 11'],
                ['HUM11-C3', 'Disciplines and Ideas in the Social Sciences', 2, 'Grade 11'],
                ['HUM11-C4', 'Philippine Politics and Governance', 2, 'Grade 11'],
                ['HUM12-C1', 'Creative Nonfiction', 1, 'Grade 12'],
                ['HUM12-C2', 'Disciplines and Ideas in the Applied Social Sciences', 1, 'Grade 12'],
                ['HUM12-C3', 'Trends, Networks, and Critical Thinking in the 21st Century', 2, 'Grade 12'],
                ['HUM12-C4', 'Community Engagement, Solidarity, and Citizenship', 2, 'Grade 12'],
            ],
            'GAS' => [
                ['GAS11-C1', 'Humanities 1', 1, 'Grade 11'],
                ['GAS11-C2', 'Organization and Management', 1, 'Grade 11'],
                ['GAS11-C3', 'Disaster Readiness and Risk Reduction', 2, 'Grade 11'],
                ['GAS11-C4', 'Applied Economics', 2, 'Grade 11'],
                ['GAS12-C1', 'Social Science 1', 1, 'Grade 12'],
                ['GAS12-C2', 'Philippine Politics and Governance', 1, 'Grade 12'],
                ['GAS12-C3', 'Community Engagement, Solidarity, and Citizenship', 2, 'Grade 12'],
                ['GAS12-C4', 'Elective: Creative Writing', 2, 'Grade 12'],
            ],
        ];

        foreach ($coreSubjects as [$code, $name, $sem, $grade]) {
            Subject::firstOrCreate(
                ['subject_code' => $code, 'grade_level' => $grade, 'strand' => null, 'semester' => $sem],
                ['subject_name' => $name, 'units' => 1, 'is_active' => true]
            );
        }

        foreach ($strandSubjects as $strand => $subjects) {
            foreach ($subjects as [$code, $name, $sem, $grade]) {
                Subject::firstOrCreate(
                    ['subject_code' => $code, 'grade_level' => $grade, 'strand' => $strand, 'semester' => $sem],
                    ['subject_name' => $name, 'units' => 1, 'is_active' => true]
                );
            }
        }
    }

    protected function seedExaminations(SchoolYear $sy): void
    {
        $examDates = [
            1 => '2025-10-15',
            2 => '2026-01-20',
            3 => '2026-03-18',
            4 => '2026-05-22',
        ];

        foreach (['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $grade) {
            for ($q = 1; $q <= 4; $q++) {
                Examination::firstOrCreate(
                    ['school_year_id' => $sy->id, 'grade_level' => $grade, 'quarter' => $q],
                    [
                        'title'        => "Quarter {$q} Examination — {$grade}",
                        'exam_date'    => $examDates[$q],
                        'start_time'   => '08:00:00',
                        'end_time'     => '12:00:00',
                        'venue'        => 'SLA Main Building',
                        'instructions' => 'Bring school ID and permit. Payment for this quarter must be settled before the exam date.',
                        'is_active'    => true,
                    ]
                );
            }
        }
    }
}
