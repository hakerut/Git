<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
    flash("Bitte zuerst anmelden.", "danger");
    redirect('login.php');
}

$stmt = $pdo->prepare("SELECT * FROM auctions WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$auktionen = $stmt->fetchAll();
?>

<h2>Meine Auktionen</h2>
<?php if (!$auktionen): ?>
    <div class="alert alert-info">Keine Auktionen gefunden.</div>
<?php endif; ?>
<?php foreach ($auktionen as $auktion): ?>
    <div class="card-auction">
        <img src="<?= esc($auktion['image'] ? $auktion['image'] : '/assets/placeholder.jpg') ?>" alt="Bild" />
        <div>
            <div class="title"><a href="auktion.php?id=<?= $auktion['id'] ?>"><?= esc($auktion['title']) ?></a></div>
            <div class="location"><?= esc($auktion['location']) ?> | Kategorie: <?= esc($auktion['category']) ?></div>
            <div class="deadline">Endet am: <?= esc($auktion['deadline']) ?></div>
            <div class="small text-muted">Erstellt am <?= esc($auktion['created_at']) ?></div>
        </div>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>