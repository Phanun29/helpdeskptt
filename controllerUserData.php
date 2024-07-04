<?php
session_start();
require "admin/config.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = "";
$name = "";
$errors = array();

function sendEmail($to, $subject, $message)
{
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 2; // Set to 2 for verbose debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kingkong290804@gmail.com'; // Replace with your email
        $mail->Password = 'vgks dcdl mieu shxh'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('kingkong290804@gmail.com', 'King Konng'); // Replace with your email and name
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if (isset($_POST['signup'])) {
    // Get and sanitize user inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $cpassword = htmlspecialchars($_POST['cpassword']);

    // Check if passwords match
    if ($password !== $cpassword) {
        $errors['password'] = "Confirm password does not match!";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $errors['email'] = "Email that you have entered already exists!";
    }

    // If there are no errors, proceed with user registration
    if (count($errors) === 0) {
        // Hash the password
        $encpass = password_hash($password, PASSWORD_BCRYPT);

        // Directly insert the user without email verification
        $status = "1"; // Directly mark the user as verified
        $stmt = $conn->prepare("INSERT INTO tbl_users (users_name, email, password, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $encpass, $status);

        if ($stmt->execute()) {
            // Automatically log the user in after successful registration
            $_SESSION['users_id'] = $stmt->insert_id; // Get the inserted user's ID
            $_SESSION['users_name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['status'] = $status;

            // Redirect to the user's dashboard or homepage
            header('location: dashboard.php'); // Adjust this path as necessary
            exit();
        } else {
            $errors['db-error'] = "Failed while inserting data into the database!";
        }
    }
}

// If user click verification code submit button
if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = htmlspecialchars($_POST['otp']);
    $stmt = $con->prepare("SELECT * FROM tbl_users WHERE code = ?");
    $stmt->bind_param("i", $otp_code);
    $stmt->execute();
    $code_res = $stmt->get_result();

    if ($code_res->num_rows > 0) {
        $fetch_data = $code_res->fetch_assoc();
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = '1';
        $stmt = $con->prepare("UPDATE tbl_users SET code = ?, status = ? WHERE code = ?");
        $stmt->bind_param("isi", $code, $status, $fetch_code);
        if ($stmt->execute()) {
            $_SESSION['users_name'] = $fetch_data['users_name']; // Ensure the session variable is set correctly
            $_SESSION['email'] = $email;
            header('location: Backend/index.php');
            exit();
        } else {
            $errors['otp-error'] = "Failed while updating code!";
        }
    } else {
        $errors['otp-error'] = "You've entered an incorrect code!";
    }
}

// If user click login button
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $check_email = "SELECT * FROM tbl_users WHERE email = '$email'";
    $res = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($res) > 0) {
        $fetch = mysqli_fetch_assoc($res);
        $fetch_pass = $fetch['password'];
        if (password_verify($password, $fetch_pass)) {
            $_SESSION['email'] = $email;
            $status = $fetch['status'];
            if ($status == '1') {
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('location: admin');
            } else {
                // echo "your account is inactive";
                $errors['email'] = "your account is inactive";
                // $info = "It looks like you haven't verified your email - $email";
                // $_SESSION['info'] = $info;
                // header('location: user-otp.php');
            }
        } else {
            $errors['email'] = "Incorrect email or password!";
        }
    } else {
        $errors['email'] = "It looks like you're not yet a member! ";
    }
}

// If user click continue button in forgot password form
if (isset($_POST['check-email'])) {
    $email = htmlspecialchars($_POST['email']);
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $run_sql = $stmt->get_result();

    if ($run_sql->num_rows > 0) {
        $code = rand(999999, 111111);
        $stmt = $conn->prepare("UPDATE tbl_users SET code = ? WHERE email = ?");
        $stmt->bind_param("is", $code, $email);
        if ($stmt->execute()) {
            $subject = "Password Reset Code";
            $message = "Your password reset code is $code";
            if (sendEmail($email, $subject, $message)) {
                $info = "We've sent a password reset OTP to your email - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                header('location: reset-code.php');
                exit();
            } else {
                $errors['otp-error'] = "Failed while sending code!";
            }
        } else {
            $errors['db-error'] = "Something went wrong!";
        }
    } else {
        $errors['email'] = "This email address does not exist!";
    }
}

// If user click check reset OTP button
if (isset($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    $otp_code = htmlspecialchars($_POST['otp']);
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE code = ?");
    $stmt->bind_param("i", $otp_code);
    $stmt->execute();
    $code_res = $stmt->get_result();

    if ($code_res->num_rows > 0) {
        $fetch_data = $code_res->fetch_assoc();
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Please create a new password that you don't use on any other site.";
        $_SESSION['info'] = $info;
        header('location: new-password.php');
        exit();
    } else {
        $errors['otp-error'] = "You've entered an incorrect code!";
    }
}

// If user click change password button
if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = htmlspecialchars($_POST['password']);
    $cpassword = htmlspecialchars($_POST['cpassword']);

    if ($password !== $cpassword) {
        $errors['password'] = "Confirm password not matched!";
    } else {
        $code = 0;
        $email = $_SESSION['email'];
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE tbl_users SET code = ?, password = ? WHERE email = ?");
        $stmt->bind_param("iss", $code, $encpass, $email);
        if ($stmt->execute()) {
            $info = "Your password has been changed. Now you can login with your new password.";
            $_SESSION['info'] = $info;
            header('Location: password-changed.php');
        } else {
            $errors['db-error'] = "Failed to change your password!";
        }
    }
}

// If login now button click
if (isset($_POST['login-now'])) {
    header('Location: index.php');
}
