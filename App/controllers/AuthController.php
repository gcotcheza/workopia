<?php

namespace App\Controllers;

use Framework\Session;
use Framework\Database;
use Framework\Validation;

class AuthController
{
    protected $db;

    public function __construct()
    {
        $config   = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show login page
     */
    public function showLoginForm(): void
    {
        loadView('users/login');
    }

    /**
     * Logout user and kill session.
     */
    public function logout()
    {
        Session::clearAll();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

        redirect('/');
    }

    /**
     * Get the credentials.
     */
    public function credentials()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        return [
            'email' => $email,
            'password' => $password
        ];
    }

    /**
     * Validate login credentials.
     */
    public function validateLogin(): bool
    {
        $credentials = $this->credentials();
        $errors = [];

        if (!Validation::email($credentials['email'])) {

            $errors['email'] = 'Please enter a valid email';
        }
        if (!Validation::string($credentials['password'], 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors,
            ]);
            exit;
        }

        return true;
    }

    /**
     * Authenticate a user with the email and password.
     */
    public function authenticate(): void
    {
        $this->validateLogin();

        $credentials = $this->credentials();

        $params = [
            'email' => $credentials['email']
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

        if (!$user || !password_verify($credentials['password'], $user->password)) {
            $this->sendFailedLoginError('Login failed. Please check your email and password and try again.');
            return;
        }

        $this->setUserSession($user);

        redirect('/');
    }

    /**
     * Send failed login error message.
     */
    public function sendFailedLoginError(string $message): void
    {
        loadView('users/login', [
            'errors' => [
                'message' => $message,
            ]
        ]);
    }

    /**
     * Set user session.
     */
    public function setUserSession(Object $user): void
    {
        Session::set('user', [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'city'  => $user->city,
            'state' => $user->state,
        ]);
    }
}
