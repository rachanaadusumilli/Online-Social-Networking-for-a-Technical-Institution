<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cc";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$Username=$_POST['Username'];

$Regdno=$_POST['Regdno'];

$Department=$_POST['Department'];

$Password=$_POST['Password'];

$Email=$_POST['Email'];

$DOB=$_POST['DOB'];

$sql = "INSERT INTO users (Username, Regdno, Department, Password, Email, DOB)
VALUES ('$Username', '$Regdno', '$Department', '$Password', '$Email', '$DOB')";

if ($conn->query($sql) === TRUE) 
{
    echo "New record created successfully";
} 
else 
{
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>


<html>
<body>

<form name=f1 action="logind1.html" method="POST">
<button type="submit" name="register">Go to Login Page</button>
</form></body></html>
