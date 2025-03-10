<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAccount;
use App\Models\FamilyVisa;
use App\Models\Referral;
use App\Models\ReferralAccount;
use App\Models\Visa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisaController extends Controller
{
    public function family_index()
    {

        return Inertia::render('Visa/Family', ['country' => 'Paskistan & Afghanistan']);
    }
    public function individual_index()
    {

        return Inertia::render('Visa/Individual', ['country' => 'Paskistan & Afghanistan']);
    }

    public function storeIndividual(Request $request)
    {
        // Retrieve existing visa or create new one
        if ($request->id) {
            $visa             = Visa::where('id', $request->id)->first();
            $referralAaccount = ReferralAccount::where('visa_id', $request->id)->first();
            $employeeAccount  = EmployeeAccount::where('visa_id', $request->id)->first();
        } else {
            $visa             = new Visa();
            $referralAaccount = new ReferralAccount();
            $employeeAccount  = new EmployeeAccount();
        }

        // Assign values to Visa
        $visa->full_name         = $request->full_name;
        $visa->phone_number      = $request->phone_number;
        $visa->status            = $request->status;
        $visa->amount            = $request->amount;
        $visa->visa_fee          = $request->visa_fee;
        $visa->tracking_id       = $request->tracking_id;
        $visa->gmail             = $request->gmail;
        $visa->pak_visa_password = $request->pak_visa_password;
        $visa->gmail_password    = $request->gmail_password;
        $visa->gender            = $request->gender;
        $visa->date              = $request->date;

        $visa->family_name    = null;
        $visa->family_members = 1;
        $visa->entry_type     = 'Individual';
        $visa->save();

        //  If a referral exists, store referral data and do NOT generate employee data
        if ($request->referral) {
            $visa->referral            = $request->referral;
            $visa->referral_commission = $request->referral_commission;

            $referralAaccount->visa_id             = $visa->id;
            $referralAaccount->cash_in             = $request->amount;
            $referralAaccount->cash_out            = $request->visa_fee;
            $referralAaccount->refferal_commission = $request->referral_commission;
            $referralAaccount->referral_id         = $request->referral;
            $referralAaccount->save();
           

            // Set employee-related fields to NULL since referral exists
            $visa->employee_id         = null;
            $visa->employee_commission = null;
        }
        //  If NO referral exists, generate employee data
        else {
            $employee_records = Employee::select('id', 'commission')->get();

            // $visa->employee_id = json_encode($request->employee); // Store multiple employees as JSON if needed

            // Calculate the net profit
            $profitAmount = $request->amount - $request->visa_fee;

            foreach ($employee_records as $employee) {
                $employeeCommissionAmount = ($profitAmount * $employee->commission) / 100; // Use the employee's actual commission percentage

                $employeeAccount                      = new EmployeeAccount();
                $employeeAccount->visa_id             = $visa->id;
                $employeeAccount->cash_in             = $request->amount;
                $employeeAccount->cash_out            = $request->visa_fee;
                $employeeAccount->employee_id         = $employee->id;
                $employeeAccount->amount              = $employeeCommissionAmount;   // Store calculated commission
                $employeeAccount->employee_commission = $employee->commission . "%"; // Store actual commission percentage
                $employeeAccount->save();
            }

            // Set referral-related fields to NULL since no referral exists
            $visa->referral            = null;
            $visa->referral_commission = null;
        }

        return 'success';
    }

    public function storeFamily(Request $request)
    {
        if (empty($request->family_forms) || ! is_array($request->family_forms)) {
            return response()->json(['message' => 'Invalid family data provided!'], 400);
        }

        // Extract the primary member
        $primaryMember = $request->family_forms[0];

        // Save the primary member in `visas` table
        $visa                    = new Visa();
        $visa->family_name       = $request->family_name;
        $visa->family_members    = $request->family_members;
        $visa->full_name         = $primaryMember['full_name'];
        $visa->phone_number      = $primaryMember['phone_number'];
        $visa->status            = $primaryMember['status'];
        $visa->amount            = $primaryMember['amount'];
        $visa->visa_fee          = $primaryMember['visa_fee'];
        $visa->tracking_id       = $primaryMember['tracking_id'];
        $visa->gmail             = $primaryMember['gmail'];
        $visa->pak_visa_password = $primaryMember['pak_visa_password'];
        $visa->gmail_password    = $primaryMember['gmail_password'];
        $visa->gender            = $primaryMember['gender'];
        $visa->date              = $primaryMember['date'];
        $visa->entry_type        = 'Family';
        $visa->save();

        // If a referral exists, store referral data and DO NOT generate employee data
        if ($request->referral) {
            $visa->referral            = $request->referral;
            $visa->referral_commission = $request->referral_commission;

            $referralAccount                      = new ReferralAccount();
            $referralAccount->visa_id             = $visa->id;
            $referralAccount->cash_in             = $primaryMember['amount'];
            $referralAccount->cash_out            = $primaryMember['visa_fee'];
            $referralAccount->refferal_commission = $request->referral_commission;
            $referralAccount->referral_id         = $request->referral;
            // dd($request->referral);
            $referralAccount->save();

            // No employee data should be stored if referral exists
            $visa->employee_id         = null;
            $visa->employee_commission = null;
        }
        // If NO referral exists, calculate employee commissions
        else {
            $employee_records = Employee::select('id', 'commission')->get();

            // Collect total amounts from all family members
            $totalAmount  = 0;
            $totalVisaFee = 0;

            foreach ($request->family_forms as $member) {
                $totalAmount += $member['amount'];
                $totalVisaFee += $member['visa_fee'];
            }

            // Calculate total profit
            $profitAmount = $totalAmount - $totalVisaFee;

            // Store multiple employees as JSON
            $visa->employee_id = json_encode($request->employee);

            // Calculate total commission per employee and store ONE entry
            foreach ($employee_records as $employee) {
                $employeeCommissionAmount = ($profitAmount * $employee->commission) / 100; // Dynamic commission

                $employeeAccount                      = new EmployeeAccount();
                $employeeAccount->visa_id             = $visa->id;
                $employeeAccount->cash_in             = $totalAmount;
                $employeeAccount->cash_out            = $totalVisaFee;
                $employeeAccount->employee_id         = $employee->id;
                $employeeAccount->amount              = $employeeCommissionAmount;
                $employeeAccount->employee_commission = $employee->commission . "%"; // Store actual commission %
                $employeeAccount->save();
            }

            // No referral data should be stored if employees exist
            $visa->referral            = null;
            $visa->referral_commission = null;
        }

        // Insert remaining family members into `family_visas`
        $familyVisaData = [];
        foreach (array_slice($request->family_forms, 1) as $member) {
            $familyVisaData[] = [
                'visa_id'           => $visa->id, // Linking to primary visa
                'full_name'         => $member['full_name'],
                'phone_number'      => $member['phone_number'],
                'status'            => $member['status'],
                'amount'            => $member['amount'],
                'visa_fee'          => $member['visa_fee'],
                'tracking_id'       => $member['tracking_id'],
                'gmail'             => $member['gmail'],
                'pak_visa_password' => $member['pak_visa_password'],
                'gmail_password'    => $member['gmail_password'],
                'gender'            => $member['gender'],
                'date'              => $member['date'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        if (! empty($familyVisaData)) {
            FamilyVisa::insert($familyVisaData);
        }

        return response()->json(['message' => 'Family Visa data saved successfully!', 'primary_visa' => $visa], 201);
    }

    public function customers_fetch()
    {
        // Dump request data to debug
        dd($request->all());
    }

    public function getIndividualVisas()
    {
        // Fetch individual visa records
        $individualVisas = Visa::where('entry_type', 'Individual')->get();
        foreach ($individualVisas as $record) {
            if ($record->referral) {

                $referral              = Referral::where('id', $record->referral)->first();
                $record->referral_name = $referral->name;

                $referralAaccount = ReferralAccount::where('visa_id', $record->id)->first();
                if ($referralAaccount) {
                    $record->visa_fee = $referralAaccount->cash_out;
                }
            }

        }
        return $individualVisas;
    }

    public function getFamilyVisas()
    {
        // Fetch family visa records along with associated family members
        $familyVisas = Visa::where('entry_type', 'Family')
            ->with(['familyMembers' => function ($query) {
                $query->select(
                    'id', 'visa_id', 'full_name', 'phone_number',
                    'status', 'amount', 'visa_fee', 'tracking_id', 'gmail',
                    'gender', 'date', 'gmail_password', 'pak_visa_password'
                );
            }])
            ->select(
                'id', 'full_name', 'phone_number', 'status',
                'amount', 'visa_fee', 'tracking_id', 'gmail', 'gender',
                'date', 'entry_type', 'gmail_password', 'pak_visa_password',
                'referral', 'referral_commission', 'family_name', 'family_members'
            )
            ->get();

        // Attach referral name, family name, and family members count
        foreach ($familyVisas as $record) {
            // Handle referral name safely
            if (! empty($record->referral)) {
                $referral              = Referral::find($record->referral);
                $record->referral_name = $referral ? $referral->name : "Unknown Referral";
            } else {
                $record->referral_name = "No Referral";
            }

            $referralAaccount = ReferralAccount::where('visa_id', $record->id)->first();
            if ($referralAaccount) {
                $record->visa_fee = $referralAaccount->cash_out;
            }
            // Set family name (use primary member's name if not set)
            $record->family_name = $record->family_name ?? $record->full_name;

        }

        return $familyVisas;
    }

    public function visa_record_delete($id)
    {
        // Check if the record exists in the FamilyVisa table
        $familyVisa = FamilyVisa::find($id);

        if ($familyVisa) {
            $familyVisa->delete();
            return 'success';
        }

        // Check if the record exists in the Visa table
        $visa = Visa::find($id);
        if ($visa) {
            $visa->delete();

            return 'success';
        }
    }

    public function referrals()
    {
        return Inertia::render('Referral/Index');
    }
    public function referrals_fetch()
    {
        $Referrals = Referral::get();
        return $Referrals;
    }

    public function referral_store(Request $request)
    {
        // Validate request
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        // Check if an ID is provided for update
        if ($request->id) {
            $referral = Referral::find($request->id); // Use find() instead of where()->first()
        } else {
            $referral = new Referral();
        }

        // If no record found and ID was provided, return an error
        if ($request->id && ! $referral) {
            return response()->json(['message' => 'Referral not found'], 404);
        }

        // Assign values
        $referral->name    = $request->name;
        $referral->email   = $request->email;
        $referral->phone   = $request->phone;
        $referral->address = $request->address;

        // Save the referral record
        $referral->save();
        return 'success';
    }

    public function refferrals_pluck()
    {
        return Referral::pluck('name', 'id');
    }
    public function referral_details($id)
{
    // Fetch employee details
    $referral = Referral::where('id', $id)->first(); 
     
    // Manually fetch transactions related to this employee
    $transactions = ReferralAccount::where('referral_id', $id)->get();
 
    return Inertia::render('Referral/Details', [
        'referral'     => $referral,
        'transactions' => $transactions,  
    ]);
}

}
