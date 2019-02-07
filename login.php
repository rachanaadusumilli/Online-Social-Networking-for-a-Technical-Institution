<?php
session_start(); // Starting Session
$error=''; // Variable To Store Error Message
if (isset($_POST['submit'])) {
if (empty($_POST['Regdno']) || empty($_POST['Password'])) {
$error = "RegdNo or Password is invalid";
}
else
{
// Define $Regdno and $Password
$Regdno=$_POST['Regdno'];
$Password=$_POST['Password'];
// Establishing Connection with Server by passing server_name, user_id and password as a parameter
$connection = mysql_connect("localhost", "root", "");
// To protect MySQL injection for Security purpose
$Regdno = stripslashes($Regdno);
$Password = stripslashes($Password);
$Regdno= mysql_real_escape_string($Regdno);
$Password = mysql_real_escape_string($Password);
// Selecting Database
$db = mysql_select_db("cc", $connection);
// SQL query to fetch information of registerd users and finds user match.
$query = mysql_query("select * from users where Password='$Password' AND Regdno='$Regdno'", $connection);
$rows = mysql_num_rows($query);
if ($rows == 1) {
$_SESSION['login_user']=$Regdno; // Initializing Session
header("location: profile.php"); // Redirecting To Other Page
} else {
$error = "RegdNo or Password is invalid";
}
mysql_close($connection); // Closing Connection
}
}
?>