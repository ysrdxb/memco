<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\SendOtpNotification;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attemptLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Generate and send OTP
            $otp = rand(100000, 999999);
            $user->update(['otp' => $otp]);
            $user->notify(new SendOtpNotification($otp)); // Use notify() method

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid email or password']);
    }

    /**
     * Verify the OTP and log the user in if valid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);
    
        $user = User::where('otp', $request->otp)->first();
    
        if ($user) {
            $user->update(['otp' => null]); // Clear OTP
    
            // Check if the user has any projects
            if ($user->projects()->exists()) {
                // Check if the user already has a current_project_id
                if ($user->current_project_id) {
                    session(['current_project_id' => $user->current_project_id]);
                } else {
                    // Set session to the first project ID if no current_project_id is set
                    $firstProject = $user->projects()->first();
                    if ($firstProject) {
                        $user->update(['current_project_id' => $firstProject->id]);
                        session(['current_project_id' => $firstProject->id]);
                    }
                }
            }
    
            Auth::login($user);
            return response()->json(['status' => 'success', 'redirect' => $this->redirectTo]);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Invalid OTP']);
    }    

    /**
     * Logout the user and invalidate the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/login');
    }
}
