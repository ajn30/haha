<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Handle Borrow Book request
if (isset($_POST['borrow_book'])) {
    $resource_id = $_POST['resource_id'];
    $borrow_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days')); // Due date set to 14 days from the current date
    
    // Insert a new transaction into the Transactions table
    $stmt = $pdo->prepare("
        INSERT INTO Transactions (UserID, ResourceID, BorrowedAt, DueDate)
        VALUES (:user_id, :resource_id, :borrowed_at, :due_date)
    ");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'resource_id' => $resource_id,
        'borrowed_at' => $borrow_date,
        'due_date' => $due_date
    ]);
    echo "<div class='alert alert-success'>You have successfully borrowed the book.</div>";
}

// Handle Return Book request
if (isset($_POST['return_book'])) {
    $transaction_id = $_POST['transaction_id'];

    // Update the transaction status to "returned"
    $stmt = $pdo->prepare("
        UPDATE Transactions SET ReturnedAt = :returned_at WHERE TransactionID = :transaction_id
    ");
    $stmt->execute([
        'returned_at' => date('Y-m-d'),
        'transaction_id' => $transaction_id
    ]);
    echo "<div class='alert alert-success'>You have successfully returned the book.</div>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header">
                <h1>Welcome, <?php echo $_SESSION['name']; ?> (Student)</h1>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="search.php">Search Library Resources</a></li>
                            <li><a href="transactions.php">View Borrowing History</a></li>
                            <li><a href="#view_all_books" data-bs-toggle="collapse">View All Books</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h3>Currently Borrowed Books</h3>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Accession Number</th>
                                    <th>Title</th>
                                    <th>Borrowed Date</th>
                                    <th>Due Date</th>
                                    <th>Return</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT Transactions.TransactionID, LibraryResources.AccessionNumber, LibraryResources.Title, Transactions.BorrowedAt, Transactions.DueDate
                                    FROM Transactions
                                    JOIN LibraryResources ON Transactions.ResourceID = LibraryResources.ResourceID
                                    WHERE Transactions.UserID = :user_id AND Transactions.ReturnedAt IS NULL
                                ");
                                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                                $transactions = $stmt->fetchAll();
                                
                                foreach ($transactions as $transaction):
                                ?>
                                    <tr>
                                        <td><?php echo $transaction['AccessionNumber']; ?></td>
                                        <td><?php echo $transaction['Title']; ?></td>
                                        <td><?php echo $transaction['BorrowedAt']; ?></td>
                                        <td><?php echo $transaction['DueDate']; ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="transaction_id" value="<?php echo $transaction['TransactionID']; ?>">
                                                <button type="submit" name="return_book" class="btn btn-danger">Return Book</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h3>Borrow a Book</h3>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="resource_id" class="form-label">Select a Book</label>
                                <select name="resource_id" class="form-select" required>
                                    <option value="">Select a book</option>
                                    <?php
                                    // Get all available books
                                    $stmt = $pdo->prepare("SELECT ResourceID, Title FROM LibraryResources WHERE ResourceID NOT IN (SELECT ResourceID FROM Transactions WHERE UserID = :user_id AND ReturnedAt IS NULL)");
                                    $stmt->execute(['user_id' => $_SESSION['user_id']]);
                                    $books = $stmt->fetchAll();

                                    foreach ($books as $book):
                                    ?>
                                        <option value="<?php echo $book['ResourceID']; ?>"><?php echo $book['Title']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="borrow_book" class="btn btn-primary">Borrow Book</button>
                        </form>
                    </div>
                </div>

                <!-- View All Books Section -->
                <div id="view_all_books" class="collapse mt-4">
                    <h3>All Library Books</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Accession Number</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Availability</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get all books from the LibraryResources table
                            $stmt = $pdo->prepare("SELECT ResourceID, AccessionNumber, Title, Category FROM LibraryResources");
                            $stmt->execute();
                            $books = $stmt->fetchAll();

                            foreach ($books as $book):
                            ?>
                                <tr>
                                    <td><?php echo $book['AccessionNumber']; ?></td>
                                    <td><?php echo $book['Title']; ?></td>
                                    <td><?php echo $book['Category']; ?></td>
                                    <td>
                                        <?php
                                        // Check if the book is available (not borrowed)
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Transactions WHERE ResourceID = :resource_id AND ReturnedAt IS NULL");
                                        $stmt->execute(['resource_id' => $book['ResourceID']]);
                                        $borrowed_count = $stmt->fetchColumn();
                                        
                                        if ($borrowed_count > 0) {
                                            echo "Currently Borrowed";
                                        } else {
                                            echo "Available";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
