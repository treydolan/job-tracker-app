<?php
require_once __DIR__ . "/../config/db.php";

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM jobs WHERE job_id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;