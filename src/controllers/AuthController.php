<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Role;

class AuthController
{
    public function register($data)
    {
        // Validate and register a new user
        $user = new User();
        $user->username = $data['username'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->email = $data['email'];
        $user->role_id = Role::getRoleIdByName('user'); // Assuming 'user' is a role in the system
        return $user->save();
    }

    public function login($data)
    {
        // Validate user credentials and log in
        $user = User::findByEmail($data['email']);
        if ($user && password_verify($data['password'], $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role'] = $user->role_id;
            return true;
        }
        return false;
    }

    public function logout()
    {
        // Log out the user
        session_destroy();
    }
}