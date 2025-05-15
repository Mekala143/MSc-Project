<?php
session_start();
require_once "includes/db.php";
require_once "includes/session.php";
require_once "includes/functions.php";

$user_id = $_SESSION['user_id'];


$expense_sql = "SELECT SUM(amount) AS total FROM expenses WHERE user_id = ? AND MONTH(expense_date) = MONTH(CURDATE())";
$stmt = $conn->prepare($expense_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expense_result = $stmt->get_result()->fetch_assoc();
$total_expense = $expense_result['total'] ?? 0;


$budget_sql = "SELECT budget_amount FROM budgets WHERE user_id = ?";
$budget_stmt = $conn->prepare($budget_sql);
$budget_stmt->bind_param("i", $user_id);
$budget_stmt->execute();
$budget_result = $budget_stmt->get_result()->fetch_assoc();
$budget = $budget_result['budget_amount'] ?? 0;

$balance = $budget - $total_expense;


$category_data = [];
$cat_sql = "SELECT category, SUM(amount) AS total FROM expenses WHERE user_id = ? AND MONTH(expense_date) = MONTH(CURDATE()) GROUP BY category";
$cat_stmt = $conn->prepare($cat_sql);
$cat_stmt->bind_param("i", $user_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
while ($row = $cat_result->fetch_assoc()) {
    $category_data[$row['category']] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Expense Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include("templates/header.php"); ?>

<main>
    <section class="dashboard-container">
        <h2>Your Dashboard</h2>

        <div class="card-summary">
            <div class="card">
                <h3>Total Budget</h3>
                <p>₹<?php echo number_format($budget, 2); ?></p>
            </div>
            <div class="card">
                <h3>Expenses This Month</h3>
                <p>₹<?php echo number_format($total_expense, 2); ?></p>
            </div>
            <div class="card">
                <h3>Remaining Balance</h3>
                <p>₹<?php echo number_format($balance, 2); ?></p>
            </div>
        </div>

        <div class="actions">
            <a href="expenses/add-expense.php" class="btn">Add Expense</a>
            <a href="expenses/view-expenses.php" class="btn outline">View All Expenses</a>
            <a href="budget/set-budget.php" class="btn secondary">Set Budget</a>
            <a href="budget/budget-check.php" class="btn secondary">Check Budget</a>
            <a href="budget/update-budget.php" class="btn outline">Update Budget</a>
        </div>

        <div class="chart-section">
            <h3>Expense Analysis</h3>

            <!-- Hidden fields for JS -->
            <input type="hidden" id="userBudget" value="<?= $budget; ?>">
            <input type="hidden" id="totalSpent" value="<?= $total_expense; ?>">
            <input type="hidden" id="categoryData" value='<?= json_encode($category_data); ?>'>

            <!-- Canvas for charts -->
            <div style="max-width: 600px; margin-bottom: 2rem;">
                <canvas id="budgetChart"></canvas>
            </div>

            <div style="max-width: 600px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </section>
</main>

<?php include("templates/footer.php"); ?>
<script src="assets/js/budget-alert.js"></script>
<script src="assets/js/chart-visuals.js"></script>
</body>
</html>
