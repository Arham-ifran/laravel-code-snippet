<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        return view('auth.login');
    }

    /**
     * Display the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function registration()
    {
        return view('auth.registration');
    }

    /**
     * Process a user's login attempt.
     * Date: 18 Sep, 2023
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Get the user's login credentials from the request
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->withSuccess('You have Successfully logged in');
        }

        // If authentication fails, store an error message in session flash data
        return redirect("/")
            ->withErrors(['message' => 'Oppes! You have entered invalid credentials']);
    }

    /**
     * Process a user's registration attempt.
     * Date: 18 Sep, 2023
     */
    public function postRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        // Create a new user record in the database
        $user = $this->create($data);

        return redirect("/")->withSuccess('Great! You have Successfully loggedin');
    }

    /**
     * Display the user's dashboard if authenticated, or redirect to the login page.
     * Date: 18 Sep, 2023
     */
    public function dashboard()
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            return view('dashboard');
        }

        // Redirect to the login page with an error message for unauthenticated users
        return redirect("/")
            ->withErrors(['message' => 'Opps! You do not have access']);
    }

    /**
     * Create a new user instance after a valid registration.
     * Date: 18 Sep, 2023
     */
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    /**
     * Log the user out of the application.
     * Date: 18 Sep, 2023
     */
    public function logout()
    {
        // Flush the user's session data
        Session::flush();
        // Log the user out
        Auth::logout();
        // Redirect the user to the login page after logout
        return redirect('/');
    }
}
