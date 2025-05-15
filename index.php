<?php

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include("templates/header.php"); ?>

    <main>
        <section class="hero">
            <h1>Welcome to Expense Manager</h1>
            <p>Track your daily spending, manage your budget, and gain control over your finances.</p>
            <div class="cta-buttons">
                <a href="users/register.php" class="btn">Get Started</a>
                <a href="users/login.php" class="btn secondary">Login</a>
            </div>
        </section>
    </main>

    <?php include("templates/footer.php"); ?>
</body>
</html>
