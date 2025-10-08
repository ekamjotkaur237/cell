<?php
require_once('./lib/config.php');
session_start();
$_SESSION['username'] = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username = '$username' AND pass = '$password'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            // echo "User ID: " . $row['id'] . " - Name: " . $row['username'] . "<br>";
            if ($row["username"] == $username) {
                $_SESSION["username"] = $row["username"];
                $role = $row["role"];
                if($role == "ADMIN"){
                    header("Location: admin/index.php");
                }
                else if($role == "CANDIDATE"){
                    header("Location: candidate/index.php");
                }
                else{
                    header("Location: users/index.php");
                }
            } else {
                echo "Invalid username or password.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="card login-card p-4">
        <div class="card-body">
            <h3 class="text-center mb-4">Login</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Username</label>
                    <input name="username" type="text" class="form-control" id="text" placeholder="Enter username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Enter password" required>
                </div>
                <div class="mb-3 form-check">
                    <!-- <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label> -->
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <!-- <p class="text-center mt-3 mb-0">Don't have an account? <a href="#">Register</a></p> -->
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>