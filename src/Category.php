<?php
// src/Category.php

class Category {
    public int $id;
    public string $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Trả về chuỗi định dạng "[id] name"
     * 
     * @return string
     */
    public function label(): string {
        return "[" . $this->id . "] " . $this->name;
    }
}
