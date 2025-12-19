<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Employee;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function presences(Request $request)
    {
        $filename = 'presences-' . date('Y-m-d-H-i-s') . '.xls';

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $query = Presence::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $data = $query->orderBy('date', 'desc')->get();

        echo '<table border="1">';
        echo '<tr>
                <th>Date</th>
                <th>Employee</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
              </tr>';

        foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . $row->date . '</td>';
            echo '<td>' . ($row->employee->fullname ?? '-') . '</td>';
            echo '<td>' . $row->check_in . '</td>';
            echo '<td>' . $row->check_out . '</td>';
            echo '<td>' . $row->status . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    public function employees()
    {
        $filename = 'employees-data-' . date('Y-m-d') . '.xls';

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $employees = Employee::with(['position', 'department'])->get();

        echo '<table border="1">';
        echo '<tr style="background-color: #f0f0f0; font-weight: bold;">
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Position</th>
                <th>Join Date</th>
                <th>Status</th>
                <th>Salary (Override)</th>
              </tr>';

        foreach ($employees as $emp) {
            echo '<tr>';
            echo '<td>' . $emp->id . '</td>';
            echo '<td>' . $emp->fullname . '</td>';
            echo '<td>' . $emp->email . '</td>';
            echo '<td>' . $emp->phone_number . '</td>';
            echo '<td>' . ($emp->department->name ?? '-') . '</td>';
            echo '<td>' . ($emp->position->title ?? '-') . '</td>';
            echo '<td>' . $emp->hire_date . '</td>';
            echo '<td>' . $emp->status . '</td>';
            echo '<td>' . number_format($emp->salary, 0, ',', '.') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
}