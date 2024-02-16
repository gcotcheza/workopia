<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show all the listings.
     */
    public function index(): void
    {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        loadView('listings/index', [
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
    public function show($params): void
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings where id = :id', $params)->fetch();

        // Check if resource exist.
        if (!$listing) {
            ErrorController::notFound('Job listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing,
        ]);
    }
}
