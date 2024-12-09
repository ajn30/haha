<?php
session_start();
if (!isset($_SESSION['membership_type']) || $_SESSION['membership_type'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Get the book ID from the URL parameter
if (!isset($_GET['id'])) {
    echo "Book ID is required!";
    exit;
}

$book_id = $_GET['id'];

// Fetch the book details from the database
$stmt = $pdo->prepare("SELECT * FROM Books WHERE BookID = :id");
$stmt->execute(['id' => $book_id]);
$book = $stmt->fetch();

if (!$book) {
    echo "Book not found!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated book details from the form
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $status = $_POST['status'];

    // Update the book details in the database
    $stmt = $pdo->prepare("UPDATE Books SET Title = :title, Author = :author, Genre = :genre, Status = :status WHERE BookID = :id");
    $stmt->execute([
        'title' => $title,
        'author' => $author,
        'genre' => $genre,
        'status' => $status,
        'id' => $book_id
    ]);

    // Redirect to the books management page after successful update
    header("Location: books.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Book</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($book['Title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" name="author" value="<?php echo htmlspecialchars($book['Author']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" class="form-control" name="genre" value="<?php echo htmlspecialchars($book['Genre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="Available" <?php echo ($book['Status'] === 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Borrowed" <?php echo ($book['Status'] === 'Borrowed') ? 'selected' : ''; ?>>Borrowed</option>
                    <option value="Reserved" <?php echo ($book['Status'] === 'Reserved') ? 'selected' : ''; ?>>Reserved</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
        <a href="books.php" class="btn btn-secondary mt-3">Back to Books</a>
    </div>
</body>
</html>
