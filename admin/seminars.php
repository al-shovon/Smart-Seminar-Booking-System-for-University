<?php include 'auth_check.php'; ?>
<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seminars — Admin Panel</title>
    <link rel="stylesheet" href="/seminar_system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'admin_nav.php'; ?>

<?php
// ── Delete Action ─────────────────────────────────────────
if (isset($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];

    // আগে এই seminar এর সব booking মুছো
    mysqli_query($conn, "DELETE FROM bookings WHERE seminar_id=$del_id");

    // তারপর seminar মুছো
    mysqli_query($conn, "DELETE FROM seminars WHERE id=$del_id");

    header("Location: seminars.php?msg=deleted");
    exit;
}

// ── Cancel/Activate Toggle ────────────────────────────────
if (isset($_GET['toggle'])) {
    $tog_id = (int) $_GET['toggle'];
    $sem    = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT status FROM seminars WHERE id=$tog_id")
              );
    $newStatus = ($sem['status'] === 'active') ? 'cancelled' : 'active';
    mysqli_query($conn, "UPDATE seminars SET status='$newStatus' WHERE id=$tog_id");
    header("Location: seminars.php?msg=updated");
    exit;
}

// ── Search / Filter ───────────────────────────────────────
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$where = [];
if ($search !== '') {
    $where[] = "(title LIKE '%$search%' OR speaker LIKE '%$search%' OR venue LIKE '%$search%')";
}
if ($filter === 'active')    $where[] = "status='active'";
if ($filter === 'cancelled') $where[] = "status='cancelled'";

$whereSQL = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$result = mysqli_query($conn,
    "SELECT * FROM seminars $whereSQL ORDER BY seminar_date DESC"
);

// ── Counts for filter badges ──────────────────────────────
$count_all       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM seminars"))['c'];
$count_active    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM seminars WHERE status='active'"))['c'];
$count_cancelled = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM seminars WHERE status='cancelled'"))['c'];
?>

<div class="admin-layout">
    <?php include 'sidebar.php'; ?>

    <main class="admin-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1><i class="fas fa-calendar-alt"></i> Manage Seminars</h1>
                <p>Create, edit, and manage all university seminars</p>
            </div>
            <a href="add_seminar.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Seminar
            </a>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_GET['msg'])): ?>
            <?php
                $msgs = [
                    'added'   => ['success', 'fa-check-circle',    'Seminar added successfully!'],
                    'updated' => ['success', 'fa-check-circle',    'Seminar updated successfully!'],
                    'deleted' => ['error',   'fa-trash-alt',       'Seminar deleted successfully!'],
                ];
                $m = $msgs[$_GET['msg']] ?? null;
            ?>
            <?php if ($m): ?>
                <div class="alert alert-<?= $m[0] ?>">
                    <i class="fas <?= $m[1] ?>"></i> <?= $m[2] ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="seminars.php" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                All <span class="tab-count"><?= $count_all ?></span>
            </a>
            <a href="seminars.php?filter=active" class="filter-tab <?= $filter === 'active' ? 'active' : '' ?>">
                Active <span class="tab-count green"><?= $count_active ?></span>
            </a>
            <a href="seminars.php?filter=cancelled" class="filter-tab <?= $filter === 'cancelled' ? 'active' : '' ?>">
                Cancelled <span class="tab-count red"><?= $count_cancelled ?></span>
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" class="admin-search">
            <?php if ($filter !== 'all'): ?>
                <input type="hidden" name="filter" value="<?= $filter ?>">
            <?php endif; ?>
            <div class="search-bar" style="max-width:100%; margin:0 0 1.5rem;">
                <input type="text" name="search" id="searchInput"
                       placeholder="Search by title, speaker, or venue..."
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <!-- Seminars Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Seminar Title</th>
                        <th>Speaker</th>
                        <th>Date & Time</th>
                        <th>Venue</th>
                        <th>Seats</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0):
                    $i = 1;
                    while ($s = mysqli_fetch_assoc($result)):
                        $available = $s['total_seats'] - $s['booked_seats'];
                        $pct       = ($s['booked_seats'] / $s['total_seats']) * 100;
                        $fClass    = $pct < 50 ? 'fill-low' : ($pct < 80 ? 'fill-medium' : 'fill-high');
                ?>
                    <tr>
                        <td><strong><?= $i++ ?></strong></td>

                        <td>
                            <div class="td-title">
                                <?= htmlspecialchars($s['title']) ?>
                            </div>
                        </td>

                        <td>
                            <span style="display:flex; align-items:center; gap:6px;">
                                <i class="fas fa-user-tie" style="color:var(--primary);"></i>
                                <?= htmlspecialchars($s['speaker']) ?>
                            </span>
                        </td>

                        <td>
                            <div style="font-size:0.88rem;">
                                <div><i class="fas fa-calendar" style="color:var(--primary); width:14px;"></i>
                                     <?= date('d M Y', strtotime($s['seminar_date'])) ?></div>
                                <div style="color:var(--muted);">
                                     <i class="fas fa-clock" style="width:14px;"></i>
                                     <?= date('h:i A', strtotime($s['seminar_time'])) ?>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span style="font-size:0.88rem;">
                                <i class="fas fa-map-marker-alt" style="color:var(--danger);"></i>
                                <?= htmlspecialchars($s['venue']) ?>
                            </span>
                        </td>

                        <td style="min-width:130px;">
                            <div style="font-size:0.82rem; color:var(--muted); margin-bottom:4px;">
                                <?= $s['booked_seats'] ?>/<?= $s['total_seats'] ?>
                                (<?= $available ?> left)
                            </div>
                            <div class="seat-bar-track">
                                <div class="seat-bar-fill <?= $fClass ?>"
                                     style="width:<?= min($pct,100) ?>%; height:6px; border-radius:10px;"></div>
                            </div>
                        </td>

                        <td>
                            <?php if ($s['status'] === 'active'): ?>
                                <span class="badge badge-available">
                                    <i class="fas fa-circle" style="font-size:0.5rem;"></i> Active
                                </span>
                            <?php else: ?>
                                <span class="badge badge-full">
                                    <i class="fas fa-circle" style="font-size:0.5rem;"></i> Cancelled
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="action-btns">
                                <!-- View Participants -->
                                <a href="participants.php?seminar_id=<?= $s['id'] ?>"
                                   class="action-btn view" title="View Participants">
                                    <i class="fas fa-users"></i>
                                </a>
                                <!-- Edit -->
                                <a href="edit_seminar.php?id=<?= $s['id'] ?>"
                                   class="action-btn edit" title="Edit Seminar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Toggle Status -->
                                <a href="seminars.php?toggle=<?= $s['id'] ?>"
                                   class="action-btn toggle" title="Toggle Status"
                                   onclick="return confirm('Change status of this seminar?')">
                                    <i class="fas fa-power-off"></i>
                                </a>
                                <!-- Delete -->
                                <a href="seminars.php?delete=<?= $s['id'] ?>"
                                   class="action-btn delete" title="Delete Seminar"
                                   onclick="return confirm('Delete this seminar and ALL its bookings? This cannot be undone!')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="8">
                            <div class="no-results">
                                <i class="fas fa-calendar-times"></i>
                                <h3>No seminars found</h3>
                                <p>Try a different search or add a new seminar.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

</body>
</html>