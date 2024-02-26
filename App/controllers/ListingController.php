<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

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
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

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
    public function show(array $params): void
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

    /**
     * Store a newly created resource in the database.
     */
    public function store(): void
    {
        $allowedFields = [
            'title',
            'description',
            'salary',
            'requirements',
            'benefits',
            'tags',
            'company',
            'address',
            'city',
            'state',
            'phone',
            'email',
        ];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        // Will be replace later using sessions.
        $newListingData['user_id'] = Session::get('user')['id'];

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = [
            'title',
            'description',
            'salary',
            'email',
            'city',
        ];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }
        if (!empty($errors)) {
            // Reload view with error
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData,
            ]);
        } else {
            // submit data
            $fields = [];
            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }
            $fields = implode(', ', $fields);

            $values = [];
            foreach ($newListingData as $field => $value) {
                // Convert emtpy strings to Null
                if ($value === '') {
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }
            $values = implode(', ', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

            $this->db->query($query, $newListingData);

            Session::setFlashMessage('success_message', 'Listing created successfully.');

            redirect('/listings');
        }
    }

    /**
     * Delete a resource.
     */
    public function destroy(array $params)
    {
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing.');
            return redirect('/listings/' . $listing->id);
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        // Set flash message
        Session::setFlashMessage('success_message', 'Listing deleted successfully.');

        redirect('/listings');
    }

    /**
     * Show the edit form.
     */
    public function edit(array $params): void
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

        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing.');
            redirect('/listings/' . $listing->id);
            return;
        }

        loadView('listings/edit', [
            'listing' => $listing,
        ]);
    }

    /**
     * Update a resource.
     */
    public function update(array $params): void
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
        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to update this listing.');
            redirect('/listings/' . $listing->id);
            return;
        }

        $allowedFields = [
            'title',
            'description',
            'salary',
            'requirements',
            'benefits',
            'tags',
            'company',
            'address',
            'city',
            'state',
            'phone',
            'email',
        ];

        $updateValues = [];

        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

        $updateValues = array_map('sanitize',$updateValues);

        $requiredFields = [
            'title',
            'description',
            'salary',
            'email',
            'city',
        ];

        $errors = [];

        foreach($requiredFields as $field) {
            if(empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        if(!empty($errors)) {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors,
            ]);
            exit;
        } else {
            // Update the resource in the database.
            $updateFields = [];
            foreach(array_keys($updateValues) as $field) {
               $updateFields[] = "{$field} = :{$field}";
            }
            $updateFields = implode(', ', $updateFields);
            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

            $updateValues['id'] = $id;
            
            $this->db->query($updateQuery, $updateValues);

            Session::setFlashMessage('success_message', 'Listing updated successfully.');

            redirect('/listings/' . $id);
        }
    }

    /**
     * Search listing by keywords/locations.
     */
    public function search()
    {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';
        $query = "SELECT * FROM listings
            WHERE (title LIKE :keywords 
            OR description LIKE :keywords 
            OR tags LIKE :keywords 
            OR company LIKE :keywords
            OR requirements LIKE :keywords
            ) AND (
            city LIKE :location 
            OR state LIKE :location)";

        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%",
          ];

         $listings = $this->db->query($query, $params)->fetchAll();
          
        loadView('/listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location,
        ]);
    }
}
