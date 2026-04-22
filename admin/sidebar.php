<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">

    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i>
        <span>Admin Panel</span>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php"
               class="<?= $current == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="seminars.php"
               class="<?= $current == 'seminars.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> Seminars
            </a>
        </li>
        <li>
            <a href="add_seminar.php"
               class="<?= $current == 'add_seminar.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i> Add Seminar
            </a>
        </li>
        <li>
            <a href="participants.php"
               class="<?= $current == 'participants.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Participants
            </a>
        </li>
        <li style="margin-top:auto; border-top:1px solid rgba(255,255,255,0.1); padding-top:1rem;">
            <a href="/seminar_system/index.php">
                <i class="fas fa-external-link-alt"></i> Student Portal
            </a>
        </li>
        <li>
            <a href="logout.php" style="color:#fca5a5;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>