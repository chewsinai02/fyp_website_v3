<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validate the inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log the user in with email and password
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Retrieve the authenticated user
            $user = Auth::user();

            // Redirect based on user role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard'); // Admin dashboard
                case 'doctor':
                    return redirect()->route('doctor_dashboard'); // Doctor dashboard
                case 'nurse_admin':
                    return redirect()->route('nurseadminDashboard'); // Nurse admin dashboard
                case 'nurse':
                    return redirect()->route('nurseDashboard'); // Nurse dashboard
                case 'patient':
                    return redirect()->route('patientDashboard'); // Patient dashboard
                default:
                    return redirect()->intended(RouteServiceProvider::HOME);
            }
        }

        // If authentication fails
        throw ValidationException::withMessages([
            'email' => 'The provided credentials are incorrect.',
        ]);
    }
}
