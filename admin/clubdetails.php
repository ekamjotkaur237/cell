<?php
require_once('./lib/header.php');

// Check if club ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>No club specified.</div>";
    require_once('./lib/footer.php');
    exit();
}

$club_id = $_GET['id'];

// Handle form submission for deleting the club
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_club'])) {
    $conn->begin_transaction();
    try {
        // First, get all vacancy ids for this club
        $vacancy_ids = [];
        $sql_get_vacancies = "SELECT id FROM vacancies WHERE cells = ?";
        $stmt_get_vacancies = $conn->prepare($sql_get_vacancies);
        $stmt_get_vacancies->bind_param("i", $club_id);
        $stmt_get_vacancies->execute();
        $result_vacancies = $stmt_get_vacancies->get_result();
        while ($row = $result_vacancies->fetch_assoc()) {
            $vacancy_ids[] = $row['id'];
        }
        $stmt_get_vacancies->close();
        // Delete all applicants for these vacancies
        if (!empty($vacancy_ids)) {
            $in = implode(',', array_fill(0, count($vacancy_ids), '?'));
            $types = str_repeat('i', count($vacancy_ids));
            $sql_delete_applicants = "DELETE FROM applicants WHERE vacancy IN ($in)";
            $stmt_delete_applicants = $conn->prepare($sql_delete_applicants);
            $stmt_delete_applicants->bind_param($types, ...$vacancy_ids);
            $stmt_delete_applicants->execute();
            $stmt_delete_applicants->close();
        }
        // Delete associated vacancies
        $sql_delete_vacancies = "DELETE FROM vacancies WHERE cells = ?";
        $stmt_delete_vacancies = $conn->prepare($sql_delete_vacancies);
        $stmt_delete_vacancies->bind_param("i", $club_id);
        $stmt_delete_vacancies->execute();
        $stmt_delete_vacancies->close();
        // Then, delete the club
        $sql_delete_club = "DELETE FROM cells WHERE id = ?";
        $stmt_delete_club = $conn->prepare($sql_delete_club);
        $stmt_delete_club->bind_param("i", $club_id);
        $stmt_delete_club->execute();
        $stmt_delete_club->close();
        $conn->commit();
        header("Location: club.php");
        exit();
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        echo "Error deleting club: " . $exception->getMessage();
    }
}

// Handle form submission for adding a new opening
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_opening'])) {
    $role = $_POST['role'];
    $description = $_POST['description'];
    $openings = $_POST['openings'];

    $sql = "INSERT INTO vacancies (cells, role, description, openings) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $club_id, $role, $description, $openings);
    
    if ($stmt->execute()) {
        // Redirect to the same page to see the new opening
        header("Location: clubdetails.php?id=" . $club_id);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submission for editing an opening
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_opening'])) {
    $role = $_POST['role'];
    $description = $_POST['description'];
    $openings = $_POST['openings'];
    $original_role = $_POST['original_role'];

    $sql = "UPDATE vacancies SET role = ?, description = ?, openings = ? WHERE cells = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiis", $role, $description, $openings, $club_id, $original_role);
    
    if ($stmt->execute()) {
        // Redirect to the same page to see the updated opening
        header("Location: clubdetails.php?id=" . $club_id);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submission for deleting a specific vacancy
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_vacancy_id'])) {
    $vacancy_id = intval($_POST['delete_vacancy_id']);
    $conn->begin_transaction();
    try {
        // Delete all applicants for this vacancy
        $stmt = $conn->prepare("DELETE FROM applicants WHERE vacancy = ?");
        $stmt->bind_param("i", $vacancy_id);
        $stmt->execute();
        $stmt->close();
        // Delete the vacancy
        $stmt = $conn->prepare("DELETE FROM vacancies WHERE id = ?");
        $stmt->bind_param("i", $vacancy_id);
        $stmt->execute();
        $stmt->close();
        $conn->commit();
        header("Location: clubdetails.php?id=" . $club_id);
        exit();
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        echo "Error deleting vacancy: " . $exception->getMessage();
    }
}

