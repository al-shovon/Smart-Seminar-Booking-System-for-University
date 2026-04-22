<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <i class="fas fa-graduation-cap"></i>
            <span>DIU Seminar Hub</span>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <span style="color:rgba(255,255,255,0.7); font-size:0.9rem;">
                <i class="fas fa-user-shield"></i>
                <?= htmlspecialchars($_SESSION['admin_user']) ?>
            </span>
            <a href="logout.php" class="btn btn-sm"
               style="background:rgba(255,255,255,0.15); color:white;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>
