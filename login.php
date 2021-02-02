<?php

$username=$_POST['username'];
$password=$_POST['password'];

session_id($username);

session_start(); # Starting Session

# Assume user authenticated successfully. Store session variables

$_SESSION['login_user']= $username; 
$_SESSION['logged_in'] = true;
$_SESSION['id'] = session_id();

header("Location: home.php"); 

?>