// Handle club status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_club_status'])) {
    $new_status = $_POST['club_status'];
    $stmt = $conn->prepare("UPDATE cells SET stat = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $club_id);
    $stmt->execute();
    $stmt->close();
    header("Location: clubdetails.php?id=" . $club_id);
    exit();
}

// Fetch club details
$sql_club = "SELECT * FROM cells WHERE id = ?";
$stmt_club = $conn->prepare($sql_club);
$stmt_club->bind_param("i", $club_id);
$stmt_club->execute();
$result_club = $stmt_club->get_result();
$club = $result_club->fetch_assoc();

if (!$club) {
    echo "<div class='alert alert-danger'>Club not found.</div>";
    require_once('./lib/footer.php');
    exit();
}

// Fetch vacancies for the club
$sql_vacancies = "SELECT * FROM vacancies WHERE cells = ?";
$stmt_vacancies = $conn->prepare($sql_vacancies);
$stmt_vacancies->bind_param("i", $club_id);
$stmt_vacancies->execute();
$result_vacancies = $stmt_vacancies->get_result();

// If voting started, prepare vote analytics data
$vote_chart_data = [];
$vote_list_data = [];
$has_votes = false;
$winner_results = [];
if ($club['stat'] === 'VOTING STARTED') {
  $vacancy_res = $conn->query("SELECT id, role FROM vacancies WHERE cells = $club_id");
  while ($vac = $vacancy_res->fetch_assoc()) {
    $role = $vac['role'];
    $vacancy_id = $vac['id'];
    $applicants = $conn->query("SELECT a.id, u.username FROM applicants a JOIN users u ON a.applicant = u.id WHERE a.vacancy = $vacancy_id AND a.stat = 'ACCEPT'");
    $labels = [];
    $votes = [];
    $vote_details = [];
    $max_votes = 0;
    $role_winners = [];
    $total_votes = 0;
    while ($app = $applicants->fetch_assoc()) {
      $labels[] = $app['username'];
      $vote_count = 0;
      $voters = [];
      $votes_res = $conn->query("SELECT u.username FROM votes v JOIN users u ON v.voter = u.id WHERE v.applicant = " . $app['id']);
      while ($v = $votes_res->fetch_assoc()) {
        $voters[] = $v['username'];
      }
      $vote_count = count($voters);
      $votes[] = $vote_count;
      $vote_details[] = [
        'applicant' => $app['username'],
        'votes' => $vote_count,
        'voters' => $voters
      ];
      if ($vote_count > $max_votes) {
        $max_votes = $vote_count;
        $role_winners = [$app['username']];
      } elseif ($vote_count === $max_votes && $vote_count > 0) {
        $role_winners[] = $app['username'];
      }
      $total_votes += $vote_count;
    }
    if ($total_votes > 0) $has_votes = true;
    $vote_chart_data[] = [
      'role' => $role,
      'labels' => $labels,
      'votes' => $votes
    ];
    $vote_list_data[] = [
      'role' => $role,
      'details' => $vote_details
    ];
    $winner_results[] = [
      'role' => $role,
      'winners' => $role_winners,
      'votes' => $max_votes,
      'is_tie' => count($role_winners) > 1,
      'has_votes' => $max_votes > 0
    ];
  }
}

// Handle declare result POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['declare_result'])) {
    // Set club status to NO ACTION
    $stmt = $conn->prepare("UPDATE cells SET stat = 'NO ACTION' WHERE id = ?");
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $stmt->close();
    // Refresh to show results and new status
    header("Location: clubdetails.php?id=" . $club_id . "&result=1");
    exit();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($club['title']); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteClubModal">
                Delete Club
            </button>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Club Details</div>
    <div class="card-body">
        <p><strong>Description:</strong> <?php echo htmlspecialchars($club['description']); ?></p>
    </div>
</div>

