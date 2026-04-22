<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
// booking_id URL থেকে নাও
$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;

// যদি booking_id না থাকে
if ($booking_id <= 0) {
    echo "
    <div class='section'>
        <div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i> Invalid booking request!
        </div>
    </div>";
    
    include 'includes/footer.php';
    exit;
}

// Booking + Seminar Data আনো
$query = "
SELECT 
    b.*, 
    s.title, 
    s.speaker, 
    s.venue, 
    s.seminar_date, 
    s.seminar_time
FROM bookings b
JOIN seminars s ON b.seminar_id = s.id
WHERE b.id = '$booking_id'
LIMIT 1
";

$result = mysqli_query($conn, $query);

// যদি booking না পাওয়া যায়
if (!$result || mysqli_num_rows($result) == 0) {
    echo "
    <div class='section'>
        <div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i> Booking not found!
        </div>
    </div>";

    include 'includes/footer.php';
    exit;
}

// Data fetch
$b = mysqli_fetch_assoc($result);
?>

<section class="section" style="max-width:680px; margin:0 auto;">

    <div class="confirm-box">

        <!-- Success Icon -->
        <div class="confirm-icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1>Booking Confirmed!</h1>

        <p class="confirm-subtitle">
            Your seat has been successfully reserved. See you at the seminar! 🎓
        </p>

        <!-- Booking Ref -->
        <div class="confirm-ref">
            Booking Reference:
            <strong>#<?= str_pad($b['id'], 6, '0', STR_PAD_LEFT); ?></strong>
        </div>

        <!-- Booking Details -->
        <div class="confirm-table">

            <div class="confirm-row">
                <span><i class="fas fa-calendar-check"></i> Seminar</span>
                <strong><?= htmlspecialchars($b['title']); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-user-tie"></i> Speaker</span>
                <strong><?= htmlspecialchars($b['speaker']); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-calendar"></i> Date</span>
                <strong><?= date('d F Y', strtotime($b['seminar_date'])); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-clock"></i> Time</span>
                <strong><?= date('h:i A', strtotime($b['seminar_time'])); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-map-marker-alt"></i> Venue</span>
                <strong><?= htmlspecialchars($b['venue']); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-user"></i> Name</span>
                <strong><?= htmlspecialchars($b['student_name']); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-id-card"></i> Student ID</span>
                <strong><?= htmlspecialchars($b['student_id']); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-building"></i> Department</span>
                <strong><?= htmlspecialchars($b['department']); ?></strong>
            </div>

            <div class="confirm-row">
                <span><i class="fas fa-envelope"></i> Email</span>
                <strong><?= htmlspecialchars($b['email']); ?></strong>
            </div>

        </div>

        <!-- Note -->
        <div class="confirm-note">
            <i class="fas fa-info-circle"></i>
            Please save your Booking Reference number. You may need it at the venue entrance.
        </div>

        <!-- Buttons -->
        <div class="confirm-actions">

            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Back to Home
            </a>

            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print / Save
            </button>

        </div>

    </div>

</section>

<?php include 'includes/footer.php'; ?>