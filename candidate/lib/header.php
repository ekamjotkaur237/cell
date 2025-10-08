<?php
    require_once("../lib/config.php");
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["username"] == '') {
        header("location: index.php");
    }
    if (!isset($_GET['club_id']) || !is_numeric($_GET['club_id'])) {
        header("Location: index.php?error=invalid_club");
        exit();
    }
    $club_id = intval($_GET['club_id']);
    
    if (!isset($_SESSION['user_id'])) {
        // Try to fetch user_id from username
        if (isset($_SESSION['username'])) {
            $username = $conn->real_escape_string($_SESSION['username']);
            $result = $conn->query("SELECT id FROM users WHERE username = '$username'");
            if ($row = $result->fetch_assoc()) {
                $_SESSION['user_id'] = $row['id'];
            } else {
                header("Location: index.php?error=user_not_found");
                exit();
            }
        } else {
            header("Location: index.php?error=session_invalid");
            exit();
        }
    }
$user_id = $_SESSION['user_id'];

// Handle all POST requests before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_vacancy_id'])) {
    $vacancy_id = intval($_POST['apply_vacancy_id']);
    $check = $conn->query("SELECT * FROM applicants WHERE applicant = $user_id AND vacancy = $vacancy_id");
    if ($check && $check->num_rows == 0) {
        $conn->query("INSERT INTO applicants (vacancy, applicant, stat) VALUES ($vacancy_id, $user_id, 'PENDING')");
        header("Location: clubdetails.php?club_id=$club_id&applied=1");
        exit();
    } else {
        header("Location: clubdetails.php?club_id=$club_id&already=1");
        exit();
    }
}

// Fetch club and prepare winner results if needed
$club = $conn->query("SELECT * FROM cells WHERE id = $club_id")->fetch_assoc();
if (!$club) {
    header("Location: index.php?error=club_not_found");
    exit();
}

// Prepare winner results if voting ended
$winner_results = [];
if ($club['stat'] === 'NO ACTION') {
    $vacancy_res = $conn->query("SELECT id, role FROM vacancies WHERE cells = $club_id");
    while ($vac = $vacancy_res->fetch_assoc()) {
        $role = $vac['role'];
        $vacancy_id = $vac['id'];
        $applicants = $conn->query("SELECT a.id, u.username FROM applicants a JOIN users u ON a.applicant = u.id WHERE a.vacancy = $vacancy_id AND a.stat = 'ACCEPT'");
        $max_votes = 0;
        $role_winners = [];
        $total_votes = 0;
        while ($app = $applicants->fetch_assoc()) {
            $votes_res = $conn->query("SELECT COUNT(*) as total FROM votes WHERE applicant = " . $app['id']);
            $vote_count = $votes_res ? $votes_res->fetch_assoc()['total'] : 0;
            if ($vote_count > $max_votes) {
                $max_votes = $vote_count;
                $role_winners = [$app['username']];
            } elseif ($vote_count === $max_votes && $vote_count > 0) {
                $role_winners[] = $app['username'];
            }
            $total_votes += $vote_count;
        }
        $winner_results[] = [
            'role' => $role,
            'winners' => $role_winners,
            'votes' => $max_votes,
            'is_tie' => count($role_winners) > 1,
            'has_votes' => $max_votes > 0
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Users Dashboard</title>
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


