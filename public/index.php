<?php
require_once __DIR__ . "/../config/db.php";

// Pull jobs + company name
$sql = "
  SELECT 
    j.job_id,
    j.title,
    j.status,
    j.applied_date,
    c.name AS company_name
  FROM jobs j
  JOIN companies c ON c.company_id = j.company_id
  ORDER BY j.job_id DESC
";
$jobs = $pdo->query($sql)->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Job Tracker</title>
  <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
  <h1>Job Tracker</h1>
  <p><a href="add_job.php">+ Add Job</a></p>

  <table border="1" cellpadding="8">
    <thead>
      <tr>
        <th>Company</th>
        <th>Title</th>
        <th>Status</th>
        <th>Applied</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($jobs) === 0): ?>
        <tr><td colspan="5">No jobs yet.</td></tr>
      <?php else: ?>
        <?php foreach ($jobs as $job): ?>
          <tr>
            <td><?= htmlspecialchars($job["company_name"]) ?></td>
            <td><?= htmlspecialchars($job["title"]) ?></td>
            <td><?= htmlspecialchars($job["status"]) ?></td>
            <td><?= htmlspecialchars($job["applied_date"] ?? "") ?></td>
            <td>
              <a href="edit_job.php?id=<?= (int)$job["job_id"] ?>">Edit</a>
              |
              <a href="delete_job.php?id=<?= (int)$job["job_id"] ?>" onclick="return confirm('Delete this job?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
