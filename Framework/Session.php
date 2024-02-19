<?php

namespace Framework;

class Session {

    /**
     * Start session
     */
    public static function start() 
    {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    } 

    /**
     * Set a session key value pair.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value by the key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Check if session key exist
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Clear session by key.
     */
    public static function clear(string $key): void
    {
        if(isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Clear all session data.
     */
    public static function clearAll(): void
    {
        session_unset();
        session_destroy();
    }
}