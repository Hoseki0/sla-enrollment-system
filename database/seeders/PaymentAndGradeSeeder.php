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
        $coreSubjects = [
            ['ENG11', 'Oral Communication', 1],
            ['FIL11', 'Komunikasyon at Pananaliksik', 1],
            ['MATH11', 'General Mathematics', 1],
            ['SCI11', 'Earth and Life Science', 1],
            ['PE11', 'Physical Education and Health', 1],
            ['ENG12', 'English for Academic Purposes', 1],
            ['FIL12', 'Pagbasa at Pagsusuri', 1],
            ['MATH12', 'Statistics and Probability', 1],
            ['PE12', 'Physical Education and Health', 1],
        ];

        $strandSubjects = [
            'STEM' => [
                ['STEM11-C1', 'Pre-Calculus', 1, 'Grade 11'],
                ['STEM11-C2', 'Basic Calculus', 1, 'Grade 11'],
                ['STEM11-C3', 'General Chemistry', 1, 'Grade 11'],
                ['STEM11-C4', 'General Physics', 1, 'Grade 11'],
                ['STEM12-C1', 'Pre-Calculus', 1, 'Grade 12'],
                ['STEM12-C2', 'Basic Calculus', 1, 'Grade 12'],
                ['STEM12-C3', 'General Chemistry 2', 1, 'Grade 12'],
                ['STEM12-C4', 'General Physics 2', 1, 'Grade 12'],
            ],
            'ABM' => [
                ['ABM11-C1', 'Applied Economics', 1, 'Grade 11'],
                ['ABM11-C2', 'Business Mathematics', 1, 'Grade 11'],
                ['ABM11-C3', 'Business Finance', 1, 'Grade 11'],
                ['ABM12-C1', 'Business Ethics', 1, 'Grade 12'],
                ['ABM12-C2', 'Principles of Marketing', 1, 'Grade 12'],
            ],
            'HUMSS' => [
                ['HUM11-C1', 'Creative Writing', 1, 'Grade 11'],
                ['HUM11-C2', 'Introduction to World Religions', 1, 'Grade 11'],
                ['HUM12-C1', 'Creative Nonfiction', 1, 'Grade 12'],
                ['HUM12-C2', 'Trends, Networks, and Critical Thinking', 1, 'Grade 12'],
            ],
            'GAS' => [
                ['GAS11-C1', 'Organization and Management', 1, 'Grade 11'],
                ['GAS11-C2', 'Disaster Readiness and Risk Reduction', 1, 'Grade 11'],
                ['GAS12-C1', 'Community Engagement', 1, 'Grade 12'],
            ],
        ];

        foreach ($coreSubjects as [$code, $name, $sem]) {
            $grade = str_contains($code, '12') ? 'Grade 12' : 'Grade 11';
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

        foreach (['Grade 11', 'Grade 12'] as $grade) {
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
