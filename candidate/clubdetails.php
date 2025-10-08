
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('./lib/header.php');

// Handle vote submission before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_app_id'])) {
    $vote_app_id = intval($_POST['vote_app_id']);
    $vote_vacancy_id = intval($_POST['vote_vacancy_id']);
    // Prevent duplicate vote for this role in this club
    $vote_check = $conn->query("SELECT * FROM votes WHERE voter = $user_id AND applicant IN (SELECT id FROM applicants WHERE vacancy = $vote_vacancy_id)");
    if ($vote_check->num_rows == 0) {
        $conn->query("INSERT INTO votes (voter, applicant) VALUES ($user_id, $vote_app_id)");
        header("Location: clubdetails.php?club_id=$club_id&voted=1");
        exit();
    } else {
        header("Location: clubdetails.php?club_id=$club_id&alreadyvoted=1");
        exit();
    }
}

// Fetch roles and applicants data before any output
$roles_res = null;
$applicants_by_role = [];
if ($club['stat'] == 'VOTING STARTED') {
    // Fetch all roles for this club
    $roles_res = $conn->query("SELECT DISTINCT role, id FROM vacancies WHERE cells = $club_id");
    $roles = [];
    while ($row = $roles_res->fetch_assoc()) {
        $roles[] = $row;
    }
    // Fetch all accepted applicants for this club, grouped by vacancy (role)
    $applicants_res = $conn->query("SELECT a.id as app_id, u.username, v.role, v.id as vacancy_id FROM applicants a JOIN users u ON a.applicant = u.id JOIN vacancies v ON a.vacancy = v.id WHERE v.cells = $club_id AND a.stat = 'ACCEPT'");
    while ($row = $applicants_res->fetch_assoc()) {
        $applicants_by_role[$row['vacancy_id']]['role'] = $row['role'];
        $applicants_by_role[$row['vacancy_id']]['candidates'][] = $row;
    }
}
?>
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title"><?php echo htmlspecialchars($club['title']); ?></h2>
            <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($club['description']); ?></p>
            <p class="card-text"><strong>Status:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($club['stat']); ?></span></p>
        </div>
    </div>

    <?php if (isset($_GET['applied'])): ?>
        <div class="alert alert-success">Application submitted!</div>
    <?php elseif (isset($_GET['already'])): ?>
        <div class="alert alert-warning">You have already applied for this role.</div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Openings</div>
        <div class="card-body">
            <?php
            $vacancies = $conn->query("SELECT * FROM vacancies WHERE cells = $club_id");
            // Fetch user's applications for this club
            $user_apps = [];
            $apps_res = $conn->query("SELECT vacancy FROM applicants WHERE applicant = $user_id");
            while($row = $apps_res->fetch_assoc()) {
                $user_apps[] = $row['vacancy'];
            }
            if ($vacancies->num_rows > 0):
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Description</th>
                            <th>Openings</th>
                            <?php if ($club['stat'] == 'APPLICANT INVITED'): ?><th>Action</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($vacancies as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['role']); ?></td>
                            <td><?php echo htmlspecialchars($v['description']); ?></td>
                            <td><?php echo htmlspecialchars($v['openings']); ?></td>
                            <?php if ($club['stat'] == 'APPLICANT INVITED'): ?>
                            <td>
                                <?php if (in_array($v['id'], $user_apps)): ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Applied</button>
                                <?php else: ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="apply_vacancy_id" value="<?php echo $v['id']; ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>No openings available for this club.</p>
            <?php endif; ?>
        </div>
    </div>

        <?php 
            $show_result = false;
            if ($club['stat'] === 'NO ACTION' && !empty($winner_results)) {
                $show_result = true;
            }
        ?>
        <?php if ($show_result): ?>
            <div class="alert alert-success"><strong>Election Result:</strong><br>
                <?php foreach ($winner_results as $res): ?>
                    <div class="mb-2">
                        <strong>Role:</strong> <?php echo htmlspecialchars($res['role']); ?><br>
                        <?php if (!$res['has_votes']): ?>
                            No votes were cast for this role.<br>
                        <?php else: ?>
                            Winner<?php echo $res['is_tie'] ? 's' : ''; ?>:
                            <strong><?php echo htmlspecialchars(implode(', ', $res['winners'])); ?></strong><br>
                            Votes: <?php echo $res['votes']; ?><br>
                            <?php if ($res['is_tie']): ?>
                                <span class="text-warning">It was a tie!</span><br>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="mb-4">
                <?php if ($club['stat'] == 'VOTING STARTED'): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Vote for Candidates</div>
                <div class="card-body">
                    <?php
                    // Data has been fetched before any output
                    ?>
                    <?php if (isset($_GET['voted'])): ?>
                        <div class="alert alert-success">Your vote has been submitted!</div>
                    <?php elseif (isset($_GET['alreadyvoted'])): ?>
                        <div class="alert alert-warning">You have already voted for this role.</div>
                    <?php endif; ?>
                    <?php if (!empty($applicants_by_role)): ?>
                        <?php foreach ($applicants_by_role as $vacancy_id => $group): ?>
                            <div class="mb-3 p-3 border rounded bg-light">
                                <h5 class="mb-2">Role: <?php echo htmlspecialchars($group['role']); ?></h5>
                                <div class="row">
                                    <?php foreach ($group['candidates'] as $candidate): ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title">Candidate: <?php echo htmlspecialchars($candidate['username']); ?></h6>
                                                    <?php
                                                    // Check if user has already voted for this role
                                                    $has_voted = false;
                                                    $vote_check = $conn->query("SELECT * FROM votes WHERE voter = $user_id AND applicant IN (SELECT id FROM applicants WHERE vacancy = $vacancy_id)");
                                                    if ($vote_check->num_rows > 0) $has_voted = true;
                                                    ?>
                                                    <form method="post" style="display:inline;" id="voteForm_<?php echo $candidate['app_id']; ?>">
                                                        <input type="hidden" name="vote_app_id" value="<?php echo $candidate['app_id']; ?>">
                                                        <input type="hidden" name="vote_vacancy_id" value="<?php echo $vacancy_id; ?>">
                                                        <?php if ($has_voted): ?>
                                                            <button type="button" class="btn btn-success btn-sm" disabled>Voted</button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-success btn-sm" onclick="showVoteModal('<?php echo $candidate['app_id']; ?>', '<?php echo htmlspecialchars($candidate['username'], ENT_QUOTES); ?>')">Vote</button>
                                                        <?php endif; ?>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No accepted applicants available for voting.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-outline-primary ms-2">Back to Clubs</a>
    </div>
</div>

<!-- Vote Confirmation Modal -->
<div class="modal fade" id="voteConfirmModal" tabindex="-1" aria-labelledby="voteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voteConfirmModalLabel">Confirm Your Vote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to vote for <strong id="candidateName"></strong>?</p>
                <p class="text-warning"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmVoteBtn">Yes, Vote</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentVoteFormId = null;

function showVoteModal(appId, candidateName) {
    currentVoteFormId = 'voteForm_' + appId;
    document.getElementById('candidateName').textContent = candidateName;
    var modal = new bootstrap.Modal(document.getElementById('voteConfirmModal'));
    modal.show();
}

document.getElementById('confirmVoteBtn').addEventListener('click', function() {
    if (currentVoteFormId) {
        document.getElementById(currentVoteFormId).submit();
    }
});
</script>

<?php require_once('./lib/footer.php'); ?>
