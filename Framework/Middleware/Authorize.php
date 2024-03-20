<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize
{
    /**
     * Check if user is authenticated.
     */
    public function isAuthenticated(): bool
    {
        return Session::has('user');
    }

    /**
     * Handle the user's request.
     */
    public function handle(string $role)
    {
        if ($role === 'guest' && $this->isAuthenticated()) {
            return redirect('/');
        } elseif ($role === 'auth' && !$this->isAuthenticated()) {
            return redirect('/auth/login');
        }
    }
}