<div class="mb-3">
    <form method="post" class="d-flex align-items-center" action="clubdetails.php?id=<?php echo $club_id; ?>">
        <label for="club_status" class="form-label me-2 mb-0"><strong>Club Status:</strong></label>
        <select name="club_status" id="club_status" class="form-select form-select-sm w-auto me-2">
            <option value="NO ACTION"<?php echo ($club['stat'] == 'NO ACTION' ? ' selected' : ''); ?>>NO ACTION</option>
            <option value="APPLICANT INVITED"<?php echo ($club['stat'] == 'APPLICANT INVITED' ? ' selected' : ''); ?>>APPLICANT INVITED</option>
            <option value="VOTING STARTED"<?php echo ($club['stat'] == 'VOTING STARTED' ? ' selected' : ''); ?>>VOTING STARTED</option>
        </select>
        <button type="submit" name="update_club_status" class="btn btn-primary btn-sm">Update</button>
    </form>
</div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h3 class="h3">Openings</h3>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addOpeningModal">
                Add New Opening
            </button>
        </div>
    </div>
</div>

<!-- Delete Club Modal -->
<div class="modal fade" id="deleteClubModal" tabindex="-1" aria-labelledby="deleteClubModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteClubModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this club? All associated openings and details will be permanently removed.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="post" action="clubdetails.php?id=<?php echo $club_id; ?>">
          <button type="submit" name="delete_club" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Add Opening Modal -->
