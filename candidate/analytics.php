<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('./lib/header.php');
if (!isset($_SESSION['user_id'])) {
    // Try to fetch user_id from username
    if (isset($_SESSION['username'])) {
        $username = $conn->real_escape_string($_SESSION['username']);
        $result = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['id'];
        } else {
            echo '<div class="alert alert-danger">User not found in database.</div>';
            require_once('./lib/footer.php');
            exit();
        }
    } else {
        echo '<div class="alert alert-danger">User ID and username not set in session.</div>';
        require_once('./lib/footer.php');
        exit();
    }
}
$user_id = $_SESSION['user_id'];
// Fetch all applications for this candidate
$apps = $conn->query("SELECT a.id as app_id, c.title as club_title, v.role as vacancy_role, a.stat, v.id as vacancy_id, c.id as club_id FROM applicants a JOIN vacancies v ON a.vacancy = v.id JOIN cells c ON v.cells = c.id WHERE a.applicant = $user_id");
echo '<!-- Debug: user_id=' . htmlspecialchars($user_id) . ', num_apps=' . ($apps ? $apps->num_rows : 'ERROR') . ' -->';
?>
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header bg-info text-white">Your Applications & Analytics</div>
        <div class="card-body">
            <?php if ($apps && $apps->num_rows > 0): ?>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Club</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $chartData = [];
                            $apps->data_seek(0); // Reset pointer
                            while($app = $apps->fetch_assoc()): 
                                // Get all candidates for this role in this club
                                $candidates = $conn->query("SELECT a.id, u.username FROM applicants a JOIN users u ON a.applicant = u.id WHERE a.vacancy = " . $app['vacancy_id']);
                                $votes_for_role = [];
                                $labels = [];
                                $my_votes = 0;
                                while($cand = $candidates->fetch_assoc()) {
                                    $vote_count = $conn->query("SELECT COUNT(*) as total FROM votes WHERE applicant = " . $cand['id']);
                                    $count = $vote_count ? $vote_count->fetch_assoc()['total'] : 0;
                                    $votes_for_role[] = $count;
                                    $labels[] = $cand['username'];
                                    if ($cand['id'] == $app['app_id']) $my_votes = $count;
                                }
                                $chartData[] = [
                                    'club' => $app['club_title'],
                                    'role' => $app['vacancy_role'],
                                    'labels' => $labels,
                                    'votes' => $votes_for_role,
                                    'my_index' => array_search($_SESSION['username'], $labels),
                                ];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['club_title']); ?></td>
                                <td><?php echo htmlspecialchars($app['vacancy_role']); ?></td>
                                <td><?php echo htmlspecialchars($app['stat']); ?></td>
                                <td><?php echo $my_votes; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <h5>Vote Comparison (Bar Chart)</h5>
                <?php foreach ($chartData as $i => $cd): ?>
                    <div class="mb-4">
                        <strong><?php echo htmlspecialchars($cd['club'] . ' - ' . $cd['role']); ?></strong>
                        <canvas id="chart_<?php echo $i; ?>" height="100"></canvas>
                    </div>
                <?php endforeach; ?>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                <?php foreach ($chartData as $i => $cd): ?>
                new Chart(document.getElementById('chart_<?php echo $i; ?>').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($cd['labels']); ?>,
                        datasets: [{
                            label: 'Votes',
                            data: <?php echo json_encode($cd['votes']); ?>,
                            backgroundColor: <?php
                                $colors = array_map(function($idx) use ($cd) {
                                    return ($idx == $cd['my_index']) ? "'#007bff'" : "'#6c757d'";
                                }, array_keys($cd['labels']));
                                echo '[' . implode(',', $colors) . ']';
                            ?>,
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        if (Number.isInteger(value)) {
                                            return value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
                <?php endforeach; ?>
                </script>
            <?php else: ?>
                <div class="alert alert-warning">You have not applied for any roles yet. (user_id=<?php echo htmlspecialchars($user_id); ?>)</div>
            <?php endif; ?>
        </div>
    </div>
    <a href="index.php" class="btn btn-outline-primary">Back to Dashboard</a>
</div>
<?php require_once('./lib/footer.php'); ?>
