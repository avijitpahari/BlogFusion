<?php
session_start();
require_once dirname(__DIR__) . '/config.php';

// Check login
function requireUser(){
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "pages/login.php");
        exit;
    }
}

// Check admin
function requireAdmin(){
    requireUser(); // first check login
    
    if ($_SESSION['role'] !== "admin") {
        header("Location: " . BASE_URL . "pages/login.php");
        exit;
    }
}

// Check author
function requireAuthor(){
    requireUser(); // first check login

    if ($_SESSION['role'] !== "author" && $_SESSION['role'] !== "admin") {
        header("Location: " . BASE_URL . "pages/login.php");
        exit;
    }
}
?>