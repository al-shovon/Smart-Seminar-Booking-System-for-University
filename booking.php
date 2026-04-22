<?php include 'includes/db.php'; ?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $seminar_id   = (int) $_POST['seminar_id'];
    $student_name = mysqli_real_escape_string($conn, trim($_POST['student_name']));
    $student_id   = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone        = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $department   = mysqli_real_escape_string($conn, trim($_POST['department']));

    $errors = [];

    if (empty($student_name)) $errors[] = "Full name is required.";
    if (empty($student_id))   $errors[] = "Student ID is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Valid email required.";

    if (empty($department)) $errors[] = "Department required.";

    $sem = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM seminars WHERE id=$seminar_id AND status='active'")
    );

    if (!$sem) {
        $errors[] = "Seminar not found.";
    } elseif ($sem['booked_seats'] >= $sem['total_seats']) {
        $errors[] = "Seminar full.";
    }

    $dup = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT id FROM bookings WHERE seminar_id=$seminar_id AND student_id='$student_id'")
    );

    if ($dup) {
        $errors[] = "Already booked.";
    }

    if (empty($errors)) {

        // INSERT FIRST
        mysqli_query($conn, "
            INSERT INTO bookings 
            (seminar_id, student_name, student_id, email, phone, department)
            VALUES
            ($seminar_id, '$student_name', '$student_id', '$email', '$phone', '$department')
        ");

        // IMMEDIATELY GET INSERT ID
        $booking_id = mysqli_insert_id($conn);

        // THEN UPDATE SEAT
        mysqli_query($conn,
            "UPDATE seminars SET booked_seats = booked_seats + 1 WHERE id=$seminar_id"
        );

        // REDIRECT
        header("Location: booking_confirm.php?booking_id=$booking_id");
        exit;

    } else {

        session_start();
        $_SESSION['booking_errors'] = $errors;

        header("Location: seminar_detail.php?id=$seminar_id");
        exit;
    }

} else {
    header("Location: index.php");
    exit;
}
?>