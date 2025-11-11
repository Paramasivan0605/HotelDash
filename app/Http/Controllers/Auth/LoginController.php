<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Location;
use App\Models\User;

class LoginController extends Controller
{
    public function index()
    {
        $locations = Location::get();
        if (Auth::check()) {
            if (Auth::user()->role == 1) {
                return view('company.admin.dashboard');
            }

            if (Auth::user()->role == 2) {
                return view('company.staff.dashboard');
            }
        }
        else {
            return view('company.auth.login',compact('locations'));
        }
    }
    
    /**
     * Handle an authentication attempt
     */
    public function authenticate(Request $request) : RedirectResponse
    {
        $credentials = $request->validate([
            'staff_id' => ['required'],
            'password' => ['required'],
        ]);
        
        $user = User::where('staff_id', $credentials['staff_id'])->first();

        // If user exists and is staff (role 2), validate location
        if ($user && $user->role == 2) {  
            // Then check if it exists
            $locationExists = Location::where('location_id', $request->location)->exists();
            if (!$locationExists) {
                return back()->withErrors([
                    'error-message' => 'Please Choose Location.'
                ])->withInput();
            }
        }

        if (Auth::attempt($credentials)) {

            if (Auth::user()->role == 1) {
                $request->session()->regenerate();
                return redirect()->route('admin-dashboard')->with('success-message', 'Login Successful.');
            }
            
            if (Auth::user()->role == 2) {
                session(['location_id' => $request->location]);          
                $request->session()->regenerate();
                return redirect()->route('staff-dashboard')->with('success-message', 'Login Successful.');
            }
        }

        return back()->withErrors([
            'error-message' => 'The provided credentials do not match our records.'
        ])->onlyInput('staff_id');
    }

    public function logout(Request $request) : RedirectResponse
    {
        $request->session()->forget('location_id');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
