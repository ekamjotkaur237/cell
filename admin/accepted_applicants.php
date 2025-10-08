<?php
require_once('./lib/header.php');
// Handle Reject (removes application, user remains candidate)
if (isset($_POST['reject_id'])) {
    $app_id = intval($_POST['reject_id']);
    $conn->query("DELETE FROM applicants WHERE id=$app_id");
    header("Location: accepted_applicants.php");
    exit();
}
// Fetch all ACCEPTED applications
$sql = "SELECT a.id as app_id, a.stat, u.username, u.role as user_role, v.role as vacancy_role, c.title as club_name
        FROM applicants a
        JOIN users u ON a.applicant = u.id
        JOIN vacancies v ON a.vacancy = v.id
        JOIN cells c ON v.cells = c.id
        WHERE a.stat = 'ACCEPT'
        ORDER BY a.id DESC";
$result = $conn->query($sql);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Accepted Applicants</h1>
    <a href="candidates.php" class="btn btn-outline-secondary btn-sm">View Pending Applicants</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Current Role</th>
                        <th>Club</th>
                        <th>Vacancy</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_role']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['club_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['vacancy_role']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stat']) . "</td>";
                        echo "<td>";
                        echo '<button class="btn btn-danger btn-sm" onclick="confirmReject(' . $row['app_id'] . ')">Reject</button>';
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="6">No accepted applications found.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<form id="rejectForm" method="post" style="display:none;">
    <input type="hidden" name="reject_id" id="reject_id">
</form>
<script>
function confirmReject(id) {
    if (confirm('Are you sure you want to reject this application? This will remove the application and the user will remain a candidate.')) {
        document.getElementById('reject_id').value = id;
        document.getElementById('rejectForm').submit();
    }
}
</script>
<?php require_once('./lib/footer.php'); ?>
