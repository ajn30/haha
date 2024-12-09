<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Check if 'BookID' is passed in the URL (GET request)
if (isset($_GET['BookID'])) {
    $bookID = $_GET['BookID'];

    // Example: Fetch the book from the database (if needed)
    $stmt = $pdo->prepare("SELECT * FROM books WHERE BookID = :bookID");
    $stmt->execute(['bookID' => $bookID]);
    $book = $stmt->fetch();

    // Handle book details or further processing
} else {
    echo "No BookID provided!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        /* Custom styles for the page */
        .content-wrapper {
            padding: 20px;
        }
        .content-header {
            font-size: 1.8rem;
            color: #007bff;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper">
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">Admin Dashboard</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="manage_users.php" class="nav-link">
                            <i class="fas fa-users"></i>
                            <p>Manage Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage_books.php" class="nav-link">
                            <i class="fas fa-book"></i>
                            <p>Manage Books</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <p>View Reports</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="addBook.php" class="nav-link">
                            <i class="fas fa-plus"></i>
                            <p>Add Book</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <h1 class="content-header">Manage Users</h1>
            <p class="lead">Here you can manage users.</p>

            <!-- Check if the BookID is set, then display the book details -->
            <?php if (isset($book)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Book Details</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Book ID:</strong> <?php echo $book['BookID']; ?></p>
                        <p><strong>Title:</strong> <?php echo $book['Title']; ?></p>
                        <p><strong>Author:</strong> <?php echo $book['Author']; ?></p>
                        <p><strong>Publisher:</strong> <?php echo $book['Publisher']; ?></p>
                        <p><strong>Year Published:</strong> <?php echo $book['YearPublished']; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- AdminLTE Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
