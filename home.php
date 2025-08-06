<?php
session_start();
require_once 'db.php';
$timeout_duration = 60; 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1"); 
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $due = $_POST['due_date'];

    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $desc, $due);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['update_task'])) {
    $task_id = $_POST['task_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $due = $_POST['due_date'];

    $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $title, $desc, $due, $task_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <link rel="stylesheet" href="style_home.css">
    <link rel="icon" type="image/png" href="logo1.png">
</head>

<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <img src="logo1.png" alt="Task Manager Logo" class="logo">
            <h2>Welcome, <?php echo $_SESSION['username']; ?> 👋</h2>
        </div>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">Add New Task</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="title" class="form-control" placeholder="Task Title" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="description" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-3">
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="add_task" class="btn btn-success w-100">Add</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                    <td><?= $row['due_date'] ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task?')">Delete</a>
                            <button class="btn btn-sm btn-secondary" onclick="toggleEdit(<?= $row['id'] ?>)">Edit</button>
                        </div>
                        <div id="edit-form-<?= $row['id'] ?>" class="mt-2 d-none">
                            <form method="POST" class="border p-2 rounded">
                                <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                                <input type="text" name="title" class="form-control mb-2" value="<?= htmlspecialchars($row['title']) ?>" required>
                                <textarea name="description" class="form-control mb-2"><?= htmlspecialchars($row['description']) ?></textarea>
                                <input type="date" name="due_date" class="form-control mb-2" value="<?= $row['due_date'] ?>">
                                <button type="submit" name="update_task" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info">You have no tasks yet. Add your first task above!</div>
    <?php endif; ?>
</div>

<script>
function toggleEdit(id) {
    var form = document.getElementById("edit-form-" + id);
    form.classList.toggle("d-none");
}
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
