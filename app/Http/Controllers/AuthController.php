<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function index()
    {
        return back();
    }

    public function login()
    {
        return view('auth.login', [
            'title' => 'Login'
        ]);
    }

    public function register()
    {
        return view('auth.register', [
            'title' => 'Register'
        ]);
    }

    public function attemptLogin(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => ''
        ]);

        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $request->merge([
            $login_type => $request->input('login')
        ]);

        if (
            Auth::attempt(
                $request->only($login_type, 'password'),
                $request->remember
            )
        ) {
            $request->session()->regenerate();

            User::setCart(auth()->user()->id);

            // Update user status > Online
            User::setStatus(auth()->user()->id, 1);

            redirect()->intended('/cart');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function storeRegister(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|alpha_dash|max:100|unique:users',
            'email' => 'required|email:rfc|unique:users',
            'phone' => 'required|unique:users|max:15|min:6',
            'password' => ['required', 'min:8', Password::min(8)->mixedCase()->letters()->numbers()->uncompromised()],
            'confirm_password' => 'required|same:password'
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = User::create($validatedData);

        $user->sendEmailVerificationNotification();
        Auth::login($user, true);

        // Update user status > Online
        User::setStatus($user->id, status: 1);

        // Initiate Cart For User
        User::setCart($user->id);
        Wishlist::createWishlist($user->id, 'General');

        return redirect()->to('/home')->with('msg', ['status' => 'success', 'title' => 'Register Successfull!', 'body' => "Welcome Aboard, " . $user->name]);
    }

    public function logout(Request $request)
    {
        if ($request->token !== csrf_token())
            return redirect('/')->with('alert', 'Logout Failed! Please Try Again Later.');

        User::setStatus(auth()->user()->id, 0);
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->intended('/')
            ->with('alert', "You Are Logged Out, Please Login Using Your Credentials");
    }

    public function forgot()
    {
        return back();
    }
}
