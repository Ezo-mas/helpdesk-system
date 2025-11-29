<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Role;

class UserController
{
    public function viewProfile($userId)
    {
        $user = User::find($userId);
        if ($user) {
            // Load the user profile view with user data
            require_once '../views/user/profile.php';
        } else {
            // Handle user not found
            echo "User not found.";
        }
    }

    public function updateProfile($userId, $data)
    {
        $user = User::find($userId);
        if ($user) {
            $user->update($data);
            // Redirect or load a success message
            header("Location: /user/profile/$userId");
        } else {
            // Handle user not found
            echo "User not found.";
        }
    }

    public function listTickets($userId)
    {
        $tickets = Ticket::where('user_id', $userId)->get();
        // Load the tickets view with the user's tickets
        require_once '../views/user/my-tickets.php';
    }
}