<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class UserController {
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
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
}