<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'vendor/autoload.php';
include_once "managerFunctions.php";

$newMail = new PHPMailer();
$newMail->isSMTP();
$newMail->Host = "smtp.gmail.com";
$newMail->SMTPAuth = true;
$newMail->Username = "managerlocalhost@gmail.com";
$newMail->Password = "jdprtejubfdlxbnz";
$newMail->setFrom("noreply@manager.com", "manager");
$newMail->isHTML(true);

function sendVerificationEmail(string $Email, string $fullName)
{
    global $newMail;
    $newMail->addAddress($Email, $fullName);
    $newMail->Subject = "Manager - Verify Your Email";
    ob_start();
    include_once "verifyEmail.php";
    $Body = ob_get_contents();
    $newMail->Body = $Body;
    ob_end_clean();
    unset($Body);
    try {
        $newMail->send();
    } catch (Exception $e) {
        return false;
    }
    return true;
}

function sendResetPasswordEmail(string $Email, string $fullName, string $Hash)
{
    global $newMail, $connectToDB;
    mysqli_query($connectToDB, "UPDATE Users SET Hash = '$Hash' WHERE Email = '$Email'");
    if (mysqli_affected_rows($connectToDB) > 0) {
        $newMail->addAddress($Email, $fullName);
        $newMail->Subject = "Manager - Reset Your Password";
        ob_start();
        include_once "resetPassword.php";
        $Body = ob_get_contents();
        $newMail->Body = $Body;
        ob_end_clean();
        unset($Body);
        try {
            $newMail->send();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    return false;
}

function sendContactUsEmail(String $senderName, String $messageSubject)
{
    global $newMail;
    $newMail->addAddress("managerlocalhost@gmail.com", $senderName);
    $newMail->Subject = $messageSubject;
    ob_start();
    include_once "contactUsEmail.php";
    $Body = ob_get_contents();
    $newMail->Body = $Body;
    ob_end_clean();
    unset($Body);
    try {
        $newMail->send();
    } catch (Exception $e) {
        return false;
    }
    return true;
}
