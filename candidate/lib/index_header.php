<?php
    require_once("../lib/config.php");
    session_start();
    
    if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
        header("Location: ../index.php");
        exit();
    }
    
    // Check if user has correct role
    $username = $_SESSION["username"];
    $role_check = $conn->query("SELECT id, role FROM users WHERE username = '" . mysqli_real_escape_string($conn, $username) . "'");
    $user_role = $role_check->fetch_assoc();
    
    if (!$user_role || strtoupper($user_role['role']) !== 'CANDIDATE') {
        header("Location: ../index.php?error=unauthorized");
        exit();
    }

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = $user_role['id'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Candidate Dashboard</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky">
                <div class="sidebar-header">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none logo">
                        <i class="bi bi-box-seam me-2"></i>
                        <strong>Voting</strong>
                    </a>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" id="dashboardtab" href="index.php">
                            <i class="bi bi-house-door"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light d-md-none">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </nav>