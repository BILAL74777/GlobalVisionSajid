<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAccount;
use App\Models\FamilyVisa;
use App\Models\Referral;
use App\Models\ReferralAccount;
use App\Models\Visa;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'employee') {
            // Fetch employee details
            $employee = Employee::where('email', auth()->user()->email)->first();

            // Manually fetch transactions related to this employee
            $transactions = EmployeeAccount::where('employee_id', $employee->id)->get();

            return Inertia::render('Employees/Details', [
                'employee'     => $employee,
                'transactions' => $transactions,
            ]);
        }

        if (auth()->user()->role == 'referral') {
            $referral = Referral::where('email', auth()->user()->email)->first();
            // Manually fetch transactions related to this employee
            
            $transactions = ReferralAccount::where('referral_id', $referral->id)->get();

            return Inertia::render('Referral/Details', [
                'referral'     => $referral,
                'transactions' => $transactions,
            ]);
        }

        return Inertia::render('Dashboard/Index');
    }
    public function create()
    {
        return Inertia::render('Dashboard/Create', [
            'title' => "Create new Events",
        ]);
    }

    public function dashboard_fetch()
    {
        $visas = Visa::select('id', 'full_name', 'visa_fee', 'amount', 'date')->where('status','!=','Refunded')
            ->get();

        $familyVisas = FamilyVisa::select('id', 'visa_id', 'full_name', 'visa_fee', 'amount', 'date')->where('status','!=','Refunded')
            ->get();

        return response()->json([
            'visas'        => $visas,
            'family_visas' => $familyVisas,
        ]);
    }
}
