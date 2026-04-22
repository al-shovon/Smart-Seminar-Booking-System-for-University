<?php include 'auth_check.php'; ?>
<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Admin Panel</title>
    <link rel="stylesheet" href="/seminar_system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_nav.php'; ?>

<?php
// ── Statistics ───────────────────────────────────────────
$total_seminars  = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as c FROM seminars"))['c'];

$active_seminars = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as c FROM seminars WHERE status='active'"))['c'];

$total_bookings  = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as c FROM bookings"))['c'];

$total_seats     = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(total_seats) as c FROM seminars"))['c'] ?? 0;

$booked_seats    = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(booked_seats) as c FROM seminars"))['c'] ?? 0;

$fill_rate = $total_seats > 0 ? round(($booked_seats / $total_seats) * 100) : 0;

// ── Upcoming Seminars (next 3) ────────────────────────────
$upcoming = mysqli_query($conn,
    "SELECT * FROM seminars
     WHERE status='active' AND seminar_date >= CURDATE()
     ORDER BY seminar_date ASC LIMIT 3");

// ── Recent Bookings (last 5) ──────────────────────────────
$recent_bookings = mysqli_query($conn,
    "SELECT b.*, s.title FROM bookings b
     JOIN seminars s ON b.seminar_id = s.id
     ORDER BY b.booked_at DESC LIMIT 5");
?>

<div class="admin-layout">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong>!
                   Here's what's happening today.</p>
            </div>
            <a href="add_seminar.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Seminar
            </a>
        </div>

        <!-- ── Stat Cards ── -->
        <div class="stat-cards">

            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-info">
                    <h3><?= $total_seminars ?></h3>
                    <p>Total Seminars</p>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3><?= $active_seminars ?></h3>
                    <p>Active Seminars</p>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?= $total_bookings ?></h3>
                    <p>Total Bookings</p>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="stat-info">
                    <h3><?= $fill_rate ?>%</h3>
                    <p>Overall Fill Rate</p>
                </div>
            </div>

        </div>

        <!-- ── Two Column Layout ── -->
        <div class="dash-grid">

            <!-- Upcoming Seminars -->
            <div class="dash-box">
                <div class="dash-box-header">
                    <h2><i class="fas fa-calendar-check"></i> Upcoming Seminars</h2>
                    <a href="seminars.php" class="btn btn-secondary btn-sm">View All</a>
                </div>

                <?php if (mysqli_num_rows($upcoming) > 0): ?>
                    <?php while ($s = mysqli_fetch_assoc($upcoming)):
                        $avail   = $s['total_seats'] - $s['booked_seats'];
                        $pct     = ($s['booked_seats'] / $s['total_seats']) * 100;
                        $fClass  = $pct < 50 ? 'fill-low' : ($pct < 80 ? 'fill-medium' : 'fill-high');
                    ?>
                    <div class="upcoming-item">
                        <div class="upcoming-date">
                            <span class="day"><?= date('d', strtotime($s['seminar_date'])) ?></span>
                            <span class="mon"><?= date('M', strtotime($s['seminar_date'])) ?></span>
                        </div>
                        <div class="upcoming-info">
                            <h4><?= htmlspecialchars($s['title']) ?></h4>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($s['venue']) ?>
                               &nbsp;|&nbsp;
                               <i class="fas fa-clock"></i> <?= date('h:i A', strtotime($s['seminar_time'])) ?>
                            </p>
                            <div class="seat-bar" style="margin-top:8px;">
                                <div class="seat-bar-label">
                                    <span><?= $s['booked_seats'] ?>/<?= $s['total_seats'] ?> booked</span>
                                    <span><?= $avail ?> left</span>
                                </div>
                                <div class="seat-bar-track">
                                    <div class="seat-bar-fill <?= $fClass ?>"
                                         style="width:<?= min($pct,100) ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <a href="edit_seminar.php?id=<?= $s['id'] ?>"
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-calendar-times"></i>
                        <p>No upcoming seminars</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Bookings -->
            <div class="dash-box">
                <div class="dash-box-header">
                    <h2><i class="fas fa-clock"></i> Recent Bookings</h2>
                    <a href="participants.php" class="btn btn-secondary btn-sm">View All</a>
                </div>

                <?php if (mysqli_num_rows($recent_bookings) > 0): ?>
                    <?php while ($b = mysqli_fetch_assoc($recent_bookings)): ?>
                    <div class="recent-booking-item">
                        <div class="avatar">
                            <?= strtoupper(substr($b['student_name'], 0, 1)) ?>
                        </div>
                        <div class="booking-info">
                            <h4><?= htmlspecialchars($b['student_name']) ?></h4>
                            <p><?= htmlspecialchars($b['title']) ?></p>
                            <span class="time-ago">
                                <i class="fas fa-clock"></i>
                                <?= date('d M, h:i A', strtotime($b['booked_at'])) ?>
                            </span>
                        </div>
                        <span class="badge badge-available">Booked</span>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-users"></i>
                        <p>No bookings yet</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>
</div>

</body>
</html>