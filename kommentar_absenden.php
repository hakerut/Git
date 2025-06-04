<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

if (!is_logged_in()) {
    flash("Bitte zuerst anmelden.", "danger");
    redirect('login.php');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');

$auction_id = intval($_POST['auction_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$csrf = $_POST['csrf_token'] ?? '';

if (!verify_csrf($csrf)) {
    flash("Ungültiges CSRF-Token.", "danger");
    redirect("auktion.php?id=$auction_id");
}
if (strlen($comment) < 2) {
    flash("Kommentar zu kurz.", "danger");
    redirect("auktion.php?id=$auction_id");
}

$stmt = $pdo->prepare("INSERT INTO comments (auction_id, user_id, comment) VALUES (:a, :u, :c)");
$stmt->execute([
    ':a' => $auction_id,
    ':u' => $_SESSION['user_id'],
    ':c' => $comment
]);
flash("Kommentar gespeichert!", "success");
redirect("auktion.php?id=$auction_id");