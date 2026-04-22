<?php include 'auth_check.php'; ?>
<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Seminar — Admin Panel</title>
    <link rel="stylesheet" href="/seminar_system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_nav.php'; ?>

<?php
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Input গুলো নাও
    $title       = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $speaker     = mysqli_real_escape_string($conn, trim($_POST['speaker']));
    $venue       = mysqli_real_escape_string($conn, trim($_POST['venue']));
    $date        = $_POST['seminar_date'];
    $time        = $_POST['seminar_time'];
    $seats       = (int) $_POST['total_seats'];

    // Validation
    if (empty($title))       $errors[] = "Seminar title is required.";
    if (empty($speaker))     $errors[] = "Speaker name is required.";
    if (empty($venue))       $errors[] = "Venue is required.";
    if (empty($date))        $errors[] = "Date is required.";
    if (empty($time))        $errors[] = "Time is required.";
    if ($seats < 1)          $errors[] = "Total seats must be at least 1.";
    if ($date < date('Y-m-d')) $errors[] = "Seminar date cannot be in the past.";

    if (empty($errors)) {
        mysqli_query($conn,
            "INSERT INTO seminars
             (title, description, speaker, venue, seminar_date, seminar_time, total_seats)
             VALUES
             ('$title','$description','$speaker','$venue','$date','$time',$seats)"
        );
        header("Location: seminars.php?msg=added");
        exit;
    }
}
?>

<div class="admin-layout">
    <?php include 'sidebar.php'; ?>

    <main class="admin-content">

        <div class="page-header">
            <div>
                <h1><i class="fas fa-plus-circle"></i> Add New Seminar</h1>
                <p>Fill in the details to create a new seminar event</p>
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
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top:8px; padding-left:20px;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= $e ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="form-card">
            <form method="POST">

                <!-- Row 1: Title -->
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Seminar Title *</label>
                    <input type="text" name="title"
                           placeholder="e.g. AI in Modern Education"
                           value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"
                           required>
                </div>

                <!-- Row 2: Speaker + Venue -->
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user-tie"></i> Speaker Name *</label>
                        <input type="text" name="speaker"
                               placeholder="e.g. Dr. Karim Rahman"
                               value="<?= isset($_POST['speaker']) ? htmlspecialchars($_POST['speaker']) : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Venue *</label>
                        <input type="text" name="venue"
                               placeholder="e.g. Auditorium A"
                               value="<?= isset($_POST['venue']) ? htmlspecialchars($_POST['venue']) : '' ?>"
                               required>
                    </div>
                </div>

                <!-- Row 3: Date + Time + Seats -->
                <div class="form-row three-col">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Seminar Date *</label>
                        <input type="date" name="seminar_date"
                               min="<?= date('Y-m-d') ?>"
                               value="<?= isset($_POST['seminar_date']) ? $_POST['seminar_date'] : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Start Time *</label>
                        <input type="time" name="seminar_time"
                               value="<?= isset($_POST['seminar_time']) ? $_POST['seminar_time'] : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-chair"></i> Total Seats *</label>
                        <input type="number" name="total_seats" min="1" max="1000"
                               placeholder="e.g. 50"
                               value="<?= isset($_POST['total_seats']) ? $_POST['total_seats'] : '' ?>"
                               required>
                    </div>
                </div>

                <!-- Row 4: Description -->
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" rows="5"
                              placeholder="Write a brief description of the seminar..."><?=
                        isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''
                    ?></textarea>
                </div>

                <!-- Submit -->
                <div class="form-actions">
                    <a href="seminars.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Seminar
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>

</body>
</html>
