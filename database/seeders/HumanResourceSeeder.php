<?php

namespace Database\Seeders;

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

        // 1. CLEAN SLATE (Hapus data lama bersih-bersih)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('departments')->truncate();
        DB::table('positions')->truncate(); 
        DB::table('users')->truncate();
        DB::table('employees')->truncate();
        DB::table('tasks')->truncate();
        DB::table('salaries')->truncate();
        DB::table('presences')->truncate();
        DB::table('leave_requests')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. DEPARTMENTS
        DB::table('departments')->insert([
            ['name' => 'Human Resources', 'description' => 'Manajemen SDM', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technology', 'description' => 'Departemen IT & Tech', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sales & Marketing', 'description' => 'Pemasaran dan Penjualan', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance & Accounting', 'description' => 'Keuangan dan Akuntansi', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operation', 'description' => 'Operasional Bisnis', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. POSITIONS (Dengan Gaji Pokok)
        DB::table('positions')->insert([
            ['id' => 1, 'title' => 'HR Manager', 'description' => 'Manajer HR', 'base_salary' => 12500000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'title' => 'Developer', 'description' => 'Pengembang Kode', 'base_salary' => 10000000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'title' => 'Sales', 'description' => 'Penjualan', 'base_salary' => 6000000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'title' => 'Accounting', 'description' => 'Staf Keuangan', 'base_salary' => 7000000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'title' => 'Supervisor', 'description' => 'Supervisor Operasional', 'base_salary' => 8500000, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Data Karyawan Statis (Supaya login tidak berubah-ubah)
        $employeesData = [
            ['id' => 1, 'user_name' => 'Asep HR', 'user_email' => 'kaztztl@gmail.com', 'position_id' => 1, 'department_id' => 1, 'fullname' => 'Asep Santoso', 'status' => 'active'],
            ['id' => 2, 'user_name' => 'Joko IT', 'user_email' => 'jokoit@gmail.com', 'position_id' => 2, 'department_id' => 2, 'fullname' => 'Joko Pramono', 'status' => 'active'],
            ['id' => 3, 'user_name' => 'Denis Sales', 'user_email' => 'denissales@gmail.com', 'position_id' => 3, 'department_id' => 3, 'fullname' => 'Denis Saputra', 'status' => 'active'],
            ['id' => 4, 'user_name' => 'Bobby Finance', 'user_email' => 'bobbyfinance@gmail.com', 'position_id' => 4, 'department_id' => 4, 'fullname' => 'Bobby Karta', 'status' => 'active'],
            ['id' => 5, 'user_name' => 'Udin Dev', 'user_email' => 'udin.dev@gmail.com', 'position_id' => 2, 'department_id' => 2, 'fullname' => 'Udin Wijaksono', 'status' => 'inactive'],
        ];

        // 4. INSERT EMPLOYEES & USERS
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
                'position_id' => $data['position_id'], 
                'status' => $data['status'],
                'salary' => 0, // Fallback 0, karena pakai base_salary di position
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

        // 5. PRESENCES (FULL YEAR 2025)
        // Kita generate data dari Januari sampai Hari Ini supaya chart dashboard bagus.
        $employeeIds = [1, 2, 3, 4]; // Karyawan aktif saja
        $startOfYear = Carbon::create(2025, 1, 1);
        $today = Carbon::now();

        foreach ($employeeIds as $id) {
            // Loop dari 1 Jan sampai hari ini
            $currDate = $startOfYear->copy();
            
            while ($currDate->lte($today)) {
                // Skip Sabtu Minggu
                if ($currDate->isWeekend()) {
                    $currDate->addDay();
                    continue;
                }

                // Random status
                $rand = rand(1, 100);
                if ($rand <= 85) $status = 'present'; // 85% Masuk
                elseif ($rand <= 95) $status = 'absent'; // 10% Alfa
                else $status = 'leave'; // 5% Cuti

                $checkIn = null;
                $checkOut = null;
                $latitude = null;
                $longitude = null;

                if ($status == 'present') {
                    // Random jam masuk (biar real)
                    $checkIn = $currDate->copy()->setTime(rand(7, 9), rand(0, 59), 0);
                    $checkOut = $currDate->copy()->setTime(17, rand(0, 30), 0);
                    
                    // Lokasi dummy (Sekitar kantor)
                    $latitude = -6.89 + ($faker->randomFloat(4, -0.001, 0.001));
                    $longitude = 107.61 + ($faker->randomFloat(4, -0.001, 0.001));
                }

                DB::table('presences')->insert([
                    'employee_id' => $id,
                    'date' => $currDate->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $currDate->addDay();
            }
        }

        // 6. TASKS (Variasi Status & Deadline)
        // Tugas untuk Payroll Test (Bulan Ini)
        DB::table('tasks')->insert([
            [
                'title' => 'Laporan Bulanan Q3',
                'description' => 'Rekap data penjualan.',
                'assigned_to' => 3, // Sales
                'due_date' => Carbon::now()->subDays(2),
                'status' => 'completed',
                'completed_at' => Carbon::now()->subDays(3), // Cepat (Bonus)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Fix Bug Login',
                'description' => 'User tidak bisa reset password.',
                'assigned_to' => 2, // Dev
                'due_date' => Carbon::now()->subDays(5),
                'status' => 'completed',
                'completed_at' => Carbon::now()->subDays(1), // Telat (Denda)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Desain Banner Promo',
                'description' => 'Banner untuk event tahun baru.',
                'assigned_to' => 3, // Sales
                'due_date' => Carbon::now()->addDays(5),
                'status' => 'pending',
                'completed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Rekap Pajak Tahunan',
                'description' => 'Siapkan dokumen PPh 21.',
                'assigned_to' => 4, // Finance
                'due_date' => Carbon::now()->addDays(10),
                'status' => 'pending',
                'completed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Interview Kandidat Baru',
                'description' => 'Posisi Junior Backend.',
                'assigned_to' => 1, // HR
                'due_date' => Carbon::now()->subDays(10),
                'status' => 'completed',
                'completed_at' => Carbon::now()->subDays(10), // Tepat Waktu
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 7. LEAVE REQUESTS (Variasi Lengkap)
        DB::table('leave_requests')->insert([
            // Kasus 1: Cuti Sakit (Disetujui) - Udin
            [
                'employee_id' => 5,
                'leave_type' => 'Sick',
                'start_date' => Carbon::parse('2025-02-10'),
                'end_date' => Carbon::parse('2025-02-12'),
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kasus 2: Cuti Liburan (Pending) - Denis
            [
                'employee_id' => 3,
                'leave_type' => 'Vacation',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(13),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kasus 3: Izin Pribadi (Pending) - Joko
            [
                'employee_id' => 2,
                'leave_type' => 'Personal',
                'start_date' => Carbon::now()->addDays(2),
                'end_date' => Carbon::now()->addDays(2),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kasus 4: Cuti Melahirkan (Ditolak) - Asep (Laki-laki kok melahirkan?)
            [
                'employee_id' => 1,
                'leave_type' => 'Birth Leave',
                'start_date' => Carbon::parse('2025-03-01'),
                'end_date' => Carbon::parse('2025-04-30'),
                'status' => 'rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kasus 5: Sakit Lampau (Approved) - Bobby
            [
                'employee_id' => 4,
                'leave_type' => 'Sick',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->subMonths(1)->addDays(1),
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 8. DATA GAJI (History Bulan Lalu)
        // Agar halaman index salaries tidak kosong saat pertama dibuka
        $lastMonth = Carbon::now()->subMonth();
        foreach ($employeeIds as $id) {
            // Ambil base salary dari array positions di atas (manual mapping)
            $salaryMap = [1 => 12500000, 2 => 10000000, 3 => 6000000, 4 => 7000000, 5 => 8500000];
            $base = $salaryMap[$id] ?? 0;
            
            DB::table('salaries')->insert([
                'employee_id' => $id,
                'salary' => $base,
                'bonuses' => 0,
                'deductions' => 0,
                'net_salary' => $base,
                'pay_date' => $lastMonth->endOfMonth()->format('Y-m-d'),
                'created_at' => $lastMonth,
                'updated_at' => $lastMonth,
            ]);
        }
    }
}