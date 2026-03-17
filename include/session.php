<?php
session_start();

// Check login
function requireUser(){
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../pages/login.php");
        exit;
    }
}

// Check admin
function requireAdmin(){
    requireUser(); // first check login

    if ($_SESSION['role'] !== "admin") {
        header("Location: ../pages/login.php");
        exit;
    }
}

// Check author
function requireAuthor(){
    requireUser(); // first check login

    if ($_SESSION['role'] !== "author") {
        header("Location: ../pages/login.php");
        exit;
    }
}
?>