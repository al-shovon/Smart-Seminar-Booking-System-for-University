<?php
$host = "localhost";
$user = "root";
$pass = "";  
$dbname = "seminar_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "seminar_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("
    <div style='font-family:sans-serif; text-align:center;
                padding:60px; background:#fee2e2; color:#dc2626;'>
        <h2>Database Connection Failed</h2>
        <p>Please make sure XAMPP is running and the database exists.</p>
        <code>" . mysqli_connect_error() . "</code>
    </div>");
}



mysqli_set_charset($conn, "utf8mb4");
?>
