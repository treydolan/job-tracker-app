<?php
require_once __DIR__ . "/../config/db.php";

$statuses = ["Wishlist", "Applied", "Interviewing", "Offer", "Rejected"];
$errors = [];

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        j.job_id,
        j.company_id,
        j.title,
        j.status,
        j.applied_date,
        c.name AS company_name
    FROM jobs j
    JOIN companies c ON c.company_id = j.company_id
    WHERE j.job_id = ?
");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) {
    die("Job not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company_name = trim($_POST["company_name"] ?? "");
    $title = trim($_POST["title"] ?? "");
    $status = $_POST["status"] ?? "Wishlist";
    $applied_date = $_POST["applied_date"] ?: null;

    if ($company_name === "") $errors[] = "Company name is required.";
    if ($title === "") $errors[] = "Job title is required.";
    if (!in_array($status, $statuses, true)) $errors[] = "Invalid status.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT company_id FROM companies WHERE name = ?");
        $stmt->execute([$company_name]);
        $company = $stmt->fetch();

        if ($company) {
            $company_id = $company["company_id"];
        } else {
            $stmt = $pdo->prepare("INSERT INTO companies (name) VALUES (?)");
            $stmt->execute([$company_name]);
            $company_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("
            UPDATE jobs
            SET company_id = ?, title = ?, status = ?, applied_date = ?
            WHERE job_id = ?
        ");
        $stmt->execute([$company_id, $title, $status, $applied_date, $id]);

        header("Location: index.php");
        exit;
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Job</title>
</head>
<body>
    <h1>Edit Job</h1>
    <p><a href="index.php">← Back</a></p>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <label>Company Name</label><br>
        <input name="company_name" value="<?= htmlspecialchars($job["company_name"]) ?>" required><br><br>

        <label>Job Title</label><br>
        <input name="title" value="<?= htmlspecialchars($job["title"]) ?>" required><br><br>

        <label>Status</label><br>
        <select name="status">
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $job["status"] === $s ? "selected" : "" ?>>
                    <?= $s ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Applied Date</label><br>
        <input type="date" name="applied_date" value="<?= htmlspecialchars($job["applied_date"] ?? "") ?>"><br><br>

        <button type="submit">Update Job</button>
    </form>
</body>
</html>