<div class="modal fade" id="addOpeningModal" tabindex="-1" aria-labelledby="addOpeningModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addOpeningModalLabel">Add New Opening</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="clubdetails.php?id=<?php echo $club_id; ?>">
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <input type="text" class="form-control" id="role" name="role" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="openings" class="form-label">Number of Openings</label>
            <input type="number" class="form-control" id="openings" name="openings" value="1" min="1" required>
          </div>
          <button type="submit" name="add_opening" class="btn btn-primary">Save Opening</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <?php 
      $show_result = false;
      // If voting ended (NO ACTION) and there were votes, show result to everyone
      if ($club['stat'] === 'NO ACTION' && !empty($winner_results)) {
        $show_result = true;
      }
      // If voting is ongoing and result just declared, show result to admin
      if ($club['stat'] === 'VOTING STARTED' && isset($_GET['result'])) {
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
    <?php if ($club['stat'] === 'VOTING STARTED' && !empty($vote_chart_data)): ?>
      <?php if ($has_votes): ?>
        <div class="mb-3">
          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#declareResultModal">Declare Result</button>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No votes have been cast yet. Result cannot be declared.</div>
      <?php endif; ?>
<!-- Declare Result Modal -->
<div class="modal fade" id="declareResultModal" tabindex="-1" aria-labelledby="declareResultModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="declareResultModalLabel">Confirm Declare Result</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to declare the election result? This will end voting and show the winner(s).</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="post" action="clubdetails.php?id=<?php echo $club_id; ?>">
          <button type="submit" name="declare_result" class="btn btn-warning">Yes, Declare Result</button>
        </form>
      </div>
    </div>
  </div>
</div>
      <div class="mb-4">
        <h4>Live Vote Analytics</h4>
        <?php foreach ($vote_chart_data as $i => $cd): ?>
          <div class="mb-4">
            <strong><?php echo htmlspecialchars($cd['role']); ?></strong>
            <canvas id="vote_chart_<?php echo $i; ?>" height="100"></canvas>
          </div>
        <?php endforeach; ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        <?php foreach ($vote_chart_data as $i => $cd): ?>
        new Chart(document.getElementById('vote_chart_<?php echo $i; ?>').getContext('2d'), {
          type: 'bar',
          data: {
            labels: <?php echo json_encode($cd['labels']); ?>,
            datasets: [{
              label: 'Votes',
              data: <?php echo json_encode($cd['votes']); ?>,
              backgroundColor: '#007bff',
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
      </div>
      <div class="mb-4">
        <h4>Votes List</h4>
        <?php foreach ($vote_list_data as $group): ?>
          <div class="mb-3 p-3 border rounded bg-light">
            <h5 class="mb-2">Role: <?php echo htmlspecialchars($group['role']); ?></h5>
            <ul class="list-group">
              <?php foreach ($group['details'] as $app): ?>
                <li class="list-group-item">
                  <strong><?php echo htmlspecialchars($app['applicant']); ?></strong> - Votes: <?php echo $app['votes']; ?>
                  <?php if (!empty($app['voters'])): ?>
                    <br><span class="text-muted small">Voters: <?php echo htmlspecialchars(implode(', ', $app['voters'])); ?></span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="table table-hover table-sm">
        <thead>
          <tr>
            <th>Role</th>
            <th>Description</th>
            <th>Openings</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result_vacancies->num_rows > 0) {
            while($row = $result_vacancies->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row["role"]) . "</td>";
              echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
              echo "<td>" . htmlspecialchars($row["openings"]) . "</td>";
              echo '<td>
                  <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editOpeningModal" data-role="' . htmlspecialchars($row["role"]) . '" data-description="' . htmlspecialchars($row["description"]) . '" data-openings="' . htmlspecialchars($row["openings"]) . '">Edit</button>
                  <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteVacancyModal' . $row["id"] . '">Delete</button>
                  </td>';
              echo "</tr>";

              // Delete Vacancy Modal for this vacancy
              echo '<div class="modal fade" id="deleteVacancyModal' . $row["id"] . '" tabindex="-1" aria-labelledby="deleteVacancyModalLabel' . $row["id"] . '" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="deleteVacancyModalLabel' . $row["id"] . '">Confirm Deletion</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p>Are you sure you want to delete this vacancy? All associated applicants will be permanently removed.</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <form method="post" action="clubdetails.php?id=' . $club_id . '">
                      <input type="hidden" name="delete_vacancy_id" value="' . $row["id"] . '">
                      <button type="submit" class="btn btn-danger">Delete</button>
                      </form>
                    </div>
                    </div>
                  </div>
                  </div>';
            }
          } else {
            echo "<tr><td colspan='4'>No openings found for this club.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Edit Opening Modal -->
<div class="modal fade" id="editOpeningModal" tabindex="-1" aria-labelledby="editOpeningModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editOpeningModalLabel">Edit Opening</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="clubdetails.php?id=<?php echo $club_id; ?>">
          <input type="hidden" id="original_role" name="original_role">
          <div class="mb-3">
            <label for="edit_role" class="form-label">Role</label>
            <input type="text" class="form-control" id="edit_role" name="role" required>
          </div>
          <div class="mb-3">
            <label for="edit_description" class="form-label">Description</label>
            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="edit_openings" class="form-label">Number of Openings</label>
            <input type="number" class="form-control" id="edit_openings" name="openings" min="1" required>
          </div>
          <button type="submit" name="edit_opening" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.getElementById('clubtab').classList.add('active');
    document.getElementById('dashboardtab').classList.remove('active');

    var editOpeningModal = document.getElementById('editOpeningModal');
    editOpeningModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var role = button.getAttribute('data-role');
        var description = button.getAttribute('data-description');
        var openings = button.getAttribute('data-openings');
        
        var modalTitle = editOpeningModal.querySelector('.modal-title');
        var originalRoleInput = editOpeningModal.querySelector('#original_role');
        var roleInput = editOpeningModal.querySelector('#edit_role');
        var descriptionInput = editOpeningModal.querySelector('#edit_description');
        var openingsInput = editOpeningModal.querySelector('#edit_openings');

        modalTitle.textContent = 'Edit Opening: ' + role;
        originalRoleInput.value = role;
        roleInput.value = role;
        descriptionInput.value = description;
        openingsInput.value = openings;
    });
</script>

<?php
require_once('./lib/footer.php');
?>
