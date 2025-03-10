<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HomeController extends Controller
{

    public function index()
    { 
        return Inertia::render('Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();
            if ($user) {
                $auth_token = $user->createToken('webToken')->plainTextToken;
                session()->put('auth_token', $auth_token);

                return 'success';
            } else {
                return response()->json([
                    'errors' => [
                        'email' => [('The provided email is not verified in our records.')],
                    ],
                    'message' => ("Please verify your email."),
                ], 422);
            }
        }
        return response()->json([
            'errors' => [
                'email' => [('The provided credentials do not match our records.')],
            ],
            'message' => ("The given data was invalid."),
        ], 422);
    }

    public function user_register(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|unique:users',
            'password' => 'required|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|required_with:password|same:password',
        ]);
        $User = new User;
        $User->name = $request->user_name;
        $User->email = $request->email;
        $User->role = "user";
        $User->password = Hash::make($request->password);
        $User->save();
        $url = url('/');
        $token = Str::random(64);
        $link = $url . '/verify-email/' . $token . '?email=' . urlencode($request->email);
        $dataforEmail = [
            'verify_email' => $link,
        ];

        $user_email = $request->email;
        $mail_subject = ('TTC Global account verification email.');
        Mail::send('Emails.mail', $dataforEmail, function ($message) use ($user_email, $mail_subject) {
            $message->to($user_email)->subject($mail_subject);
        });
        return 'success';
    }

    public function verify_email(Request $request)
    {
        $verified_user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();
        if ($verified_user) {
            return redirect('errors/404-page');
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->email_verified_at = Carbon::now();
                $user->save();
                return Inertia::render('Backend/Login', [
                    'message' => ("Email verified successfully now you can login."),
                ]);
            }
        }
    }

    public function forgot_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);
        //Get the token just created above
        $verifying = DB::table('users')->where('email', $request->email)->whereNotNull('email_verified_at')->first();
        if ($verifying) {
            $tokenData = DB::table('password_resets')->where('email', $request->email)->first();
            if ($this->sendResetEmail($request->email, $tokenData->token)) {
                return 'success';
            }
        } else {
            return response()->json([
                'errors' => [
                    'email' => [('The provided email is not verified in our records please first verify email.')],
                ],
                'message' => ("Please verify your email."),
            ], 422);
        }
    }

    private function sendResetEmail($email, $token)
    {
        //Retrieve the user from the database
        $user = DB::table('users')->where('email', $email)->select('name', 'email')->whereNotNull('email_verified_at')->first();
        //Generate, the password reset link. The token generated is embedded in the link
        $url = url('/');
        $link = $url . '/password-reset/' . $token . '?email=' . urlencode($user->email);
        $dataforEmail = [
            'link' => $link,
        ];
        $user_email = $user->email;
        $mail_subject = ('Portafolio account password reset.');
        // return view('emails.schedule_tasks', compact('user', 'events_and_tasks'));
        Mail::send('Emails.mail', $dataforEmail, function ($message) use ($user_email, $mail_subject) {
            $message->to($user_email)->subject($mail_subject);
        });
    }
    public function resetPasswordVue(Request $request)
    {
        $tokenDataValidate = DB::table('password_resets')
            ->where('token', $request->token)->where('email', $request->email)->first();
        $user = User::where('email', $request->email)->first();
        if ($tokenDataValidate) {
            return Inertia::render('Backend/resetPassword', [
                'user_id' => $user->id,
            ]);
        } else {
            return redirect('errors/404-page');
        }
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'password' => 'required|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|required_with:password|same:password',
        ]);
        $user = User::findOrFail($request->user_id);
        $tokenDataValidate = DB::table('password_resets')->where('email', $user->email)->first();
        $user->password = Hash::make($request->password);
        $user->token = $tokenDataValidate->token;
        $user->save();
        DB::table('password_resets')->where('email', $user->email)->delete();
        return Inertia::render('Backend/Login', [
            'message' => ("Password reset successfully now you can login."),
        ]);
    }

    public function logout_user(Request $request)
    {

        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }

        Auth::guard('web')->logout();
        $request->session()->flush();
        session()->forget('auth_token');
    }

    public function forgotPassword()
    {
        return Inertia::render('Backend/forgotPassword');
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|max:255',
            'new_password' => 'required|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|required_with:new_password|same:new_password',
        ]);
        $data = $request->all();
        $user = User::find(auth()->user()->id);
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => [('The provided current Password do not match our records.')],
                ],
                'message' => ("The given data was invalid."),
            ], 422);
        } else {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
            return 'success';
        }
    }

    public function admin_setting()
    {
        return Inertia::render('Backend/Profile');
    }
    public function show_porfile($id)
    {
        $profile = User::where('id', $id)->first();
        $abc = $profile->name;
        return Inertia::render('Backend/Profile', [
            'profile' => 123,
        ]);
    }
    public function setting()
    {
        $userId = auth()->user()->id;
        return Inertia::render('Backend/Profile', [
            'userId' => $userId,
        ]);
    }

    public function UserprofileShow()
    {
        $userId = User::findOrFail(auth()->user()->id);
        return Inertia::render('Backend/Talents/Talent_Profile', [
            'userId' => $userId,
        ]);
    }

    public function profile_update(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
        ]);
        $User = User::findOrFail(auth()->user()->id);
        $User->name = $request->user_name;
        $User->save();
        return 'success';
    }

}
