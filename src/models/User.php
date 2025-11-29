<?php

class User {
    private $id;
    private $username;
    private $password;
    private $email;
    private $role_id;

    public function __construct($id, $username, $password, $email, $role_id) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role_id = $role_id;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRoleId() {
        return $this->role_id;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setRoleId($role_id) {
        $this->role_id = $role_id;
    }

    public static function findById($id) {
        // Logic to find a user by ID from the database
    }

    public static function findByUsername($username) {
        // Logic to find a user by username from the database
    }

    public function save() {
        // Logic to save the user to the database
    }

    public function delete() {
        // Logic to delete the user from the database
    }
}