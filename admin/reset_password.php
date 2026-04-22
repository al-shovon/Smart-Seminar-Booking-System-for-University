<?php include '../includes/db.php';

$token = $_GET['token'];

$result = mysqli_query($conn, "SELECT * FROM admins WHERE reset_token='$token' AND token_expire > NOW()");
$user = mysqli_fetch_assoc($result);

if(!$user){
    die("Invalid or expired token");
}
?>

<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit">Reset Password</button>
</form>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "UPDATE admins 
        SET password='$pass', reset_token=NULL, token_expire=NULL 
        WHERE id=".$user['id']);

    echo "Password Updated!";
}
?>
