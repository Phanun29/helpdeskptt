<?php
session_start();
include "config.php";

// Check if the session variables are set
if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
    $email = $_SESSION['email'];
    $password = $_SESSION['password'];

    if ($email != false && $password != false) {
        $sql = "SELECT * FROM tbl_users WHERE email = '$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $fetch_info = $result->fetch_assoc();
            $status = $fetch_info['status'];
            $code = $fetch_info['code'];
            // Proceed with the rest of your code here if the user is found
        } else {
            // Redirect to 404.php if user not found
            header("Location: 404.php");
            exit;
        }
    } else {
        // Redirect to 404.php if email or password is invalid
        header("Location: 404.php");
        exit;
    }
} else {
    // Redirect to the login page if the session variables are not set
    header("Location: ../index.php");
    exit;
}
