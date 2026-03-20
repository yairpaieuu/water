<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        // Redirect already-authenticated users to the dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->render('auth.login', [
            'csrfField' => CSRF::field(),
            'error'     => Session::getFlash('error'),
            'success'   => Session::getFlash('success'),
        ], 'auth');
    }

    public function login(): void
    {
        // Verify CSRF token
        $request = new Request();
        $token   = $request->post('_csrf_token', '');

        if (!CSRF::verify($token)) {
            Session::flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/login');
        }

        $email    = $request->post('email', '');
        $password = $request->post('password', '');

        // Basic validation
        $validator = new Validator();
        $errors    = $validator->validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required|min:6']
        );

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            Session::flash('old_email', $email);
            $this->redirect('/login');
        }

        if (!Auth::login($email, $password)) {
            Session::flash('error', 'Invalid email or password.');
            Session::flash('old_email', $email);
            $this->redirect('/login');
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('success', 'You have been logged out successfully.');
        $this->redirect('/login');
    }
}
