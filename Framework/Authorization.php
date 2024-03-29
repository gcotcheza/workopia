<?php

namespace Framework;

class Authorization
{
    /**
     * Check if current logged in user owns a resource.
     */
    public static function isOwner($userId): bool
    {
        $sessionUser = Session::get('user');

        if($sessionUser !== null && isset($sessionUser['id'])) {
            $sessionUserId = (int) $sessionUser['id'];
            return $sessionUserId === $userId;
        }
        return false;
    }
}
