<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- FontAwesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        /* Custom styles for the dashboard */
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
            <h1 class="content-header">Welcome, <?php echo $_SESSION['name']; ?> (Admin)</h1>
            <p class="lead">Manage the library system effectively from here.</p>
            <div class="row">
                <!-- Add clickable cards for each section -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="manage_users.php">
                                <i class="fas fa-users fa-3x"></i>
                                <h4>Manage Users</h4>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="manage_books.php">
                                <i class="fas fa-book fa-3x"></i>
                                <h4>Manage Books</h4>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="reports.php">
                                <i class="fas fa-chart-line fa-3x"></i>
                                <h4>View Reports</h4>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="addBook.php">
                                <i class="fas fa-plus fa-3x"></i>
                                <h4>Add Book</h4>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- AdminLTE Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
