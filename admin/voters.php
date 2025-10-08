<?php
require_once('./lib/header.php');
// Handle promote to candidate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote_user_id'])) {
    $promote_id = intval($_POST['promote_user_id']);
    $conn->query("UPDATE users SET role = 'CANDIDATE' WHERE id = $promote_id");
    echo '<div class="alert alert-success">User promoted to candidate.</div>';
}
// Handle remove as candidate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_candidate_id'])) {
    $remove_id = intval($_POST['remove_candidate_id']);
    // Set role to USER
    $conn->query("UPDATE users SET role = 'USER' WHERE id = $remove_id");
    // Delete all applications by this user
    $conn->query("DELETE FROM applicants WHERE applicant = $remove_id");
    echo '<div class="alert alert-warning">Candidate removed and all their applications deleted.</div>';
}
// Fetch all users and candidates
$users = $conn->query("SELECT id, username, role FROM users WHERE role IN ('USER', 'CANDIDATE')");
?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">Users & Candidates</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($u = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['id']); ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['role']); ?></td>
                            <td>
                                                                <?php if ($u['role'] === 'USER'): ?>
                                                                        <form method="post" style="display:inline;">
                                                                                <input type="hidden" name="promote_user_id" value="<?php echo $u['id']; ?>">
                                                                                <button type="submit" class="btn btn-sm btn-primary">Promote to Candidate</button>
                                                                        </form>
                                                                <?php else: ?>
                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#removeCandidateModal<?php echo $u['id']; ?>">Remove as Candidate</button>
                                                                        <!-- Remove Candidate Modal -->
                                                                        <div class="modal fade" id="removeCandidateModal<?php echo $u['id']; ?>" tabindex="-1" aria-labelledby="removeCandidateModalLabel<?php echo $u['id']; ?>" aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="removeCandidateModalLabel<?php echo $u['id']; ?>">Confirm Remove Candidate</h5>
                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <p>Are you sure you want to remove <strong><?php echo htmlspecialchars($u['username']); ?></strong> as a candidate? This will delete all their applications and make them ineligible for any posts.</p>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                                        <form method="post" style="display:inline;">
                                                                                            <input type="hidden" name="remove_candidate_id" value="<?php echo $u['id']; ?>">
                                                                                            <button type="submit" class="btn btn-danger">Yes, Remove</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('votertab').classList.add('active');
</script>
<?php
require_once('./lib/footer.php');
?>