<?php
require_once __DIR__ . "/../config/db.php";

$errors = [];
$statuses = ["Wishlist","Applied","Interviewing","Offer","Rejected"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $company_name = trim($_POST["company_name"] ?? "");
  $title = trim($_POST["title"] ?? "");
  $status = $_POST["status"] ?? "Wishlist";
  $applied_date = $_POST["applied_date"] ?? null;

  if ($company_name === "") $errors[] = "Company name is required.";
  if ($title === "") $errors[] = "Job title is required.";
  if (!in_array($status, $statuses, true)) $errors[] = "Invalid status.";

  if (empty($errors)) {
    // 1) Find or create company
    $stmt = $pdo->prepare("SELECT company_id FROM companies WHERE name = ?");
    $stmt->execute([$company_name]);
    $company = $stmt->fetch();

    if ($company) {
      $company_id = (int)$company["company_id"];
    } else {
      $stmt = $pdo->prepare("INSERT INTO companies (name) VALUES (?)");
      $stmt->execute([$company_name]);
      $company_id = (int)$pdo->lastInsertId();
    }

    // 2) Insert job
    $stmt = $pdo->prepare("
      INSERT INTO jobs (company_id, title, status, applied_date)
      VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$company_id, $title, $status, $applied_date ?: null]);

    header("Location: index.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Add Job</title>
</head>
<body>
  <h1>Add Job</h1>
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
    <input name="company_name" required><br><br>

    <label>Job Title</label><br>
    <input name="title" required><br><br>

    <label>Status</label><br>
    <select name="status">
      <?php foreach ($statuses as $s): ?>
        <option value="<?= $s ?>"><?= $s ?></option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Applied Date</label><br>
    <input type="date" name="applied_date"><br><br>

    <button type="submit">Save</button>
  </form>
</body>
</html>
