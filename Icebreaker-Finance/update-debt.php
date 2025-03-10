<?php
require __DIR__ . '/model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are set
    if (isset($_POST['debt_id'], $_POST['debt_name'], $_POST['debt_amount'], $_POST['min_payment'], $_POST['interest_rate'])) {

        $id = intval($_POST['debt_id']);
        $debt_name = trim($_POST['debt_name']);
        $debt_amount = floatval($_POST['debt_amount']);
        $min_payment = floatval($_POST['min_payment']);
        $interest_rate = floatval($_POST['interest_rate']);

        // Validate input values
        if ($id <= 0 || $debt_amount <= 0 || $min_payment <= 0 || $interest_rate < 0) {
            echo json_encode(["success" => false, "message" => "Invalid input values"]);
            exit;
        }

        try {
            // Update query
            $sql = "UPDATE debt_lookup SET debt_name = :debt_name, amount_owed = :debt_amount,
                    min_payment = :min_payment, interest_rate = :interest_rate WHERE debt_id = :id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':debt_name', $debt_name, PDO::PARAM_STR);
            $stmt->bindParam(':debt_amount', $debt_amount, PDO::PARAM_STR);
            $stmt->bindParam(':min_payment', $min_payment, PDO::PARAM_STR);
            $stmt->bindParam(':interest_rate', $interest_rate, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Debt updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Database update failed"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
