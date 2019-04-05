<?php

// if $number is a number and is 10 characters long, return true
function validatePhoneNumber($number) {
    return (is_numeric($number) && (strlen((string) $number) == 10));
}

// if $email is of format xx@xx.xx then return true
function validateEmail($email) {
    $atSignPos = strpos($email, "@");
    $dotPos = strpos($email, ".");
    return ($atSignPos != FALSE && $dotPos != FALSE
        && $atSignPos < $dotPos);
}

?>
