<?php

class Ticket {
    private $id;
    private $userId;
    private $title;
    private $description;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct($userId, $title, $description) {
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
        $this->status = 'open'; // Default status
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function save() {
        // Code to save the ticket to the database
    }

    public function update() {
        // Code to update the ticket in the database
    }

    public function delete() {
        // Code to delete the ticket from the database
    }

    public static function getAllTickets() {
        // Code to retrieve all tickets from the database
    }

    public static function getTicketById($id) {
        // Code to retrieve a ticket by its ID from the database
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setStatus($status) {
        $this->status = $status;
        $this->updatedAt = date('Y-m-d H:i:s');
    }
}