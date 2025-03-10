<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('User/Index');
    }

    public function store(Request $request)
    {
        
        if($request->user_id)
        {
            $request->validate([
                'user_name' => 'required|string|max:255',
                'role' => 'required', 
                'email'=> 'required|unique:users,email,' . $request->user_id, 
            ]);
            $User = User::findOrFail($request->user_id);
        }else
        {
            $request->validate([
                'user_name' => 'required|string|max:255',
                'role' => 'required', 
                'email'=> 'required|unique:users,email',
                'password' => 'required|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required|required_with:password|same:password',
            ]);
            $User = new User;

        }
        $User->name = $request->user_name;
        $User->email = $request->email;
        $User->role = $request->role;
        $User->password = Hash::make($request->password);
         
        $User->save();
 
        // $website_name = Setting::where('type', 'website_name')->first();
        // $url = url('/');
        // $token = Str::random(64);
        // $link = $url . '/verify-email/' . $token . '?email=' . urlencode($request->email);
        // $dataforEmail = [
        //     'verify_email' => $link,
        //     'website_name' => $website_name->value,
        //     'user_email' => $request->email,
        //     'password' => $request->password,
        // ];
        // $user_email = $request->email;
        // $mail_subject = translate('Account verification email.');
        // Mail::send('Emails.mail', $dataforEmail, function ($message) use ($user_email, $mail_subject) {
        //     $message->to($user_email)->subject($mail_subject);
        // });

        return 'success';

    }


    public function users_fetch()
    {

        $users = User::all();
        $users_ids = $users->pluck('id');

        return [
            'users' => $users,
            'users_ids' => $users_ids,
        ];

    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }

    public function delete($id)
    {

        $user = User::findOrFail($id);
        $user->delete();
        return 'success';
    }
}
