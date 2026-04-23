<?php include 'auth_check.php'; ?>
<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants — Admin Panel</title>
    <link rel="stylesheet" href="/seminar_system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_nav.php'; ?>

<?php
// ── Delete / Cancel Booking ───────────────────────────────
if (isset($_GET['delete'])) {
    $del_id    = (int) $_GET['delete'];
    $seminar_id = (int) $_GET['sid'];

    // Booking
    mysqli_query($conn, "DELETE FROM bookings WHERE id=$del_id");

    // Seminar booked_seats
    mysqli_query($conn,
        "UPDATE seminars SET booked_seats = GREATEST(booked_seats - 1, 0)
         WHERE id=$seminar_id"
    );

    header("Location: participants.php?msg=deleted" .
           ($seminar_id ? "&seminar_id=$seminar_id" : ""));
    exit;
}

// ── CSV Export ────────────────────────────────────────────
if (isset($_GET['export'])) {
    $exp_sid = isset($_GET['seminar_id']) ? (int)$_GET['seminar_id'] : 0;
    $expWhere = $exp_sid ? "WHERE b.seminar_id=$exp_sid" : "";

    $exp = mysqli_query($conn,
        "SELECT b.id, s.title, b.student_name, b.student_id,
                b.email, b.phone, b.department,
                DATE_FORMAT(b.booked_at,'%d-%m-%Y %H:%i') as booked_at
         FROM bookings b
         JOIN seminars s ON b.seminar_id = s.id
         $expWhere
         ORDER BY b.booked_at DESC"
    );

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="participants_' .
           date('d-m-Y') . '.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['#','Seminar','Student Name',
                   'Student ID','Email','Phone','Department','Booked At']);
    $n = 1;
    while ($row = mysqli_fetch_assoc($exp)) {
        fputcsv($out, [
            $n++,
            $row['title'],
            $row['student_name'],
            $row['student_id'],
            $row['email'],
            $row['phone'],
            $row['department'],
            $row['booked_at'],
        ]);
    }
    fclose($out);
    exit;
}

// ── Filters ───────────────────────────────────────────────
$seminar_id = isset($_GET['seminar_id']) ? (int)$_GET['seminar_id'] : 0;
$search     = isset($_GET['search'])
              ? mysqli_real_escape_string($conn, trim($_GET['search']))
              : '';
$dept       = isset($_GET['dept'])
              ? mysqli_real_escape_string($conn, trim($_GET['dept']))
              : '';

$where = ["1=1"];
if ($seminar_id) $where[] = "b.seminar_id=$seminar_id";
if ($search !== '') {
    $where[] = "(b.student_name LIKE '%$search%'
                 OR b.student_id  LIKE '%$search%'
                 OR b.email       LIKE '%$search%')";
}
if ($dept !== '') $where[] = "b.department='$dept'";

$whereSQL = implode(' AND ', $where);

// ── Main Query ────────────────────────────────────────────
$result = mysqli_query($conn,
    "SELECT b.*, s.title AS seminar_title,
            s.seminar_date, s.seminar_time, s.venue
     FROM bookings b
     JOIN seminars s ON b.seminar_id = s.id
     WHERE $whereSQL
     ORDER BY b.booked_at DESC"
);

// ── Stats ─────────────────────────────────────────────────
$total_bookings = mysqli_num_rows($result);
mysqli_data_seek($result, 0);

// Seminar dropdown
$all_seminars = mysqli_query($conn,
    "SELECT id, title FROM seminars ORDER BY seminar_date DESC"
);

// Department list
$depts = mysqli_query($conn,
    "SELECT DISTINCT department FROM bookings ORDER BY department"
);

