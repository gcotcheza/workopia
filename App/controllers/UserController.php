<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
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
    public function login(): void
    {
        loadView('users/login');
    }

    /**
     * Show the register page.
     */
    public function create(): void
    {
        loadView('users/create');
    }

    /**
     * Store a new user in the database.
     */
    public function store(): void
    {
        $name                 = $_POST['name'];
        $email                = $_POST['email'];
        $city                 = $_POST['city'];
        $state                = $_POST['state'];
        $password             = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        $errors = [];

        // Validation
        if(!Validation::email($email)) {
            $errors['email'] = ' Please enter a valid email address.';
        }

        if (!Validation::string($name, 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 characters.';
        }

        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be atleast 6 characters.';
        }

        if (!Validation::match($password, $passwordConfirmation)) {
            $errors['password'] = 'Passwords do not match.';
        }

        if(!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user'   => [
                    'name'  => $name,
                    'email' => $email,
                    'city'  => $city,
                    'state' => $state,
                ],
            ]);
            exit;
        }
        // Check if email already exist in the database.
        $params = [
            'email' => $email,
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

        if($user) {
            $errors['email'] = 'That email is already taken.';
            loadView('users/create', [
                'errors' => $errors,
            ]);
            exit;
        }

        // Create user account
        $params = [
            'name'     => $name,
            'email'    => $email,
            'city'     => $city,
            'state'    => $state,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $this->db->query('INSERT INTO users(name, email, city, state, password) VALUES(:name, :email, :city, :state, :password)', $params);

        // Get the new user ID
        $userId = $this->db->conn->lastInsertId();

        // Set user session
        Session::set('user', [
            'id'    => $userId,
            'name'  => $name,
            'email' => $email,
            'city'  => $city,
            'state' => $state,
        ]);

        redirect('/');
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
     * Authenticate a user with email and password
     */
    public function authenticate(): void
    {
        $email    = $_POST['email'];
        $password = $_POST['password'];

        $errors = [];

        // Validation
        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email';
        }

        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be atleast 6 characters';
        }

        // Check for errors.
        if(!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors,
            ]);
            exit;
        }

        // Check for email.
        $params = [
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

        if(!$user) {
            $errors['email'] = 'Incorrect credentials.';
            loadView('users/login', [
                'errors' => $errors,
            ]);
            exit;
        }

        // Check if password is correct.
        if(!password_verify($password, $user->password)) {
            $errors['email'] = 'Incorrect credentials.';
            loadView('users/login', [
                'errors' => $errors,
            ]);
            exit;
        }
        // Set user session
        Session::set('user', [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'city'  => $user->city,
            'state' => $user->state,
        ]);

        redirect('/');
    }
}
