<?php include 'auth_check.php'; ?>
<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Seminar — Admin Panel</title>
    <link rel="stylesheet" href="/seminar_system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_nav.php'; ?>

<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$s  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM seminars WHERE id=$id"));

if (!$s) {
    echo "<div class='section'>
          <div class='alert alert-error'>
          <i class='fas fa-exclamation-circle'></i> Seminar not found!</div></div>";
    include '../includes/footer.php';
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $speaker     = mysqli_real_escape_string($conn, trim($_POST['speaker']));
    $venue       = mysqli_real_escape_string($conn, trim($_POST['venue']));
    $date        = $_POST['seminar_date'];
    $time        = $_POST['seminar_time'];
    $seats       = (int) $_POST['total_seats'];
    $status      = $_POST['status'];

    // Validation
    if (empty($title))   $errors[] = "Title is required.";
    if (empty($speaker)) $errors[] = "Speaker is required.";
    if (empty($venue))   $errors[] = "Venue is required.";
    if ($seats < $s['booked_seats'])
        $errors[] = "Total seats cannot be less than already booked seats ({$s['booked_seats']}).";

    if (empty($errors)) {
        mysqli_query($conn,
            "UPDATE seminars SET
                title='$title',
                description='$description',
                speaker='$speaker',
                venue='$venue',
                seminar_date='$date',
                seminar_time='$time',
                total_seats=$seats,
                status='$status'
             WHERE id=$id"
        );
        header("Location: seminars.php?msg=updated");
        exit;
    }

    // Error হলে POST data দিয়ে $s update করো preview এর জন্য
    $s = array_merge($s, $_POST);
}
?>

<div class="admin-layout">
    <?php include 'sidebar.php'; ?>

    <main class="admin-content">

        <div class="page-header">
            <div>
                <h1><i class="fas fa-edit"></i> Edit Seminar</h1>
                <p>Update the seminar details below</p>
            </div>
            <a href="seminars.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <div>
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Please fix these errors:</strong>
                    <ul style="margin-top:8px; padding-left:20px;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= $e ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Booking Info Banner -->
        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            This seminar has <strong><?= $s['booked_seats'] ?> confirmed bookings</strong>
            out of <?= $s['total_seats'] ?> total seats.
            You cannot set total seats below the booked count.
        </div>

        <!-- Form -->
        <div class="form-card">
            <form method="POST">

                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Seminar Title *</label>
                    <input type="text" name="title"
                           value="<?= htmlspecialchars($s['title']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user-tie"></i> Speaker Name *</label>
                        <input type="text" name="speaker"
                               value="<?= htmlspecialchars($s['speaker']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Venue *</label>
                        <input type="text" name="venue"
                               value="<?= htmlspecialchars($s['venue']) ?>" required>
                    </div>
                </div>

                <div class="form-row three-col">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Date *</label>
                        <input type="date" name="seminar_date"
                               value="<?= $s['seminar_date'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Time *</label>
                        <input type="time" name="seminar_time"
                               value="<?= $s['seminar_time'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-chair"></i> Total Seats *</label>
                        <input type="number" name="total_seats"
                               min="<?= $s['booked_seats'] ?>"
                               value="<?= $s['total_seats'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Status *</label>
                    <select name="status">
                        <option value="active"    <?= $s['status']==='active'    ? 'selected':'' ?>>Active</option>
                        <option value="cancelled" <?= $s['status']==='cancelled' ? 'selected':'' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" rows="5"><?=
                        htmlspecialchars($s['description'])
                    ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="seminars.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Seminar
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>

</body>
</html>