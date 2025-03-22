<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAccount;
use App\Models\FamilyVisa;
use App\Models\Referral;
use App\Models\ReferralAccount;
use App\Models\User;
use App\Models\Visa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class VisaController extends Controller
{
    public function family_index()
    {

        return Inertia::render('Visa/Family', ['country' => 'Pakistan Visa']);
    }
    public function individual_index()
    {

        return Inertia::render('Visa/Individual', ['country' => 'Pakistan Visa']);
    }

    public function storeIndividual(Request $request)
    {
        // If $request->id exists, we are updating an existing visa record
        if ($request->id) {
            // Retrieve the existing visa record to update
            $visa = Visa::where('id', $request->id)->first();
        } else {
            // If no ID, create a new visa record
            $visa = new Visa();
        }

        // Common fields to assign regardless of create or update
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
        $visa->user_id           = auth()->user()->id;

        $visa->family_name    = null;
        $visa->phone_number    = null;
        $visa->family_members = 1;
        $visa->entry_type     = 'Individual';
        $visa->save();

        // Case when there is a referral in the request
        if ($request->referral) {
            // If $request->referral matches the current visa referral (update)
            if ($request->referral == $visa->referral) {

                // Update the existing ReferralAccount if it exists
                $referralAccount = ReferralAccount::where('visa_id', $request->id)
                    ->where('referral_id', $request->referral)
                    ->first();

                // If no existing referral account is found, create a new one
                if (! $referralAccount) {
                    $referralAccount = new ReferralAccount();
                }

                // Get referral data
                $referralData             = Referral::select('id', 'commission')->where('id', $request->referral)->first();
                $profitAmount             = $request->amount - $request->visa_fee;
                $referralCommissionAmount = ($profitAmount * $referralData->commission) / 100;

                // Assign values to referral account and save
                $referralAccount->visa_id             = $visa->id;
                $referralAccount->cash_in             = $request->amount;
                $referralAccount->cash_out            = $request->visa_fee;
                $referralAccount->referral_id         = $referralData->id;
                $referralAccount->commission_amount   = $referralCommissionAmount;
                $referralAccount->referral_commission = $referralData->commission . "%";
                $referralAccount->save();
            } else {
                // dd("test in else",$visa->referral);
                // Referral ID has changed, delete old ReferralAccount and EmployeeAccount records

                // Delete the old ReferralAccount if a different referral is selected
                $olderReferral = ReferralAccount::where('referral_id', $visa->referral)->first();

                if ($olderReferral) {
                    $olderReferral->delete();
                }
                // Delete the related EmployeeAccount records as the referral has changed
                EmployeeAccount::where('visa_id', $request->id)->delete();

                // Create a new ReferralAccount for the new referral
                $referralData = Referral::select('id', 'commission')->where('id', $request->referral)->first();
                if ($referralData) {
                    $profitAmount             = $request->amount - $request->visa_fee;
                    $referralCommissionAmount = ($profitAmount * $referralData->commission) / 100;

                    // Store new ReferralAccount data
                    $referralAccount                      = new ReferralAccount();
                    $referralAccount->visa_id             = $visa->id;
                    $referralAccount->cash_in             = $request->amount;
                    $referralAccount->cash_out            = $request->visa_fee;
                    $referralAccount->referral_id         = $referralData->id;
                    $referralAccount->commission_amount   = $referralCommissionAmount;
                    $referralAccount->referral_commission = $referralData->commission . "%";
                    $referralAccount->save();
                }

                // Set employee-related fields to NULL since the referral exists
                // $visa->employee_id = null;
            }
        } else {

            // No referral in the request, but the visa has an existing referral
            if ($visa->referral) {
                // Delete the old ReferralAccount as there's no referral anymore
                $referralAccount = ReferralAccount::where('referral_id', $visa->referral)->first();
                if ($referralAccount) {
                    $referralAccount->delete();
                }

                // Create EmployeeAccount records for each employee
                $employee_records = Employee::select('id', 'commission')->get();
                $profitAmount     = $request->amount - $request->visa_fee;

                foreach ($employee_records as $employee) {
                    $employeeCommissionAmount = ($profitAmount * $employee->commission) / 100;

                    $employeeAccount                      = new EmployeeAccount();
                    $employeeAccount->visa_id             = $visa->id;
                    $employeeAccount->cash_in             = $request->amount;
                    $employeeAccount->cash_out            = $request->visa_fee;
                    $employeeAccount->employee_id         = $employee->id;
                    $employeeAccount->amount              = $employeeCommissionAmount;
                    $employeeAccount->employee_commission = $employee->commission . "%";
                    $employeeAccount->save();
                }

                // Clear referral fields on the visa as no referral exists
                $visa->referral = null;
            } else {
                // No referral and no existing referral on the visa, just process EmployeeAccount records
                $employee_records = Employee::select('id', 'commission')->get();
                $profitAmount     = $request->amount - $request->visa_fee;

                foreach ($employee_records as $employee) {
                    $employeeCommissionAmount = ($profitAmount * $employee->commission) / 100;

                    $employeeAccount                      = new EmployeeAccount();
                    $employeeAccount->visa_id             = $visa->id;
                    $employeeAccount->cash_in             = $request->amount;
                    $employeeAccount->cash_out            = $request->visa_fee;
                    $employeeAccount->employee_id         = $employee->id;
                    $employeeAccount->amount              = $employeeCommissionAmount;
                    $employeeAccount->employee_commission = $employee->commission . "%";
                    $employeeAccount->save();
                }

                // Clear referral fields on the visa as no referral exists

            }
        }

        $visa->referral = $request->referral ?? null;
        $visa->save();
        return 'success';
    }

    public function storeFamily(Request $request)
    {
        if (empty($request->family_forms) || !is_array($request->family_forms)) {
            return response()->json(['message' => 'Invalid family data provided!'], 400);
        }
    
        // Extract the primary member
        $primaryMember = $request->family_forms[0];
    
        // Initialize total amount and visa_fee
        $totalAmount = 0;
        $totalVisaFee = 0;
    
        // Save the primary member in `visas` table
        $visa = new Visa();
        $visa->family_name       = $request->family_name;
        $visa->phone_number       = $request->phone_number;
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
        $visa->referral          = $request->referral;
        $visa->entry_type        = 'Family';
        $visa->user_id           = auth()->user()->id;
        $visa->save();
    
        // Add the primary member's data to total amount and visa fee
        $totalAmount += $primaryMember['amount'];
        $totalVisaFee += $primaryMember['visa_fee'];
    
        // Process for referral commission if referral exists
        if ($request->referral) {
            // Fetch the referral commission from the database
            $referralData = Referral::select('id', 'commission')->where('id', $request->referral)->first();
    
            if ($referralData) {
                $visa->referral_commission = $referralData->commission; // Save commission in visa table
    
                // Calculate total profit
                $totalProfitAmount = $totalAmount - $totalVisaFee;
                $referralCommissionAmount = ($totalProfitAmount * $referralData->commission) / 100;
    
                // Store referral commission in ReferralAccount table
                $referralAccount = new ReferralAccount();
                $referralAccount->visa_id             = $visa->id;
                $referralAccount->cash_in             = $totalAmount;  // Total amount for referral
                $referralAccount->cash_out            = $totalVisaFee; // Total visa fee for referral
                $referralAccount->referral_id         = $referralData->id;
                $referralAccount->commission_amount   = $referralCommissionAmount;  // Store calculated commission
                $referralAccount->referral_commission = $referralData->commission . "%"; // Store actual commission percentage
                $referralAccount->save();
            } else {
                // Handle case where referral data is not found
                $visa->referral_commission = null;
            }
        }
        // If no referral exists, process employee commissions
        else {
            $employee_records = Employee::select('id', 'commission')->get();
    
            // Calculate total profit for employees
            $totalProfitAmount = $totalAmount - $totalVisaFee;
    
            foreach ($employee_records as $employee) {
                $employeeCommissionAmount = ($totalProfitAmount * $employee->commission) / 100; // Employee commission calculation
    
                // Store employee commission in EmployeeAccount table
                $employeeAccount = new EmployeeAccount();
                $employeeAccount->visa_id             = $visa->id;
                $employeeAccount->cash_in             = $totalAmount;  // Total amount for employee
                $employeeAccount->cash_out            = $totalVisaFee; // Total visa fee for employee
                $employeeAccount->employee_id         = $employee->id;
                $employeeAccount->amount              = $employeeCommissionAmount;   // Store calculated commission
                $employeeAccount->employee_commission = $employee->commission . "%"; // Store actual commission percentage
                $employeeAccount->save();
            }
    
            // Set referral-related fields to NULL since no referral exists
            $visa->referral = null;
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
                'user_id'           => auth()->user()->id,
                'referral'          => $request->referral,
            ];
    
            // Add member's amount and visa fee to totals
            $totalAmount += $member['amount'];
            $totalVisaFee += $member['visa_fee'];
        }
    
        if (!empty($familyVisaData)) {
            FamilyVisa::insert($familyVisaData);
        }
    
        return response()->json(['message' => 'Family Visa data saved successfully!', 'primary_visa' => $visa], 201);
    }
    

    public function updateFamilyRecord(Request $request)
    {
        // Retrieve existing visa or create new one
        if ($request->visa_id) {
            $visa = FamilyVisa::where('id', $request->id)->first();
        } else {
            $visa = Visa::where('id', $request->id)->first();
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

        // $visa->family_name    = null;
        // $visa->family_members = 1;
        // $visa->entry_type     = 'Individual';
        $visa->save();
        // dd($request->visa_fee);
        // dd($visa);

        return 'success';
    }

    public function getIndividualVisas()
    {
        // Fetch individual visa records
        $individualVisas = Visa::where('entry_type', 'Individual')->orderByDesc('created_at')->get();

        foreach ($individualVisas as $record) {
            if ($record->referral) {

                $referral              = Referral::where('id', $record->referral)->first();
                $record->referral_name = $referral->name;

                $referralAccount = ReferralAccount::where('visa_id', $record->id)->first();
                if ($referralAccount) {
                    $record->visa_fee = $referralAccount->cash_out;
                }

            }
            if ($record->user_id) {
                $user                  = User::where('id', $record->user_id)->select('name')->first();
                $record->added_by_user = $user->name;

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
                    'gender', 'date', 'gmail_password', 'pak_visa_password', 'user_id'
                );
            }])
            ->select(
                'id', 'full_name', 'phone_number', 'status',
                'amount', 'visa_fee', 'tracking_id', 'gmail', 'gender',
                'date', 'entry_type', 'gmail_password', 'pak_visa_password',
                'referral', 'family_name', 'family_members', 'user_id'
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

            $referralAccount = ReferralAccount::where('visa_id', $record->id)->first();
            if ($referralAccount) {
                $record->visa_fee = $referralAccount->cash_out;
            }
            // Set family name (use primary member's name if not set)
            $record->family_name = $record->family_name ?? $record->full_name;
            $record->phone_number = $record->phone_number ?? $record->phone_number;

            if ($record->user_id) {
                $user                  = User::where('id', $record->user_id)->select('name')->first();
                $record->added_by_user = $user->name;

            }

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
            'email'   => 'required|unique:users,email',
            'address' => 'nullable|string|max:500',
        ]);

        // Check if an ID is provided for update
        if ($request->id) {
            $referral = Referral::find($request->id); // Use find() instead of where()->first()
            $User     = User::where('email', $request->email);
            if (! $User) {
                $User                    = new User();
                $User->password          = Hash::make($request->phone);
                $User->email_verified_at = now();
            }
        } else {
            $referral                = new Referral();
            $User                    = new User();
            $User->password          = Hash::make($request->phone);
            $User->email_verified_at = now();
        }

        // If no record found and ID was provided, return an error
        if ($request->id && ! $referral) {
            return response()->json(['message' => 'Referral not found'], 404);
        }

        // Assign values
        $referral->name       = $request->name;
        $referral->email      = $request->email;
        $referral->phone      = $request->phone;
        $referral->address    = $request->address;
        $referral->commission = $request->commission;

        $User->name  = $request->name;
        $User->email = $request->email;
        $User->role  = 'referral';

        $User->save();

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

    // Initialize arrays for individual and family visas
    $individualVisas = [];
    $familyVisas = [];

    foreach($transactions as $transaction) {
        // Fetch the associated Visa
        $transaction->visa = Visa::where('id', $transaction->visa_id)->first();

        // Check the type of visa and add to respective array
        if ($transaction->visa->entry_type == 'Individual') {
            $individualVisas[] = $transaction->visa;
        } elseif ($transaction->visa->entry_type == 'Family') {
            $familyVisas[] = $transaction->visa;
        }
    }

    // Fetch family members for the family visas
    foreach ($familyVisas as $visa) {
        $visa->familyMembers = Visa::where('entry_type', 'Family')
            ->where('family_name', $visa->family_name)
            ->with(['familyMembers' => function ($query) {
                $query->select(
                    'id', 'visa_id', 'full_name', 'phone_number',
                    'status', 'amount', 'visa_fee', 'tracking_id', 'gmail',
                    'gender', 'date', 'gmail_password', 'pak_visa_password', 'user_id'
                );
            }])
            ->get();
    }

    return Inertia::render('Referral/Details', [
        'referral'     => $referral,
        'transactions' => $transactions,
        'individualVisas' => $individualVisas,
        'familyVisas' => $familyVisas,
    ]);
}


}
