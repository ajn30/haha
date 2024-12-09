<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_POST['user_id'];
    $resourceID = $_POST['resource_id'];
    $dueDate = $_POST['due_date'];

    // Check borrowing limit
    $stmt = $pdo->prepare("SELECT MaxBooks, 
        (SELECT COUNT(*) FROM Transactions WHERE UserID = :userID AND ReturnedAt IS NULL) AS CurrentBorrowed 
        FROM Users WHERE UserID = :userID");
    $stmt->execute(['userID' => $userID]);
    $result = $stmt->fetch();

    if (!$result || $result['CurrentBorrowed'] >= $result['MaxBooks']) {
        echo "User has reached the borrowing limit.";
    } else {
        try {
            $transactionStmt = $pdo->prepare("INSERT INTO Transactions (UserID, ResourceID, DueDate) VALUES (:userID, :resourceID, :dueDate)");
            $transactionStmt->execute(['userID' => $userID, 'resourceID' => $resourceID, 'dueDate' => $dueDate]);

            echo "Borrowing transaction recorded successfully.";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
<!-- HTML form for borrowing -->
<form method="POST">
    <label>User ID:</label> <input type="text" name="user_id" required><br>
    <label>Resource ID:</label> <input type="text" name="resource_id" required><br>
    <label>Due Date:</label> <input type="date" name="due_date" required><br>
    <button type="submit">Borrow</button>
</form>
