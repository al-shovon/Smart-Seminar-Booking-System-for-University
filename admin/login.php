<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

include '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $result = mysqli_query($conn, "SELECT * FROM admins WHERE username='$username'");
        $admin  = mysqli_fetch_assoc($result);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — DIU Seminar Hub</title>
    <link rel="stylesheet" href="/seminar_system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a3c6e 0%, #2563eb 100%);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
            color: white;
        }

        .login-logo i {
            font-size: 3rem;
            color: #e8a020;
            display: block;
            margin-bottom: 0.5rem;
        }

        .login-logo h1 {
            font-size: 1.5rem;
            margin-bottom: 0.3rem;
        }

        .login-logo p {
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .login-card h2 {
            color: #1a3c6e;
            font-size: 1.3rem;
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-card p {
            color: #6b7280;
            font-size: 0.88rem;
            margin-bottom: 1.8rem;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.95rem;
        }

        .input-icon input {
            padding-left: 42px !important;
        }

        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            background: none;
            border: none;
            font-size: 0.95rem;
        }

        .toggle-pass:hover { color: #1a3c6e; }

        .login-btn {
            width: 100%;
            justify-content: center;
            padding: 14px;
            font-size: 1rem;
            margin-top: 0.5rem;
            border-radius: 10px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .back-link:hover { color: #e8a020; }

        .shake {
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%       { transform: translateX(-8px); }
            75%       { transform: translateX(8px); }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- Logo -->
    <div class="login-logo">
        <i class="fas fa-graduation-cap"></i>
        <h1>DIU Seminar Hub</h1>
        <p>Admin Control Panel</p>
    </div>

    <!-- Login Card -->
    <div class="login-card <?= $error ? 'shake' : '' ?>">
        <h2><i class="fas fa-lock"></i> Admin Login</h2>
        <p>Enter your credentials to access the dashboard</p>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username"
                           placeholder="Enter username"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                           required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-key"></i> Password</label>
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" id="passInput"
                           placeholder="Enter password" required>
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <div style="text-align: right; margin-bottom: 10px;">
    <a href="forgot_password.php" style="font-size: 14px; color: #2563eb; text-decoration: none;">
        Forgot Password?
    </a>
</div>
<button type="submit" class="btn btn-primary login-btn">
<i class="fas fa-sign-in-alt"></i> Login to Dashboard
</button>
</form>
    </div>
    <a href="/seminar_system/index.php" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Student Portal
    </a>

</div>

<script>
function togglePass() {
    const input   = document.getElementById('passInput');
    const icon    = document.getElementById('eyeIcon');
    const isPass  = input.type === 'password';
    input.type    = isPass ? 'text' : 'password';
    icon.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
}
</script>
</body>
</html>
