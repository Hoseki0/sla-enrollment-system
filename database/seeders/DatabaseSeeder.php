<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admins
        Admin::firstOrCreate(['email' => 'admin@stalucia.edu.ph'], [
            'name'     => 'System Administrator',
            'password' => Hash::make('Admin@123'),
            'role'     => 'super_admin',
        ]);
        Admin::firstOrCreate(['email' => 'registrar@stalucia.edu.ph'], [
            'name'     => 'Registrar Office',
            'password' => Hash::make('Admin@123'),
            'role'     => 'registrar',
        ]);

        // School Years
        SchoolYear::firstOrCreate(['year_label' => '2024-2025'], ['is_active' => false]);
        $sy = SchoolYear::firstOrCreate(['year_label' => '2025-2026'], ['is_active' => true]);

        // JHS Sections
        $jhsGrades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
        foreach ($jhsGrades as $grade) {
            foreach (['Section 1', 'Section 2'] as $sec) {
                Section::firstOrCreate([
                    'section_name'   => $sec,
                    'grade_level'    => $grade,
                    'school_year_id' => $sy->id,
                ], ['max_capacity' => 40]);
            }
        }

        // SHS Sections
        $shsStrands = [
            ['Grade 11', 'Academic', 'STEM'],
            ['Grade 11', 'Academic', 'ABM'],
            ['Grade 11', 'Academic', 'HUMSS'],
            ['Grade 11', 'Academic', 'GAS'],
            ['Grade 12', 'Academic', 'STEM'],
            ['Grade 12', 'Academic', 'ABM'],
            ['Grade 12', 'Academic', 'HUMSS'],
            ['Grade 12', 'Academic', 'GAS'],
        ];
        foreach ($shsStrands as [$grade, $track, $strand]) {
            Section::firstOrCreate([
                'section_name'   => 'Section 1',
                'grade_level'    => $grade,
                'track'          => $track,
                'strand'         => $strand,
                'school_year_id' => $sy->id,
            ], ['max_capacity' => 40]);
            // STEM gets Section 2
            if ($strand === 'STEM') {
                Section::firstOrCreate([
                    'section_name'   => 'Section 2',
                    'grade_level'    => $grade,
                    'track'          => $track,
                    'strand'         => $strand,
                    'school_year_id' => $sy->id,
                ], ['max_capacity' => 40]);
            }
        }

        $this->call(PaymentAndGradeSeeder::class);
    }
}
