<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

if (!is_logged_in()) {
    flash("Bitte zuerst anmelden.", "danger");
    redirect('login.php');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');

$auction_id = intval($_POST['auction_id'] ?? 0);
$price = floatval($_POST['price'] ?? 0);
$message = trim($_POST['message'] ?? '');
$csrf = $_POST['csrf_token'] ?? '';

if (!verify_csrf($csrf)) {
    flash("Ungültiges CSRF-Token.", "danger");
    redirect("auktion.php?id=$auction_id");
}
if ($price <= 0) {
    flash("Ungültiges Angebot.", "danger");
    redirect("auktion.php?id=$auction_id");
}

$stmt = $pdo->prepare("INSERT INTO offers (auction_id, user_id, price, message) VALUES (:a, :u, :p, :m)");
$stmt->execute([
    ':a' => $auction_id,
    ':u' => $_SESSION['user_id'],
    ':p' => $price,
    ':m' => $message
]);
flash("Angebot gespeichert!", "success");
redirect("auktion.php?id=$auction_id");