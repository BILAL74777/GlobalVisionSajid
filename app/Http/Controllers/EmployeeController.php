<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

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

    public function fetch()
    {
        $employees = Employee::get();
        
        return $employees;
    }

    public function store(Request $request)
    {
        // Check if an ID is provided for update
        if ($request->id) {
            // Find the existing employee record
            $employee = Employee::find($request->id);
            
            // Get the user associated with the email
            $User = User::where('email', $request->email)->first();
    
            // Validate request for existing employee (email validation allows the same email if updating)
            $request->validate([
                'name'       => 'required|string|max:255',
                'phone'      => 'required|string|max:20',
                'commission' => 'required',
                'email'      => 'required|email|unique:users,email,' . ($User ? $User->id : ''),
                'address'    => 'nullable|string|max:500',
            ]);
    
            // If the user does not exist, create a new one
            if (! $User) {
                $User = new User();
                $User->password = Hash::make($request->phone);
                $User->email_verified_at = now();
            }
        } else {
            // Validate request for new employee (email must be unique)
            $request->validate([
                'name'       => 'required|string|max:255',
                'phone'      => 'required|string|max:20',
                'commission' => 'required',
                'email'      => 'required|email|unique:users,email',
                'address'    => 'nullable|string|max:500',
            ]);
    
            // Create a new employee and user
            $employee = new Employee();
            $User = new User();
            $User->password = Hash::make($request->phone);
            $User->email_verified_at = now();
        }
    
        // If no record found and ID was provided, return an error
        if ($request->id && ! $employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
    
        // Assign values to the employee and user
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->commission = $request->commission;
        $employee->address = $request->address;
    
        $User->name = $request->name;
        $User->email = $request->email;
        $User->role = 'employee';
    
        // Save the user and employee records
        $User->save();
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
