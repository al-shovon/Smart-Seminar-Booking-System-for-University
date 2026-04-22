<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
// URL থেকে seminar id নাও
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Database থেকে seminar তথ্য আনো
$result = mysqli_query($conn, "SELECT * FROM seminars WHERE id=$id AND status='active'");
$s = mysqli_fetch_assoc($result);

// যদি seminar না পাওয়া যায়
if (!$s) {
    echo "<div class='section'><div class='alert alert-error'>
          <i class='fas fa-exclamation-circle'></i> Seminar not found!</div></div>";
    include 'includes/footer.php';
    exit;
}

$available = $s['total_seats'] - $s['booked_seats'];
$percent   = ($s['booked_seats'] / $s['total_seats']) * 100;
$fillClass = $percent < 50 ? 'fill-low' : ($percent < 80 ? 'fill-medium' : 'fill-high');
?>

<section class="section">

    <!-- Back Button -->
    <a href="index.php" class="btn btn-secondary btn-sm" style="margin-bottom:1.5rem;">
        <i class="fas fa-arrow-left"></i> Back to Seminars
    </a>

    <div class="detail-layout">

        <!-- LEFT: Seminar Info -->
        <div class="detail-card">
            <div class="detail-header">
                <h1><?= htmlspecialchars($s['title']) ?></h1>
                <span class="card-badge" style="font-size:1rem; padding:6px 16px;">
                    <i class="fas fa-user-tie"></i> <?= htmlspecialchars($s['speaker']) ?>
                </span>
            </div>

            <div class="detail-body">

                <!-- Info Grid -->
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <span class="info-label">Date</span>
                            <span class="info-value"><?= date('l, d F Y', strtotime($s['seminar_date'])) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="info-label">Time</span>
                            <span class="info-value"><?= date('h:i A', strtotime($s['seminar_time'])) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <span class="info-label">Venue</span>
                            <span class="info-value"><?= htmlspecialchars($s['venue']) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-chair"></i>
                        <div>
                            <span class="info-label">Total Seats</span>
                            <span class="info-value"><?= $s['total_seats'] ?> seats</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="detail-description">
                    <h3><i class="fas fa-align-left"></i> About this Seminar</h3>
                    <p><?= nl2br(htmlspecialchars($s['description'])) ?></p>
                </div>

                <!-- Seat Progress -->
                <div class="seat-bar" style="margin-top:1.5rem;">
                    <div class="seat-bar-label">
                        <span><strong>Seat Availability</strong></span>
                        <span><?= $s['booked_seats'] ?> booked / <?= $available ?> remaining</span>
                    </div>
                    <div class="seat-bar-track" style="height:12px;">
                        <div class="seat-bar-fill <?= $fillClass ?>"
                             style="width:<?= min($percent,100) ?>%"></div>
                    </div>
                </div>

                <!-- Availability Badge -->
                <div style="margin-top:1rem;">
                    <?php if ($available == 0): ?>
                        <span class="badge badge-full" style="font-size:0.95rem; padding:8px 18px;">
                            <i class="fas fa-times-circle"></i> Fully Booked
                        </span>
                    <?php elseif ($available <= 5): ?>
                        <span class="badge badge-limited" style="font-size:0.95rem; padding:8px 18px;">
                            <i class="fas fa-exclamation-circle"></i> Only <?= $available ?> seats left — Hurry!
                        </span>
                    <?php else: ?>
                        <span class="badge badge-available" style="font-size:0.95rem; padding:8px 18px;">
                            <i class="fas fa-check-circle"></i> <?= $available ?> seats available
                        </span>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- RIGHT: Booking Form -->
        <div class="booking-card">
            <div class="booking-card-header">
                <h2><i class="fas fa-ticket-alt"></i> Book Your Seat</h2>
                <p>Fill in your details to register</p>
            </div>

            <?php if ($available > 0): ?>

                <form action="booking.php" method="POST" class="booking-form">
                    <input type="hidden" name="seminar_id" value="<?= $s['id'] ?>">

                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" name="student_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> Student ID *</label>
                        <input type="text" name="student_id" placeholder="e.g. 221-15-5678" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" name="email" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="text" name="phone" placeholder="01XXXXXXXXX">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Department *</label>
                        <select name="department" required>
                            <option value="">-- Select Department --</option>
                            <option>Computer Science & Engineering</option>
                            <option>Software Engineering</option>
                            <option>Electrical & Electronic Engineering</option>
                            <option>Business Administration</option>
                            <option>English</option>
                            <option>Law</option>
                            <option>Pharmacy</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:14px;">
                        <i class="fas fa-check-circle"></i> Confirm Booking
                    </button>
                </form>

            <?php else: ?>
                <div style="text-align:center; padding:2rem; color:var(--muted);">
                    <i class="fas fa-calendar-times" style="font-size:3rem; color:#d1d5db; margin-bottom:1rem;"></i>
                    <h3>Booking Closed</h3>
                    <p>This seminar is fully booked. Please check other available seminars.</p>
                    <a href="index.php" class="btn btn-secondary" style="margin-top:1rem;">
                        <i class="fas fa-search"></i> Find Other Seminars
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>