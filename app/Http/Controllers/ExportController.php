<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Presence;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Mengekspor Data Karyawan (Employees) ke file Excel (.xlsx).
     */
    public function exportEmployees()
    {
        // 1. Ambil data
        $employees = Employee::with('department', 'position')->get();

        // 2. Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Atur judul Sheet
        $sheet->setTitle('Data Karyawan');

        // 3. Tulis Header Kolom
        $headers = [
            'ID', 
            'Nama Lengkap', 
            'Email', 
            'Departemen', 
            'Jabatan', 
            'Tanggal Masuk', 
            'Gaji Pokok',
            'Status'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // 4. Tulis Data Baris
        $row = 2;
        foreach ($employees as $employee) {
            $data = [
                $employee->id,
                $employee->fullname,
                $employee->email,
                $employee->department->name ?? 'N/A',
                $employee->position->title ?? 'N/A',
                $employee->hire_date,
                $employee->salary,
                ucfirst($employee->status)
            ];
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }

        // 5. Atur lebar kolom agar otomatis
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 6. Buat Writer dan kirimkan respons stream
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_karyawan_' . time() . '.xlsx';
        
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Mengekspor Data Kehadiran (Presences) ke file Excel (.xlsx).
     */
    public function exportPresences(Request $request)
    {
        // Logika otorisasi
        if (!Auth::user()->can('presence_view_all')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Presence::with('employee');
        
        // Anda bisa menambahkan filter waktu atau karyawan di sini jika diperlukan
        // Contoh: $query->whereYear('date', date('Y'));
        
        $presences = $query->orderBy('date', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kehadiran');

        $headers = [
            'ID', 
            'Nama Karyawan', 
            'Tanggal', 
            'Check In', 
            'Check Out', 
            'Status'
        ];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($presences as $presence) {
            $data = [
                $presence->id,
                $presence->employee->fullname ?? 'N/A',
                $presence->date,
                $presence->check_in,
                $presence->check_out,
                ucfirst($presence->status)
            ];
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_kehadiran_' . time() . '.xlsx';
        
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}