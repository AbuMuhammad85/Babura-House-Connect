<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Redirect;
use App\Helpers\Flash;

class AuthController extends BaseController
{
    public function login(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('public/login', [
            'title' => 'Login - Babura House Connect'
        ]);
    }

    public function handleLogin(Request $request, Response $response)
    {
        $body = $request->getBody();
        $email = $body['email'] ?? '';
        $role = $body['role'] ?? 'tenant';

        // Mock User profiles session
        $user = [
            'email' => $email,
            'role' => $role,
        ];

        if ($role === 'landlord') {
            $user['name'] = 'Alhaji Ibrahim Babura';
            $user['verified'] = true; // Set to true to satisfy VerifiedLandlordMiddleware
        } elseif ($role === 'admin') {
            $user['name'] = 'System Admin';
        } else {
            $user['name'] = 'Garba Danladi';
        }

        Auth::login($user);
        Flash::set('success', 'Logged in successfully as ' . $user['name'] . '.');

        return Redirect::to("/{$role}/dashboard");
    }

    public function register(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('public/register', [
            'title' => 'Register - Babura House Connect'
        ]);
    }

    public function handleRegister(Request $request, Response $response)
    {
        Flash::set('success', 'Registration submitted. Please log in.');
        return Redirect::to('/login');
    }

    public function forgotPassword(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('public/forgot_password', [
            'title' => 'Forgot Password - Babura House Connect'
        ]);
    }

    public function handleForgotPassword(Request $request, Response $response)
    {
        return $this->render('public/forgot_password', [
            'title' => 'Forgot Password - Babura House Connect',
            'success' => 'A password reset link has been sent to your email address.'
        ]);
    }

    public function logout(Request $request, Response $response)
    {
        Auth::logout();
        return Redirect::to('/');
    }
}
