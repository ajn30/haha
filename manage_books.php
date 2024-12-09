<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Fetch all books from the database
$stmt = $pdo->prepare("SELECT ResourceID, Title, Author, ISBN, Publisher, YearPublished, Available FROM LibraryResources");
$stmt->execute();
$books = $stmt->fetchAll();

// Add a new book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $publisher = $_POST['publisher'];
    $year_published = $_POST['year_published'];

    // Insert the new book into the database
    $stmt = $pdo->prepare("INSERT INTO LibraryResources (Title, Author, ISBN, Publisher, YearPublished) VALUES (:title, :author, :isbn, :publisher, :year_published)");
    $stmt->execute([
        'title' => $title,
        'author' => $author,
        'isbn' => $isbn,
        'publisher' => $publisher,
        'year_published' => $year_published
    ]);
    echo "<div class='alert alert-success'>Book added successfully!</div>";
}

// Delete a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book'])) {
    $resource_id = $_POST['resource_id'];

    // Delete the book from the database
    $stmt = $pdo->prepare("DELETE FROM LibraryResources WHERE ResourceID = :resource_id");
    $stmt->execute(['resource_id' => $resource_id]);
    echo "<div class='alert alert-success'>Book deleted successfully!</div>";
}

// Update book availability (for example, when borrowed or returned)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_availability'])) {
    $resource_id = $_POST['resource_id'];
    $available = $_POST['available'];

    // Update the availability status
    $stmt = $pdo->prepare("UPDATE LibraryResources SET Available = :available WHERE ResourceID = :resource_id");
    $stmt->execute([
        'available' => $available,
        'resource_id' => $resource_id
    ]);
    echo "<div class='alert alert-success'>Book availability updated!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        .sidebar-dark-primary {
            background-color: #343a40 !important;
        }
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
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
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
                        <a href="books.php" class="nav-link">
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
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <h1 class="content-header">Manage Books</h1>

            <!-- Add Book Form -->
            <h3>Add New Book</h3>
            <form method="POST" action="manage_books.php">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" name="author" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" name="isbn" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="publisher">Publisher</label>
                    <input type="text" name="publisher" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="year_published">Year Published</label>
                    <input type="number" name="year_published" class="form-control" required>
                </div>
                <button type="submit" name="add_book" class="btn btn-primary">Add Book</button>
            </form>

            <!-- Display Books -->
            <h3 class="mt-5">All Books</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Resource ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Publisher</th>
                        <th>Year Published</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $book['ResourceID']; ?></td>
                            <td><?php echo $book['Title']; ?></td>
                            <td><?php echo $book['Author']; ?></td>
                            <td><?php echo $book['ISBN']; ?></td>
                            <td><?php echo $book['Publisher']; ?></td>
                            <td><?php echo $book['YearPublished']; ?></td>
                            <td><?php echo $book['Available'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <!-- Delete Book Form -->
                                <form method="POST" action="manage_books.php" style="display:inline;">
                                    <input type="hidden" name="resource_id" value="<?php echo $book['ResourceID']; ?>">
                                    <button type="submit" name="delete_book" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <!-- Update Availability Form -->
                                <form method="POST" action="manage_books.php" style="display:inline;">
                                    <input type="hidden" name="resource_id" value="<?php echo $book['ResourceID']; ?>">
                                    <select name="available" class="form-select btn btn-info btn-sm">
                                        <option value="1" <?php echo $book['Available'] == 1 ? 'selected' : ''; ?>>Available</option>
                                        <option value="0" <?php echo $book['Available'] == 0 ? 'selected' : ''; ?>>Not Available</option>
                                    </select>
                                    <button type="submit" name="update_availability" class="btn btn-success btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- AdminLTE Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
