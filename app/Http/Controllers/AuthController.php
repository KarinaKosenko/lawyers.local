<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

/**
 * Class AuthController - custom class for Registration and Authentication.
 */
class AuthController extends Controller
{
    /**
     * Method for getting register form page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function register(Request $request)
	{
		return view('layouts.single', [
			'title' => 'Registration',
			'page' => 'pages.registrationPage',
		]);
	}

    /**
     * Method for user validation and registration.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function registerPost(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:200|min:2',
			'address' => 'required|min:10',
			'email' => 'required|email|unique:users|max:200',
			'phone' => 'required|numeric',
			'birthday' => 'required|date_format:"d-m-Y"',
			'date' => 'required|date_format:"d-m-Y"',
			'password' => 'required|max:200|min:6',
			'password2' => 'required|same:password',
			'is_confirmed' => 'accepted'
		]);
		
		
		$user = User::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'birthday' => $request->input('birthday'),
            'date' => $request->input('date'),
            'password' => bcrypt($request->input('password')),
            'created_at' => Carbon::createFromTimestamp(time())
                ->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::createFromTimestamp(time())
                ->format('Y-m-d H:i:s'),
        ]);
		
		return redirect("/upload/" . $user->id);
	}

    /**
     * Method for getting login form page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function login()
	{
	    return view('layouts.single', [
			'title' => 'Log In',
			'page' => 'pages.loginPage',
		]);
	}

    /**
     * Method for user authentication.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function loginPost(Request $request)
	{
		$remember = $request->input('remember') ? true : false;
		
		$authResult = Auth::attempt([
			'email' => $request->input('email'),
			'password' => $request->input('password'),
		], $remember);
		
		if ($authResult) {
			return redirect()->route('public.profiles.index');
		} 
		else {
			return redirect()
				->route('public.auth.login')
				->with('authError', trans('custom.wrong_password'));
		}
	}

    /**
     * Method for log out.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
	public function logout()
	{
		Auth::logout();
		return redirect()->route('public.profiles.index');
	}

    /**
     * Method for getting Terms adn Conditions page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function conditions()
    {
        return view('layouts.single', [
            'title' => 'Terms and Conditions',
            'page' => 'pages.conditionsPage',
        ]);
    }
}