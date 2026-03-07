<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }


    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function checkEmail(Request $request)
    {
        $email = $request->email;
        $exists = User::where('email', $email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function register(Request $request)
    {
        $badWords = ['badword1', 'badword2', 'spam', 'admin', 'root']; // Example list

        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($badWords) {
            foreach ($badWords as $word) {
                if (stripos($value, $word) !== false) {
                    $fail('The name contains inappropriate language.');
                }
            }
        },
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('status', 'Registration successful! Please login to continue.');
    }



    public function showForgotPasswordForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find a user with that email address.');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        [
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]
        );

        // In a real app, you'd send an actual email here. 
        // For now, we will log it and simulate success if mailer is set to log.
        // We'll use the built-in notification if possible, but manual is safer for custom logic.

        try {
            $url = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

            // Sending standard Laravel password reset notification
            $user->sendPasswordResetNotification($token);

            return back()->with('status', 'We have e-mailed your password reset link!');
        }
        catch (\Throwable $e) {
            Log::error('Mail Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send email. Please check your mail configuration. Error: ' . $e->getMessage());
        }
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
        ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return back()->with('error', 'Invalid token or email.');
        }

        if (!Hash::check($request->token, $reset->token)) {
            return back()->with('error', 'Invalid token or email.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('status', 'Your password has been reset!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
