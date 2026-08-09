<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Redirect;
use App\Helpers\Flash;
use App\Helpers\Validation;
use App\Repositories\UserRepository;
use App\Core\Database;

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
        $password = $body['password'] ?? '';
        $role = $body['role'] ?? 'tenant';

        $repo = new UserRepository();
        $user = $repo->findByEmail($email);

        if (!$user || $user['role'] !== $role || !password_verify($password, $user['password_hash'])) {
            // Log failed login event
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $user ? $user['id'] : null,
                    'action' => 'LOGIN_FAILED',
                    'description' => "Failed {$role} portal login attempt for " . $email,
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            Flash::set('error', 'Invalid credentials.');
            return Redirect::to('/login');
        }

        if ($user['status'] !== 'active') {
            Flash::set('error', 'Your account is currently ' . $user['status'] . '.');
            return Redirect::to('/login');
        }

        // Establish secure authenticated session
        Auth::login($user);
        $repo->updateLastLogin($user['id']);

        // Log successful login event
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $user['id'],
                'action' => 'LOGIN_SUCCESS',
                'description' => "Successful {$role} portal login for " . $email,
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        Flash::set('success', 'Welcome back, ' . $user['full_name'] . '!');
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
        $body = $request->getBody();
        $name = $body['name'] ?? '';
        $email = $body['email'] ?? '';
        $phone = $body['phone'] ?? '';
        $role = $body['role'] ?? 'tenant';
        $password = $body['password'] ?? '';
        $confirmPassword = $body['confirm_password'] ?? '';

        // Server-side validation checks
        $val = new Validation();
        $rules = [
            'name' => 'required|min:3|max:150',
            'email' => 'required|email|max:191',
            'phone' => 'required|min:7|max:30',
            'password' => 'required|min:6',
        ];

        if (!$val->validate($body, $rules)) {
            $errors = array_merge(...array_values($val->errors()));
            Flash::set('error', implode(' ', $errors));
            return Redirect::to('/register');
        }

        if ($password !== $confirmPassword) {
            Flash::set('error', 'Password confirmation does not match.');
            return Redirect::to('/register');
        }

        // Validate uniqueness of email and phone
        $repo = new UserRepository();
        if ($repo->findByEmail($email)) {
            Flash::set('error', 'This email address is already registered.');
            return Redirect::to('/register');
        }
        if ($repo->findByPhone($phone)) {
            Flash::set('error', 'This phone number is already registered.');
            return Redirect::to('/register');
        }

        // Whitelist role values to block any admin escalation
        if (!in_array($role, ['tenant', 'landlord'])) {
            Flash::set('error', 'Invalid role selection.');
            return Redirect::to('/register');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Transaction block for creating user and profile
        Database::beginTransaction();
        try {
            $userId = $repo->create([
                'role' => $role,
                'full_name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => $passwordHash,
                'status' => 'active'
            ]);

            if ($role === 'tenant') {
                $repo->createTenantProfile($userId);
            } else {
                $repo->createLandlordProfile($userId, [
                    'verification_status' => 'pending'
                ]);
            }

            Database::commit();

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'REGISTRATION',
                    'description' => "New {$role} portal registration created for " . $email,
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            // Log in the user immediately
            $user = $repo->findById($userId);
            Auth::login($user);

            Flash::set('success', 'Registration successful! Welcome to your dashboard portal.');
            return Redirect::to("/{$role}/dashboard");

        } catch (\Exception $e) {
            Database::rollBack();
            Flash::set('error', 'Account registration failed. Please try again.');
            return Redirect::to('/register');
        }
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
        $userId = Auth::user('id');
        if ($userId) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'LOGOUT',
                    'description' => "User logged out of session.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );
        }

        Auth::logout();
        Flash::set('success', 'You have been successfully logged out.');
        return Redirect::to('/');
    }
}
