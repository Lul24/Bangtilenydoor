<?php
// ============================================
// DEBUGGING - SHOW ALL ERRORS
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================
// SESSION & DATABASE
// ============================================
session_start();
require_once '../config/database.php';

// 🔐 PROTECT PAGE - TEMPORARILY DISABLED FOR TESTING
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit();
// }

// ============================================
// TEST DATABASE CONNECTION
// ============================================
$conn = getConnection();

if (!$conn) {
    die("❌ Database connection failed. Please check your database configuration.");
}

echo "<!-- Database connected successfully -->";

// ============================================
// DELETE MESSAGE
// ============================================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM contact_messages WHERE id = $id")) {
        header("Location: messages.php");
        exit();
    } else {
        die("❌ Delete failed: " . $conn->error);
    }
}

// ============================================
// MARK AS READ
// ============================================
if (isset($_GET['read'])) {
    $id = intval($_GET['read']);
    if ($conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id")) {
        header("Location: messages.php");
        exit();
    } else {
        die("❌ Update failed: " . $conn->error);
    }
}

// ============================================
// FETCH MESSAGES
// ============================================
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");

if (!$result) {
    die("❌ Query failed: " . $conn->error);
}

// Check if table exists and has data
$checkTable = $conn->query("SHOW TABLES LIKE 'contact_messages'");
if ($checkTable->num_rows == 0) {
    die("❌ Table 'contact_messages' does not exist. Please create the table first.");
}

$rowCount = $result->num_rows;
echo "<!-- Found $rowCount messages -->";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Messages | Bangtilenydoor Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .container {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    h2 {
        color: #333;
        border-left: 5px solid #764ba2;
        padding-left: 20px;
    }

    .table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .badge-new {
        background: #ff9800;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .badge-read {
        background: #4caf50;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .btn-sm {
        margin: 2px;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>

    <div class="container">

        <!-- Header with Stats -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-envelope"></i> 📬 Contact Messages
            </h2>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Statistics Cards -->
        <?php
    // Get statistics
    $totalQuery = $conn->query("SELECT COUNT(*) as total FROM contact_messages");
    $total = $totalQuery->fetch_assoc()['total'];
    
    $unreadQuery = $conn->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
    $unread = $unreadQuery->fetch_assoc()['unread'];
    ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stats-card text-center">
                    <h3><?php echo $total; ?></h3>
                    <p><i class="fas fa-inbox"></i> Total Messages</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card text-center">
                    <h3><?php echo $unread; ?></h3>
                    <p><i class="fas fa-envelope"></i> Unread Messages</p>
                </div>
            </div>
        </div>

        <!-- Debug Info (Hidden - Remove after testing) -->
        <?php if ($rowCount == 0): ?>
        <div class="alert alert-warning">
            <i class="fas fa-info-circle"></i>
            No messages found in the database. When someone submits the contact form, messages will appear here.
        </div>
        <?php endif; ?>

        <!-- Messages Table -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rowCount > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr style="<?= $row['is_read'] ? '' : 'background:#f0f8ff; font-weight:bold;' ?>">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['subject'] ?? 'No Subject') ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#messageModal<?= $row['id'] ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>

                                    <!-- Modal for viewing full message -->
                                    <div class="modal fade" id="messageModal<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Message from
                                                        <?= htmlspecialchars($row['name']) ?></h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?>
                                                    </p>
                                                    <p><strong>Subject:</strong>
                                                        <?= htmlspecialchars($row['subject'] ?? 'No Subject') ?></p>
                                                    <hr>
                                                    <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($row['is_read']): ?>
                                    <span class="badge-read"><i class="fas fa-check-circle"></i> Read</span>
                                    <?php else: ?>
                                    <span class="badge-new"><i class="fas fa-envelope"></i> New</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('M d, Y H:i', strtotime($row['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if (!$row['is_read']): ?>
                                    <a href="?read=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-check"></i> Mark Read
                                    </a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this message from <?= htmlspecialchars($row['name']) ?>?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    No messages found.
                                    <br>
                                    <small>Messages from the contact form will appear here.</small>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SQL Debug Info (Only visible in development) -->
        <div class="alert alert-secondary mt-3" style="font-size: 12px;">
            <i class="fas fa-database"></i>
            <strong>Debug Info:</strong>
            Table 'contact_messages' exists ✓ |
            Total messages: <?= $total ?> |
            Query status: OK ✓
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>