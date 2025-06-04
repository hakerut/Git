<?php
require_once '../includes/db.php';

$q = trim($_GET['q'] ?? '');
$cat = trim($_GET['category'] ?? '');
$city = trim($_GET['city'] ?? '');

$sql = "SELECT * FROM auctions WHERE status='open'";
$params = [];
if ($q) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($cat) {
    $sql .= " AND category = ?";
    $params[] = $cat;
}
if ($city) {
    $sql .= " AND city = ?";
    $params[] = $city;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>
<!-- HTML: изкарва резултатите като карти -->