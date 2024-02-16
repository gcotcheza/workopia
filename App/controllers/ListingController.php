<?php

namespace App\Controllers;

use Framework\Database;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show aall the listings.
     */
    public function index(): void
    {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        loadView('home', [
            'listings' => $listings,
        ]);
    }

    /**
     * Shows the form to create a resource.
     */
    public function create(): void
    {
        loadView('listings/create');
    }

    /**
     * Show a specific resource.
     */
    public function show(): void
    {
        $id = $_GET['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings where id = :id', $params)->fetch();

        loadView('listings/show', [
            'listing' => $listing,
        ]);
    }
}
