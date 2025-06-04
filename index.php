<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Suche
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [];
$sql = "SELECT a.*, u.username FROM auctions a JOIN users u ON a.user_id = u.id WHERE 1";
if ($search !== "") {
    $sql .= " AND (a.title LIKE :q OR a.description LIKE :q OR a.category LIKE :q OR a.location LIKE :q)";
    $params[':q'] = '%' . $search . '%';
}
$sql .= " ORDER BY a.created_at DESC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$auktionen = $stmt->fetchAll();
?>
<h1>Aktuelle Auktionen</h1>
<form class="row mb-4" method="get" action="">
    <div class="col-10 col-md-6">
        <input type="text" name="q" value="<?= esc($search) ?>" class="form-control" placeholder="Suche nach Titel, Kategorie, Ort ...">
    </div>
    <div class="col-2">
        <button class="btn btn-yellow w-100" type="submit">Suchen</button>
    </div>
</form>
<?php if (count($auktionen) == 0): ?>
    <div class="alert alert-info">Keine Auktionen gefunden.</div>
<?php endif; ?>
<?php foreach ($auktionen as $auktion): ?>
    <div class="card-auction">
        <img src="<?= esc($auktion['image'] ? $auktion['image'] : '/assets/placeholder.jpg') ?>" alt="Bild" />
        <div>
            <div class="title"><a href="auktion.php?id=<?= $auktion['id'] ?>"><?= esc($auktion['title']) ?></a></div>
            <div class="location"><?= esc($auktion['location']) ?> | Kategorie: <?= esc($auktion['category']) ?></div>
            <div class="deadline">Endet am: <?= esc($auktion['deadline']) ?></div>
            <div class="small text-muted">von <?= esc($auktion['username']) ?> | Erstellt am <?= esc($auktion['created_at']) ?></div>
        </div>
    </div>
<?php endforeach; ?>
<?php require_once __DIR__ . '/includes/footer.php';