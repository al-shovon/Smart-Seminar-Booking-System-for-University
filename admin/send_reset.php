<?php
include '../includes/db.php';

$email = $_POST['email'];

$token = bin2hex(random_bytes(50));
$expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

$query = "UPDATE admins SET reset_token='$token', token_expire='$expire' WHERE email='$email'";
mysqli_query($conn, $query);

$link = "http://yourdomain.com/admin/reset_password.php?token=$token";

echo "Reset Link: <a href='$link'>$link</a>";
?>
