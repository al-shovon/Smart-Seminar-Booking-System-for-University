<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
// Fetch stats
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM seminars WHERE status='active'"))['c'];
$bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(booked_seats) as c FROM seminars"))['c'] ?? 0;

// Fetch all active seminars
$result = mysqli_query($conn, "SELECT * FROM seminars WHERE status='active' ORDER BY seminar_date ASC");
?>

<!-- HERO -->
<section class="hero">
    <h1>Welcome to <span>DIU Seminar Hub</span></h1>
    <p>Discover and book upcoming university seminars, workshops, and academic events — all in one place.</p>
    <a href="#seminars" class="btn btn-primary"><i class="fas fa-calendar-check"></i> Browse Seminars</a>

    <div class="hero-stats">
        <div class="stat-item">
            <h3><?= $total ?></h3>
            <p>Active Seminars</p>
        </div>
        <div class="stat-item">
            <h3><?= $bookings ?>+</h3>
            <p>Total Bookings</p>
        </div>
        <div class="stat-item">
            <h3>100%</h3>
            <p>Online Process</p>
        </div>
    </div>
</section>

<!-- SEMINARS SECTION -->
<section class="section" id="seminars">
    <div class="section-title">
        <h2><i class="fas fa-calendar-alt"></i> Upcoming Seminars</h2>
        <p>Click on any seminar to view details and book your seat</p>
    </div>

    <!-- Search -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search by title, speaker, venue..."
               onkeyup="filterSeminars()">
        <button><i class="fas fa-search"></i></button>
    </div>

    <!-- Cards -->
    <div class="cards-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($s = mysqli_fetch_assoc($result)): ?>
                <?php
                    $available = $s['total_seats'] - $s['booked_seats'];
                    $percent   = ($s['booked_seats'] / $s['total_seats']) * 100;
                    $fillClass = $percent < 50 ? 'fill-low' : ($percent < 80 ? 'fill-medium' : 'fill-high');

                    if ($available == 0) {
                        $badgeClass = 'badge-full';
                        $badgeText  = '<i class="fas fa-times-circle"></i> Full';
                    } elseif ($available <= 5) {
                        $badgeClass = 'badge-limited';
                        $badgeText  = '<i class="fas fa-exclamation-circle"></i> ' . $available . ' seats left';
                    } else {
                        $badgeClass = 'badge-available';
                        $badgeText  = '<i class="fas fa-check-circle"></i> ' . $available . ' seats available';
                    }
                ?>
                <div class="card seminar-card"
                     data-title="<?= htmlspecialchars($s['title']) ?>"
                     data-speaker="<?= htmlspecialchars($s['speaker']) ?>"
                     data-venue="<?= htmlspecialchars($s['venue']) ?>">

                    <div class="card-header">
                        <h3><?= htmlspecialchars($s['title']) ?></h3>
                        <span class="card-badge"><i class="fas fa-user-tie"></i> <?= htmlspecialchars($s['speaker']) ?></span>
                    </div>

                    <div class="card-body">
                        <div class="card-info">
                            <span><i class="fas fa-calendar"></i> <?= date('d M, Y', strtotime($s['seminar_date'])) ?></span>
                            <span><i class="fas fa-clock"></i> <?= date('h:i A', strtotime($s['seminar_time'])) ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($s['venue']) ?></span>
                        </div>

                        <!-- Seat Progress Bar -->
                        <div class="seat-bar">
                            <div class="seat-bar-label">
                                <span>Seats Booked</span>
                                <span><?= $s['booked_seats'] ?> / <?= $s['total_seats'] ?></span>
                            </div>
                            <div class="seat-bar-track">
                                <div class="seat-bar-fill <?= $fillClass ?>"
                                     style="width: <?= min($percent, 100) ?>%"></div>
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>

                            <?php if ($available > 0): ?>
                                <a href="seminar_detail.php?id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-right"></i> Details
                                </a>
                            <?php else: ?>
                                <span class="btn btn-sm" style="background:#e5e7eb; color:var(--muted); cursor:not-allowed;">
                                    <i class="fas fa-ban"></i> Closed
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results" id="noResults" style="grid-column:1/-1;">
                <i class="fas fa-calendar-times"></i>
                <h3>No seminars available right now</h3>
                <p>Check back later for upcoming events.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- No search results message -->
    <div class="no-results" id="noResults" style="display:none; margin-top:2rem;">
        <i class="fas fa-search"></i>
        <h3>No results found</h3>
        <p>Try a different search keyword.</p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>