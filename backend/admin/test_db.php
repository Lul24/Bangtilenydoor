<?php
require 'backend/config/database.php';

$conn = getConnection();

if ($conn) {
    echo "✅ Database Connected Successfully!";
} else {
    echo "❌ Connection Failed!";
}
?>