<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Role;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
         if (! Auth::check()) {
            return redirect('/login'); 
        }

        $user = $request->user(); 

        if (is_null($user->employee_id)) {
            abort(403, 'Anda tidak terdaftar sebagai karyawan.');
        }

        $employee = Employee::find($user->employee_id);

        if (!$employee || !isset($employee->role) || !in_array($employee->role->title, $roles)) {
             abort(403, 'Akses ditolak. Peran Anda tidak diizinkan mengakses halaman ini.');
        }
        
        $request->session()->put('role', $employee->role->title); 
        $request->session()->put('employee_id', $employee->id); 

        return $next($request);
    }
}
