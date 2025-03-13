<?php
// Start the session to access user authentication data
session_start();

// Include database connection file
require __DIR__ . '/model/db.php';

// Check if user is authenticated
if (!isset($_SESSION['isLoggedIn'])) {
    // Return unauthorized response if not logged in
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'] ?? null;

// Process only POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $debtName = $_POST['debt_name'] ?? '';
    $debtType = $_POST['debt_type'] ?? '';
    $amountOwed = $_POST['debt_amount'] ?? 0;
    $balance = $amountOwed;  // Initial balance equals amount owed
    $minPayment = $_POST['min_payment'] ?? 0;
    $interestRate = $_POST['interest_rate'] ?? 0;
    $status = 'active';  // Default status for new debt
    $debtVis = 1;  // Visibility flag (1 = visible)

    // Validate input data
    if (empty($debtName) || empty($debtType) || $amountOwed <= 0 || $minPayment <= 0 || $interestRate < 0) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    try {
        // Prepare SQL statement with parameterized query to prevent SQL injection
        $stmt = $db->prepare("INSERT INTO debt_lookup
            (user_id, debt_type, amount_owed, balance, interest_rate, status,
            debt_name, debt_vis, min_payment, date_added)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())");

        // Execute the query with form data
        $stmt->execute([$userId, $debtType, $amountOwed, $balance, $interestRate,
                      $status, $debtName, $debtVis, $minPayment]);

        // Get the auto-generated debt ID and return success response
        $debtId = $db->lastInsertId();
        echo json_encode(["success" => true, "debt_id" => $debtId]);

    } catch (PDOException $e) {
        // Handle database errors and return error message
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
