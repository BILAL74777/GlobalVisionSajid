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

            $transactions = ReferralAccount::where('referral_id', $referral->id)->get();
    
            // Group the transactions by parent_id
            $groupedTransactions = $transactions->groupBy('parent_id');
        
            // Prepare an array to store grouped data with total calculations
            $groupedData = [];

            foreach ($groupedTransactions as $parentId => $transactionsGroup) {
                $totalCashIn = 0;
                $totalCashOut = 0;
                $totalCommissionAmount = 0;
        
                // Loop through each transaction in the group and calculate totals
                foreach ($transactionsGroup as $transaction) {
                    $totalCashIn += $transaction->cash_in;
                    $totalCashOut += $transaction->cash_out;
                    $totalCommissionAmount += $transaction->commission_amount;
                }
        
                // Store the grouped data and totals for each parent_id
                $groupedData[] = [
                    'parent_id' => $parentId,
                    'transactions' => $transactionsGroup,
                    'total_cash_in' => $totalCashIn,
                    'total_cash_out' => $totalCashOut,
                    'total_commission_amount' => $totalCommissionAmount
                ];
            }

             // Iterate through the transactions and get related Visa and FamilyVisa details
        foreach ($transactions as $transaction) {
            // Get the Visa record for the transaction
            $visa = Visa::where('id', $transaction->visa_id)->first();
            $transaction->visa = $visa;
    
            // If a Visa record exists, check for related FamilyVisa records
            if ($visa) {
                $familyMembers = FamilyVisa::where('visa_id', $visa->id)->get();
                $transaction->familyMembers = $familyMembers;  // Attach family members to the transaction
            } else {
                $transaction->familyMembers = []; // No family members if no visa exists
            }
        }
    
        // Transform the groupedData array using a foreach loop
        foreach ($groupedData as &$group) {
            $group['transactions'] = $group['transactions']->map(function ($transaction) {
                return $transaction->toArray(); // Convert each transaction to an array
            });
        }
    
        // Return the view with the data
        return Inertia::render('Referral/Details', [
            'referral'     => $referral,
            'transactions' => $transactions,
            'groupedData'  => $groupedData, // Now contains the transaction arrays
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
        $visas = Visa::select('id', 'full_name', 'visa_fee', 'amount', 'date')->where('status', '!=', 'Refunded')
            ->get();

        $familyVisas = FamilyVisa::select('id', 'visa_id', 'full_name', 'visa_fee', 'amount', 'date')->where('status', '!=', 'Refunded')
            ->get();

        return response()->json([
            'visas'        => $visas,
            'family_visas' => $familyVisas,
        ]);
    }

    public function approval_rejection_rate()
    {
        // Count the total number of visas and the number of approved and rejected visas
        $totalVisas    = Visa::where('status', '!=', 'Refunded')->count();
        $approvedVisas = Visa::where('status', 'Approved')->count();
        $rejectedVisas = Visa::where('status', 'Rejected')->count();

        // Calculate the approval and rejection percentages
        $approvalRate  = $totalVisas > 0 ? ($approvedVisas / $totalVisas) * 100 : 0;
        $rejectionRate = $totalVisas > 0 ? ($rejectedVisas / $totalVisas) * 100 : 0;

        return response()->json([
            'approval_rate'  => round($approvalRate, 2),
            'rejection_rate' => round($rejectionRate, 2),
        ]);
    }

}
