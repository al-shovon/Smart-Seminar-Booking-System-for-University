<?php include 'auth_check.php'; ?>
<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password — Admin</title>
    <link rel="stylesheet" href="/seminar_system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_nav.php'; ?>

<?php
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current  = trim($_POST['current_password']);
    $new      = trim($_POST['new_password']);
    $confirm  = trim($_POST['confirm_password']);
    $admin_id = $_SESSION['admin_id'];

    $admin = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT password FROM admins WHERE id=$admin_id")
    );

    if (!password_verify($current, $admin['password'])) {
        $errors[] = "Current password is incorrect.";
    }
    if (strlen($new) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    }
    if ($new !== $confirm) {
        $errors[] = "New passwords do not match.";
    }

    if (empty($errors)) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        mysqli_query($conn,
            "UPDATE admins SET password='$hashed' WHERE id=$admin_id"
        );
        $success = true;
    }
}
?>

<div class="admin-layout">
    <?php include 'sidebar.php'; ?>

    <main class="admin-content">

        <div class="page-header">
            <div>
                <h1><i class="fas fa-key"></i> Change Password</h1>
                <p>Update your admin account password</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Password changed successfully!
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <div>
                    <i class="fas fa-exclamation-circle"></i>
                    <ul style="margin-top:6px; padding-left:18px;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= $e ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-card" style="max-width:500px;">
            <form method="POST">

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Current Password</label>
                    <input type="password" name="current_password"
                           placeholder="Enter current password" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-key"></i> New Password</label>
                    <input type="password" name="new_password"
                           placeholder="Min. 6 characters" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-check"></i> Confirm New Password</label>
                    <input type="password" name="confirm_password"
                           placeholder="Re-enter new password" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>
</body>
</html>
