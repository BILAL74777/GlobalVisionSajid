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

        $visa->family_name = null;
        // $visa->cash_in = 0;

        // $visa->phone_number    = null;
        $visa->family_members = 1;
        $visa->entry_type     = 'Individual';
        $visa->save();

        // Case when there is a referral in the request
        if ($request->status == 'Refunded') {
            // check the referralAcount and employee acount using the visa id and delte those tables records completely and update the status value in the visa table and make empty th referaal .
            $referralAccount = ReferralAccount::where('visa_id', $request->id)->first();
            if ($referralAccount) {
                $referralAccount->delete();
            }
            EmployeeAccount::where('visa_id', $request->id)->delete();

            // $employeeAccount = EmployeeAccount::where('visa_id', $request->id)->first();
            // if ($employeeAccount) {
            //     $employeeAccount->delete();
            // }
            $visa->referral = null;

            $visa->save();

            return 'success';
        } else if ($request->referral) {
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
                $referralAccount->parent_id             = $visa->id;
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
                    $referralAccount->parent_id             = $visa->id;
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
                    $employeeAccount->parent_id             = $visa->id;
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
                // Get all employee records
                $employee_records = Employee::select('id', 'commission')->get();
                $profitAmount     = $request->amount - $request->visa_fee;

                foreach ($employee_records as $employee) {
                    $employeeCommissionAmount = ($profitAmount * $employee->commission) / 100;

                    // Check if the record exists
                    $existingRecord = EmployeeAccount::where('visa_id', $visa->id)
                        ->where('employee_id', $employee->id)
                        ->first();

                    if ($existingRecord) {
                        // Update existing record
                        $existingRecord->cash_in             = $request->amount;
                        $existingRecord->cash_out            = $request->visa_fee;
                        $existingRecord->amount              = $employeeCommissionAmount;
                        $existingRecord->employee_commission = $employee->commission . "%";
                        $existingRecord->save();
                    } else {
                        // No existing record, create a new one
                        $employeeAccount                      = new EmployeeAccount();
                        $employeeAccount->visa_id             = $visa->id;
                        $employeeAccount->cash_in             = $request->amount;
                        $employeeAccount->cash_out            = $request->visa_fee;
                        $employeeAccount->employee_id         = $employee->id;
                        $employeeAccount->amount              = $employeeCommissionAmount;
                        $employeeAccount->employee_commission = $employee->commission . "%";
                        $employeeAccount->save();
                    }
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
        // Validate family data
        if (empty($request->family_forms) || ! is_array($request->family_forms)) {
            return response()->json(['message' => 'Invalid family data provided!'], 400);
        }

                                                              // Save the primary member in `visas` table
        $primaryMember           = $request->family_forms[0]; // First member is the primary member
        $visa                    = new Visa();
        $visa->family_name       = $request->family_name;
        $visa->phone_number      = $request->phone_number;
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

        // Process ReferralAccount or EmployeeAccount for the primary member
        if ($request->referral) {
            // Fetch referral data
            $referralData = Referral::select('id', 'commission')->where('id', $request->referral)->first();
            if ($referralData) {
                // Save ReferralAccount for the primary member
                $referralAccount                      = new ReferralAccount();
                $referralAccount->visa_id             = $visa->id;
                $referralAccount->parent_id             = $visa->id;
                $referralAccount->cash_in             = $primaryMember['amount'];   // Member's amount
                $referralAccount->cash_out            = $primaryMember['visa_fee']; // Member's visa fee
                $referralAccount->referral_id         = $referralData->id;
                $referralAccount->commission_amount   = ($primaryMember['amount'] - $primaryMember['visa_fee']) * ($referralData->commission / 100); // Commission for this member
                $referralAccount->referral_commission = $referralData->commission . "%";                                                             // Store referral commission percentage
                $referralAccount->save();
            }
        } else {
            // Process EmployeeAccount for the primary member if no referral exists
            $employeeRecords = Employee::select('id', 'commission')->get();

            foreach ($employeeRecords as $employee) {
                // Save EmployeeAccount for the primary member
                $employeeAccount                      = new EmployeeAccount();
                $employeeAccount->visa_id             = $visa->id;
                $employeeAccount->parent_id             = $visa->id;
                $employeeAccount->cash_in             = $primaryMember['amount'];   // Member's amount
                $employeeAccount->cash_out            = $primaryMember['visa_fee']; // Member's visa fee
                $employeeAccount->employee_id         = $employee->id;
                $employeeAccount->amount              = ($primaryMember['amount'] - $primaryMember['visa_fee']) * ($employee->commission / 100); // Commission for this member
                $employeeAccount->employee_commission = $employee->commission . "%";                                                             // Store employee commission percentage
                $employeeAccount->save();
            }
        }

        // Process each family member
        foreach ($request->family_forms as $index => $member) {
            if ($index === 0) {
                // Skip primary member since it's already processed
                continue;
            }

            // Insert family member into `family_visas`
            $familyVisa                    = new FamilyVisa();
            $familyVisa->visa_id           = $visa->id; // Linking to primary visa
            $familyVisa->full_name         = $member['full_name'];
            $familyVisa->phone_number      = $member['phone_number'];
            $familyVisa->status            = $member['status'];
            $familyVisa->amount            = $member['amount'];
            $familyVisa->visa_fee          = $member['visa_fee'];
            $familyVisa->tracking_id       = $member['tracking_id'];
            $familyVisa->gmail             = $member['gmail'];
            $familyVisa->pak_visa_password = $member['pak_visa_password'];
            $familyVisa->gmail_password    = $member['gmail_password'];
            $familyVisa->gender            = $member['gender'];
            $familyVisa->date              = $member['date'];
            $familyVisa->user_id           = auth()->user()->id;
            $familyVisa->referral          = $request->referral;
            $familyVisa->save();

            // Process ReferralAccount for this family member if referral exists
            if ($request->referral) {
                $referralData = Referral::select('id', 'commission')->where('id', $request->referral)->first();
                if ($referralData) {
                    // Directly save ReferralAccount for this family member
                    $referralAccount                      = new ReferralAccount();
                    $referralAccount->visa_id             = $familyVisa->id;
                    $referralAccount->parent_id             = $visa->id;
                    $referralAccount->cash_in             = $member['amount'];   // Store member's amount
                    $referralAccount->cash_out            = $member['visa_fee']; // Store member's visa fee
                    $referralAccount->referral_id         = $referralData->id;
                    $referralAccount->commission_amount   = ($member['amount'] - $member['visa_fee']) * ($referralData->commission / 100); // Commission for this member
                    $referralAccount->referral_commission = $referralData->commission . "%";                                               // Store referral commission percentage
                    $referralAccount->save();
                }
            } else {
                // Process EmployeeAccount for this family member if no referral exists
                $employeeRecords = Employee::select('id', 'commission')->get();

                foreach ($employeeRecords as $employee) {
                    // Directly save EmployeeAccount for this family member
                    $employeeAccount                      = new EmployeeAccount();
                    $employeeAccount->visa_id             = $familyVisa->id;
                    $employeeAccount->parent_id             = $visa->id;
                    $employeeAccount->cash_in             = $member['amount'];   // Store member's amount
                    $employeeAccount->cash_out            = $member['visa_fee']; // Store member's visa fee
                    $employeeAccount->employee_id         = $employee->id;
                    $employeeAccount->amount              = ($member['amount'] - $member['visa_fee']) * ($employee->commission / 100); // Commission for this member
                    $employeeAccount->employee_commission = $employee->commission . "%";                                               // Store employee commission percentage
                    $employeeAccount->save();
                }
            }
        }

        // Response to indicate success
        return response()->json(['message' => 'Family Visa data saved successfully!', 'primary_visa' => $visa], 201);
    }

    public function updateFamilyRecord(Request $request)
    {
        // Check if a Visa or FamilyVisa is being updated
        $visa = null;

        // dd($request);
        if ($request->has('visa_id') && $request->visa_id) {

            // If 'visa_id' exists, update the FamilyVisa record
            $visa = FamilyVisa::find($request->id);
        } else {

            // Otherwise, update the Visa record
            $visa = Visa::find($request->id);
        }

        // Check if the visa record exists
        if (! $visa) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Update Visa or FamilyVisa record with the new data
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

        // Save the changes to the Visa or FamilyVisa record
        $visa->save();

        // Process ReferralAccount if referral exists in the request
       
        if ($request->referral) {
            // Find the ReferralAccount associated with the visa_id and referral_id
            $referralAccount = ReferralAccount::where('visa_id', $visa->id)
                ->where('referral_id', $request->referral)
                ->first();

            if ($referralAccount) {

                // If status is 'Refunded', adjust the cash_in and cash_out
                if ($request->status == 'Refunded') {
                    $referralAccount->delete();

                } else {
                    // dd("test");

                    // If not refunded, calculate the commission amount and update the ReferralAccount
                    $referralCommission       = (float) str_replace('%', '', $referralAccount->referral_commission);
                    $profitAmount             = $request->amount - $request->visa_fee;
                    $referralCommissionAmount = ($profitAmount * $referralCommission) / 100;

                                                                      // Update the ReferralAccount with the new values
                    $referralAccount->cash_in = $request->amount;    // Add new amount to cash_in
                    $referralAccount->cash_out = $request->visa_fee; // Add new visa_fee to cash_out
                    $referralAccount->commission_amount = $referralCommissionAmount;

                    // Save the updated ReferralAccount
                    $referralAccount->save();
                }
            } else {
                if ($request->status == 'Refunded') {
                    $referralAccount = ReferralAccount::where('visa_id', $visa->id)
                ->where('referral_id', $request->referral)
                ->first();
                if($referralAccount)
                {

                    $referralAccount->delete();
                }

                }else
                {
                // If no ReferralAccount exists, create a new one
                $referralData = Referral::select('id', 'commission')->where('id', $request->referral)->first();
                // dd("Test",$request->referral,$referralData,$visa->id);
                if ($referralData) {
                    $profitAmount             = $request->amount - $request->visa_fee;
                    $referralCommissionAmount = ($profitAmount * $referralData->commission) / 100;

                    // Store new ReferralAccount data
                    $referralAccount                      = new ReferralAccount();
                    $referralAccount->visa_id             = $visa->id;
                    // $referralAccount->parent_id             = $visa->id;
                    $referralAccount->cash_in             = $request->amount;
                    $referralAccount->cash_out            = $request->visa_fee;
                    $referralAccount->referral_id         = $referralData->id;
                    $referralAccount->commission_amount   = $referralCommissionAmount;
                    $referralAccount->referral_commission = $referralData->commission . "%";
                    $referralAccount->save();
                }
                }
            }
        } else {  
            // If no referral exists, update EmployeeAccount records for all employees
            $employeeRecords = EmployeeAccount::where('visa_id', $visa->id)->get();
            if ($employeeRecords->isNotEmpty()) {
                // dd("testin gin ",$employeeRecords);
                // dd("test",$employeeRecords);

                foreach ($employeeRecords as $employeeAccount) {
                    // If status is 'Refunded', subtract the requested amount from cash_in for each employee
                    // dd("indside");
                    if ($request->status == 'Refunded') {
                        $employeeAccount->delete();
                        // dd("test",$employeeAccount);

                    } else {
                        // Otherwise, update the EmployeeAccount for the employee based on the new values
                        
                        $employee_commission       = (float) str_replace('%', '', $employeeAccount->employee_commission);

                        $profitAmount             = $request->amount - $request->visa_fee;
                        $employeeCommissionAmount = ($profitAmount * $employee_commission) / 100;
                        // Update the cash_in, cash_out, and amount for each employee
                        $employeeAccount->cash_in  = $request->amount;
                        $employeeAccount->cash_out = $request->visa_fee;
                        $employeeAccount->amount   = $employeeCommissionAmount;
                        $employeeAccount->save();
                    }

                    // Save the updated EmployeeAccount
                }
            } else {
                // dd("test- 1",$employeeRecords);
                // If no EmployeeAccount exists, create a new one
                $employeeRecords = Employee::select('id', 'commission')->get();
                $profitAmount    = $request->amount - $request->visa_fee;

                foreach ($employeeRecords as $employee) {
                    $employee_commission       = (float) str_replace('%', '', $employee->commission);
                    // dd($employee_commission);
                    $employeeCommissionAmount = ($profitAmount * $employee_commission) / 100;

                    // Check if the record exists
                    $existingRecord = EmployeeAccount::where('visa_id', $visa->id)
                        ->where('employee_id', $employee->id)
                        ->first();

                    if ($existingRecord) {
                        // Update existing record
                        $existingRecord->cash_in             = $request->amount;
                        $existingRecord->cash_out            = $request->visa_fee;
                        $existingRecord->amount              = $employeeCommissionAmount;
                        $existingRecord->employee_commission = $employee->commission . "%";
                        $existingRecord->save();
                    } else {
                        // No existing record, create a new one
                        $employeeAccount                      = new EmployeeAccount();
                        $employeeAccount->visa_id             = $visa->id;
                        $employeeAccount->cash_in             = $request->amount;
                        $employeeAccount->cash_out            = $request->visa_fee;
                        $employeeAccount->employee_id         = $employee->id;
                        $employeeAccount->amount              = $employeeCommissionAmount;
                        $employeeAccount->employee_commission = $employee->commission . "%";
                        $employeeAccount->save();
                    }
                }
            }
        }

        // Update the visa referral field if necessary
        $visa->referral = $request->referral ?? null;
        $visa->save();

        // Return success response with the updated visa record
        return response()->json([
            'message' => 'Record updated successfully',
            'data'    => $visa,
        ], 200);
    }

    public function getIndividualVisas()
    {
        // Fetch individual visa records
        // $individualVisas = Visa::where('entry_type', 'Individual')->orderByDesc('date')->get();
        $individualVisas = Visa::where('entry_type', 'Individual')
        ->orderByDesc('date') 
        ->orderByDesc('id')
        ->get();
         

        foreach ($individualVisas as $record) {
            if ($record->referral) {

                $referral              = Referral::where('id', $record->referral)->first();
                $record->referral_name = $referral->name ?? "";
                $record->referral_id   = $referral->id ?? "";

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
            ->orderByDesc('date')
            ->orderByDesc('id') 
            ->get();

        // Attach referral name, family name, and family members count
        foreach ($familyVisas as $record) {
            $referral = '';
            // Handle referral name safely
            if (! empty($record->referral)) {
                $referral              = Referral::find($record->referral);
                $record->referral_name = $referral ? $referral->name : "Unknown Referral";
                $referral              = $referral->id;
            } else {
                $record->referral_name = "No Referral";
            }

            // Store referral_id in the main record
            $record->referral = $referral;

            // Now pass the referral_id to each family member
            foreach ($record->familyMembers as $familyMember) {
                $familyMember->referral = $referral; // Assign referral_id to each family member
            }

            $referralAccount = ReferralAccount::where('visa_id', $record->id)->first();
            if ($referralAccount) {
                $record->visa_fee = $referralAccount->cash_out;
            }

            // Set family name (use primary member's name if not set)
            $record->family_name  = $record->family_name ?? $record->full_name;
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
        // Check if an ID is provided for update
        if ($request->id) {
            $referral = Referral::find($request->id);                   // Use find() instead of where()->first()
            $User     = User::where('email', $request->email)->first(); // Corrected to get the first user with the email

            // Validate request
            $request->validate([
                'name'    => 'required|string|max:255',
                'phone'   => 'required|string|max:20',
                'email'   => 'required|email|unique:users,email,' . ($User ? $User->id : ''), // Check if user exists and exclude their ID
                'address' => 'nullable|string|max:500',
            ]);

            if (! $User) {
                $User                    = new User();
                $User->password          = Hash::make($request->phone);
                $User->email_verified_at = now();
            }
        } else {
            // Validate request for new referral
            $request->validate([
                'name'    => 'required|string|max:255',
                'phone'   => 'required|string|max:20',
                'email'   => 'required|email|unique:users,email', // Email must be unique for new users
                'address' => 'nullable|string|max:500',
            ]);

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

        // Save the user and referral records
        $User->save();
        $referral->save();

        return 'success';
    }

    public function refferrals_pluck()
    {
        return Referral::pluck('name', 'id');
    }
    public function referral_details($id)
    {
        // Fetch referral details
        $referral = Referral::where('id', $id)->first();
    
        // Fetch transactions related to this referral
        $transactions = ReferralAccount::where('referral_id', $id)->get();
    
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
    

    

}
