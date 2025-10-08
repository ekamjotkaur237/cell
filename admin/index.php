<?php
require_once('./lib/header.php');
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Clubs</div>
            <div class="card-body">
                <h5 class="card-title">Create a new Club</h5>
                <p class="card-text">Add, edit, or remove clubs from the election.</p>
                <a href="club.php" class="btn btn-light">Go to Clubs</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card  bg-warning mb-3">
            <div class="card-header">Candidates</div>
            <div class="card-body">
                <h5 class="card-title">Manage Candidates</h5>
                <p class="card-text">Add, edit, or remove candidates from the election.</p>
                <a href="candidates.php" class="btn btn-light">Go to Candidates</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Voters</div>
            <div class="card-body">
                <h5 class="card-title">Manage Voters</h5>
                <p class="card-text">View and manage the list of registered voters.</p>
                <a href="voters.php" class="btn btn-light">Go to Voters</a>
            </div>
        </div>
    </div>

<script>
    document.getElementById('dashboardtab').classList.add('active');
</script>

<?php
require_once('./lib/footer.php');
?>

