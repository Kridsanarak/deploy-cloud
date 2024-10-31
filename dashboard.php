<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>
    <nav>
        <ul>
            <li><a href="logout.php">Logout</a></li> <!-- Add Logout link -->
            <li>Welcome, <?php echo $_SESSION['full_name']; ?></li>
        </ul>
    </nav>

    <h1>Welcome to the Dashboard, <?php echo $_SESSION['full_name']; ?>!</h1>
</body>

</html>