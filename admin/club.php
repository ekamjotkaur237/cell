<?php
require_once('./lib/header.php');

// Handle form submission for adding a new club
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_club'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $sql = "INSERT INTO cells (title, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $title, $description);
    
    if ($stmt->execute()) {
        // Redirect to the same page to see the new club
        header("Location: club.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Database query to get club data
$sql = "SELECT c.id, c.title, c.description, SUM(v.openings) as total_openings
        FROM cells c
        LEFT JOIN vacancies v ON c.id = v.cells
        GROUP BY c.id, c.title, c.description";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Clubs/Cells/Centers</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addClubModal">
                Add New Club
            </button>
        </div>
    </div>
</div>

<!-- Add Club Modal -->
<div class="modal fade" id="addClubModal" tabindex="-1" aria-labelledby="addClubModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addClubModalLabel">Add New Club</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="club.php">
          <div class="mb-3">
            <label for="title" class="form-label">Club/Cell Name</label>
            <input type="text" class="form-control" id="title" name="title" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
          <button type="submit" name="add_club" class="btn btn-primary">Save Club</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Club ID</th>
                        <th>Club/Cell Name</th>
                        <th>Description</th>
                        <th>Openings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["title"] . "</td>";
                            echo "<td>" . $row["description"] . "</td>";
                            echo "<td>" . ($row["total_openings"] ? $row["total_openings"] : 0) . "</td>";
                            echo '<td><a href="clubdetails.php?id=' . $row["id"] . '" class="btn btn-sm btn-outline-primary">View Details</a></td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No clubs found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('clubtab').classList.add('active');
    document.getElementById('dashboardtab').classList.remove('active');
</script>

<?php
require_once('./lib/footer.php');
?>