// Selected seminar info
$selected_seminar = null;
if ($seminar_id) {
    $selected_seminar = mysqli_fetch_assoc(
        mysqli_query($conn,
            "SELECT title, booked_seats, total_seats
             FROM seminars WHERE id=$seminar_id")
    );
}
?>

<div class="admin-layout">
    <?php include 'sidebar.php'; ?>

    <main class="admin-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1><i class="fas fa-users"></i> Participants</h1>
                <p>
                    <?php if ($selected_seminar): ?>
                        Showing bookings for:
                        <strong><?= htmlspecialchars($selected_seminar['title']) ?></strong>
                        &nbsp;—&nbsp;
                        <?= $selected_seminar['booked_seats'] ?>/<?= $selected_seminar['total_seats'] ?> booked
                    <?php else: ?>
                        All participant bookings across all seminars
                    <?php endif; ?>
                </p>
            </div>

            <!-- Export CSV Button -->
            <a href="participants.php?export=1<?= $seminar_id ? "&seminar_id=$seminar_id" : '' ?>"
               class="btn btn-primary">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
        </div>

        <!-- Alert -->
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-error">
                <i class="fas fa-trash-alt"></i> Booking cancelled successfully!
            </div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="participant-summary">
            <div class="summary-card">
                <i class="fas fa-ticket-alt"></i>
                <div>
                    <h3><?= $total_bookings ?></h3>
                    <p><?= $seminar_id ? 'Bookings (filtered)' : 'Total Bookings' ?></p>
                </div>
            </div>

            <?php
            // Department breakdown (filtered)
            mysqli_data_seek($result, 0);
            $dept_counts = [];
            while ($r = mysqli_fetch_assoc($result)) {
                $d = $r['department'];
                $dept_counts[$d] = ($dept_counts[$d] ?? 0) + 1;
            }
            arsort($dept_counts);
            $top_dept = array_key_first($dept_counts);
            mysqli_data_seek($result, 0);
            ?>

            <div class="summary-card">
                <i class="fas fa-building"></i>
                <div>
                    <h3><?= count($dept_counts) ?></h3>
                    <p>Departments Represented</p>
                </div>
            </div>

            <div class="summary-card">
                <i class="fas fa-star"></i>
                <div>
                    <h3 style="font-size:1rem; line-height:1.3;">
                        <?= $top_dept ? htmlspecialchars($top_dept) : 'N/A' ?>
                    </h3>
                    <p>Most Active Department</p>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" class="filter-form">
            <?php if ($seminar_id): ?>
                <input type="hidden" name="seminar_id" value="<?= $seminar_id ?>">
            <?php endif; ?>

            <div class="filter-bar">

                <!-- Search -->
                <div class="filter-item" style="flex:2;">
                    <input type="text" name="search"
                           placeholder="Search by name, student ID, or email..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>

                <!-- Seminar Filter -->
                <div class="filter-item">
                    <select name="seminar_id" onchange="this.form.submit()">
                        <option value="">All Seminars</option>
                        <?php
                        mysqli_data_seek($all_seminars, 0);
                        while ($sem = mysqli_fetch_assoc($all_seminars)):
                        ?>
                            <option value="<?= $sem['id'] ?>"
                                <?= $seminar_id == $sem['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sem['title']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Department Filter -->
                <div class="filter-item">
                    <select name="dept" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        <?php while ($d = mysqli_fetch_assoc($depts)): ?>
                            <option value="<?= htmlspecialchars($d['department']) ?>"
                                <?= $dept === $d['department'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['department']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Search Button -->
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>

                <!-- Clear Filters -->
                <?php if ($search || $dept || $seminar_id): ?>
                    <a href="participants.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>

            </div>
        </form>

        <!-- Participants Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Student ID</th>
                        <th>Department</th>
                        <th>Contact</th>
                        <th>Seminar</th>
                        <th>Booked At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                mysqli_data_seek($result, 0);
                if (mysqli_num_rows($result) > 0):
                    $i = 1;
                    while ($b = mysqli_fetch_assoc($result)):
                ?>
                    <tr>
                        <td><strong><?= $i++ ?></strong></td>

                        <!-- Student Name + Avatar -->
                        <td>
                            <div class="student-cell">
                                <div class="avatar-sm">
                                    <?= strtoupper(substr($b['student_name'], 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($b['student_name']) ?></span>
                            </div>
                        </td>

                        <!-- Student ID -->
                        <td>
                            <code class="student-id">
                                <?= htmlspecialchars($b['student_id']) ?>
                            </code>
                        </td>

                        <!-- Department -->
                        <td>
                            <span class="dept-badge">
                                <?= htmlspecialchars($b['department']) ?>
                            </span>
                        </td>

                        <!-- Contact -->
                        <td>
                            <div style="font-size:0.83rem; line-height:1.7;">
                                <div>
                                    <i class="fas fa-envelope"
                                       style="color:var(--primary); width:14px;"></i>
                                    <?= htmlspecialchars($b['email']) ?>
                                </div>
                                <?php if ($b['phone']): ?>
                                <div style="color:var(--muted);">
                                    <i class="fas fa-phone" style="width:14px;"></i>
                                    <?= htmlspecialchars($b['phone']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Seminar Info -->
                        <td>
                            <div style="font-size:0.83rem; line-height:1.7;">
                                <div style="font-weight:600; color:var(--primary);">
                                    <?= htmlspecialchars($b['seminar_title']) ?>
                                </div>
                                <div style="color:var(--muted);">
                                    <i class="fas fa-calendar" style="width:14px;"></i>
                                    <?= date('d M Y', strtotime($b['seminar_date'])) ?>
                                    &nbsp;
                                    <i class="fas fa-map-marker-alt" style="width:14px;"></i>
                                    <?= htmlspecialchars($b['venue']) ?>
                                </div>
                            </div>
                        </td>

                        <!-- Booked At -->
                        <td>
                            <div style="font-size:0.83rem; color:var(--muted);">
                                <i class="fas fa-clock"></i>
                                <?= date('d M Y', strtotime($b['booked_at'])) ?>
                                <br>
                                <?= date('h:i A', strtotime($b['booked_at'])) ?>
                            </div>
                        </td>

                        <!-- Delete Action -->
                        <td>
                            <a href="participants.php?delete=<?= $b['id'] ?>&sid=<?= $b['seminar_id'] ?><?= $seminar_id ? "&seminar_id=$seminar_id" : '' ?>"
                               class="action-btn delete" title="Cancel Booking"
                               onclick="return confirm('Cancel this booking for <?= addslashes($b['student_name']) ?>?\nThis will free up one seat.')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="8">
                            <div class="no-results">
                                <i class="fas fa-users-slash"></i>
                                <h3>No participants found</h3>
                                <p>Try adjusting your filters or search term.</p>
                                <?php if ($search || $dept || $seminar_id): ?>
                                    <a href="participants.php"
                                       class="btn btn-secondary"
                                       style="margin-top:1rem;">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Department Breakdown (bottom) -->
        <?php if (!empty($dept_counts) && $total_bookings > 0): ?>
        <div class="dept-breakdown">
            <h3><i class="fas fa-chart-bar"></i> Department Breakdown</h3>
            <div class="dept-bars">
                <?php foreach ($dept_counts as $dname => $dcount):
                    $dpct = round(($dcount / $total_bookings) * 100);
                ?>
                <div class="dept-bar-row">
                    <span class="dept-label">
                        <?= htmlspecialchars($dname) ?>
                    </span>
                    <div class="dept-bar-track">
                        <div class="dept-bar-fill"
                             style="width:<?= $dpct ?>%">
                        </div>
                    </div>
                    <span class="dept-count">
                        <?= $dcount ?> <small>(<?= $dpct ?>%)</small>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
