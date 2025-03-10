<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeeAccount;
use App\Models\Visa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class EmployeeController extends Controller
{
    public function index()
    {
        return Inertia::render('Employees/Index');
    }

    public function employees()
{
    return Inertia::render('Employee/Index');
}

public function  fetch()
{
    $employees = Employee::get();
    return $employees;
}

public function store(Request $request)
{
    // Validate request
    $request->validate([
        'name'    => 'required|string|max:255',
        'phone'   => 'required|string|max:20',
        'commission'   => 'required',
        'email'   => 'nullable|email|max:255',
        'address' => 'nullable|string|max:500',
    ]);

    // Check if an ID is provided for update
    if ($request->id) {
        $employee = Employee::find($request->id);
    } else {
        $employee = new Employee();
    }

    // If no record found and ID was provided, return an error
    if ($request->id && ! $employee) {
        return response()->json(['message' => 'Employee not found'], 404);
    }

    // Assign values
    $employee->name    = $request->name;
    $employee->email   = $request->email;
    $employee->phone   = $request->phone;
    $employee->commission   = $request->commission;
    $employee->address = $request->address;

    // Save the employee record
    $employee->save();
    return 'success';
}

public function pluck()
{
    return Employee::pluck('name', 'id');
}
public function employee_details($id)
{
    // Fetch employee details
    $employee = Employee::where('id', $id)->first(); 
     
    // Manually fetch transactions related to this employee
    $transactions = EmployeeAccount::where('employee_id', $id)->get();
 
    return Inertia::render('Employees/Details', [
        'employee'     => $employee,
        'transactions' => $transactions,  
    ]);
}


}
