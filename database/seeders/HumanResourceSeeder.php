<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class HumanResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('departments')->truncate();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('employees')->truncate();
        DB::table('tasks')->truncate();
        DB::table('salaries')->truncate();
        DB::table('presences')->truncate();
        DB::table('leave_requests')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('departments')->insert([
            [
                'name' => 'Human Resources',
                'description' => 'Manajemen SDM',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technology',
                'description' => 'Departemen IT & Tech',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales & Marketing',
                'description' => 'Pemasaran dan Penjualan',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance & Accounting',
                'description' => 'Keuangan dan Akuntansi',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operation',
                'description' => 'Operasional Bisnis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('roles')->insert([
            [
                'title' => 'HR Manager',
                'description' => 'Manajer HR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Developer',
                'description' => 'Pengembang Kode',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Sales',
                'description' => 'Penjualan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Accounting',
                'description' => 'Staf Keuangan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Supervisor',
                'description' => 'Supervisor Operasional',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $employeesData = [
            [
                'id' => 1,
                'user_name' => 'Asep HR',
                'user_email' => 'asephr@gmail.com',
                'role_id' => 1,
                'department_id' => 1,
                'fullname' => 'Asep Santoso',
                'status' => 'active',
                'salary' => 12500000.00
            ],
            [
                'id' => 2,
                'user_name' =>
                'Joko IT',
                'user_email' => 'jokoit@gmail.com',
                'role_id' => 2,
                'department_id' => 2,
                'fullname' => 'Joko Pramono',
                'status' => 'active',
                'salary' => 15000000.00
            ],
            [
                'id' => 3,
                'user_name' => 'Denis Sales',
                'user_email' => 'denissales@gmail.com',
                'role_id' => 3,
                'department_id' => 3,
                'fullname' => 'Denis Saputra',
                'status' => 'active',
                'salary' => 9500000.00
            ],
            [
                'id' => 4,
                'user_name' => 'Bobby Finance',
                'user_email' => 'bobbyfinance@gmail.com',
                'role_id' => 4,
                'department_id' => 4,
                'fullname' => 'Bobby Karta',
                'status' => 'active',
                'salary' => 8000000.00
            ],
            [
                'id' => 5,
                'user_name' => 'Udin Dev',
                'user_email' => 'udin.dev@gmail.com',
                'role_id' => 2,
                'department_id' => 2,
                'fullname' => 'Udin Wijaksono',
                'status' => 'inactive',
                'salary' => 11000000.00
            ],
        ];

        foreach ($employeesData as $data) {
            $now = now();
            DB::table('employees')->insert([
                'id' => $data['id'],
                'fullname' => $data['fullname'],
                'email' => $data['user_email'],
                'phone_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'birth_date' => $faker->dateTimeBetween('-40 years', '-25 years'),
                'hire_date' => $now->copy()->subMonths(rand(6, 60)),
                'department_id' => $data['department_id'],
                'role_id' => $data['role_id'],
                'status' => $data['status'],
                'salary' => $data['salary'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('users')->insert([
                'id' => $data['id'],
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'password' => Hash::make('password'),
                'employee_id' => $data['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $employeeIds = [1, 2, 3, 4, 5];
        $activeEmployeeIds = [1, 2, 3, 4];
        $numPresencesPerEmployee = 20;

        DB::table('tasks')->insert([
            [
                'title' => 'Persiapan Anggaran Q4',
                'description' => 'Siapkan dokumen final anggaran kuartal keempat.',
                'assigned_to' => 4,
                'due_date' => Carbon::parse('2025-10-20'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Migrasi Database Cloud',
                'description' => 'Pindahkan database dari server lokal ke cloud AWS.',
                'assigned_to' => 2,
                'due_date' => Carbon::parse('2025-10-15'),
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Riset Pasar Kompetitor',
                'description' => 'Lakukan analisis mendalam terhadap harga kompetitor utama.',
                'assigned_to' => 3,
                'due_date' => Carbon::parse('2025-10-25'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Audit Kehadiran Tahunan',
                'description' => 'Verifikasi data absensi seluruh karyawan selama tahun 2025.',
                'assigned_to' => 1,
                'due_date' => Carbon::parse('2025-10-20'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pelatihan Alat Baru',
                'description' => 'Pelatihan penggunaan alat manajemen proyek baru untuk tim Developer.',
                'assigned_to' => 2,
                'due_date' => Carbon::parse('2025-10-10'),
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        foreach ($employeeIds as $id) {
            $baseSalary = $employeesData[$id - 1]['salary'];
            $bonuses = $faker->randomFloat(2, 500000, 2000000);
            $deductions = $faker->randomFloat(2, 100000, 500000);
            $now = now();

            DB::table('salaries')->insert([
                'employee_id' => $id,
                'salary' => $baseSalary,
                'bonuses' => $bonuses,
                'deductions' => $deductions,
                'net_salary' => $baseSalary + $bonuses - $deductions,
                'pay_date' => Carbon::parse('2025-10-01'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $year = 2025;

        foreach ($employeeIds as $id) {
            if (in_array($id, $activeEmployeeIds)) {
                for ($j = 0; $j < $numPresencesPerEmployee; $j++) {
                    $randomMonth = rand(1, 12);
                    $daysInMonth = Carbon::create($year, $randomMonth, 1)->daysInMonth;
                    $randomDay = rand(1, $daysInMonth);
                    $date = Carbon::create($year, $randomMonth, $randomDay);

                    if (!$date->isWeekday()) continue;

                    $now = now();
                    DB::table('presences')->insert([
                        'employee_id' => $id,
                        'date' => $date,
                        'check_in' => $date->copy()->setTime(8, rand(0, 15), 0),
                        'check_out' => $date->copy()->setTime(17, rand(0, 30), 0),
                        'status' => 'present',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        DB::table('leave_requests')->insert([
            [
                'employee_id' => 5,
                'leave_type' => 'Sick',
                'start_date' => Carbon::parse('2025-02-10'),
                'end_date' => Carbon::parse('2025-02-20'),
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 3,
                'leave_type' => 'Vacation',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->subDays(3),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 2,
                'leave_type' => 'Personal',
                'start_date' => Carbon::parse('2025-11-01'),
                'end_date' => Carbon::parse('2025-11-05'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 1,
                'leave_type' => 'Birth Leave',
                'start_date' => Carbon::parse('2025-03-01'),
                'end_date' => Carbon::parse('2025-04-30'),
                'status' => 'rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
