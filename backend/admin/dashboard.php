<?php
// ============================================
// ADMIN DASHBOARD
// ============================================

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$conn = getConnection();

// Get statistics
$stats = [];

// Total inquiries
$result = $conn->query("SELECT COUNT(*) as total FROM student_inquiries");
$stats['total_inquiries'] = $result->fetch_assoc()['total'];

// New inquiries
$result = $conn->query("SELECT COUNT(*) as total FROM student_inquiries WHERE status = 'new'");
$stats['new_inquiries'] = $result->fetch_assoc()['total'];

// Total subscribers
$result = $conn->query("SELECT COUNT(*) as total FROM newsletter_subscribers");
$stats['total_subscribers'] = $result->fetch_assoc()['total'];

// Total contact messages
$result = $conn->query("SELECT COUNT(*) as total FROM contact_messages WHERE is_read = 0");
$stats['unread_messages'] = $result->fetch_assoc()['total'];

// Recent inquiries
$recentInquiries = $conn->query("SELECT * FROM student_inquiries ORDER BY created_at DESC LIMIT 5");

// Recent subscribers
$recentSubscribers = $conn->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bangtilenydoor Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100%;
            background: #0a2b4e;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar nav a {
            display: block;
            padding: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-bottom: 5px;
            border-radius: 8px;
        }
        .sidebar nav a:hover, .sidebar nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #0a2b4e;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        .section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .section h3 {
            margin-bottom: 15px;
            color: #0a2b4e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .status-new {
            background: #f39c12;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-new { background: #f39c12; color: white; }
        .badge-read { background: #2ecc71; color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Bangtilenydoor Academy</h2>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="inquiries.php">Inquiries</a>
            <a href="subscribers.php">Subscribers</a>
            <a href="messages.php">Contact Messages</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo $_SESSION['admin_name']; ?>!</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_inquiries']; ?></div>
                <div class="stat-label">Total Inquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['new_inquiries']; ?></div>
                <div class="stat-label">New Inquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_subscribers']; ?></div>
                <div class="stat-label">Newsletter Subscribers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['unread_messages']; ?></div>
                <div class="stat-label">Unread Messages</div>
            </div>
        </div>
        
        <div class="section">
            <h3>Recent Inquiries</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Interest</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $recentInquiries->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo str_replace('_', ' ', $row['interest_type']); ?></td>
                        <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h3>Recent Subscribers</h3>
             <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Subscribed Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $recentSubscribers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['subscribed_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>