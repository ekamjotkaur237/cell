
<?php
require_once('./lib/header.php');
// Fetch all clubs
$clubs = $conn->query("SELECT * FROM cells");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
</div>

<div class="row">
    <?php while($club = $clubs->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($club['title']); ?></h5>
                    <p class="card-text mb-2"><strong>Description:</strong> <?php echo htmlspecialchars($club['description']); ?></p>
                    <p class="card-text">Status: <span class="badge bg-secondary"><?php echo htmlspecialchars($club['stat']); ?></span></p>
                    <a href="clubdetails.php?club_id=<?php echo $club['id']; ?>" class="btn btn-primary mt-auto">View Details</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
    document.getElementById('dashboardtab').classList.add('active');
</script>

<?php
require_once('./lib/footer.php');
?>

