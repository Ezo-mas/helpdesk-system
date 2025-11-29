<?php

class Role {
    private $id;
    private $name;
    private $permissions;

    public function __construct($id, $name, $permissions = []) {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function addPermission($permission) {
        $this->permissions[] = $permission;
    }

    public function removePermission($permission) {
        $this->permissions = array_filter($this->permissions, function($p) use ($permission) {
            return $p !== $permission;
        });
    }
}