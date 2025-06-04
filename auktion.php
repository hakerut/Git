<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Защита: Проверка за валидно ID (цял положителен int)
$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo '<div class="alert alert-danger">Ungültige Auktion ID.</div>';
    require_once __DIR__ . '/includes/footer.php'; exit;
}

// Защита: Използвай подготвени заявки (prepared statements) – вече е така навсякъде!
$stmt = $pdo->prepare("SELECT a.*, u.username FROM auctions a JOIN users u ON a.user_id = u.id WHERE a.id = :id");
$stmt->execute([':id' => $id]);
$auktion = $stmt->fetch();

if (!$auktion) {
    echo '<div class="alert alert-danger">Auktion nicht gefunden.</div>';
    require_once __DIR__ . '/includes/footer.php'; exit;
}

// Извличане на оферти с prepared statement
$stmt = $pdo->prepare("SELECT o.*, u.username FROM offers o JOIN users u ON o.user_id = u.id WHERE o.auction_id = :id ORDER BY o.price DESC");
$stmt->execute([':id' => $id]);
$angebote = $stmt->fetchAll();

// Извличане на коментари с prepared statement
$stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.auction_id = :id ORDER BY c.created_at ASC");
$stmt->execute([':id' => $id]);
$comments = $stmt->fetchAll();

// Защита: XSS – всички изходни данни минават през esc()
// Защита: броят на заявките е ограничен до една аукция, не могат да се виждат чужди данни
?>

<h2><?= esc($auktion['title']) ?></h2>
<div class="row">
    <div class="col-md-6">
        <img src="<?= esc($auktion['image'] ? $auktion['image'] : '/assets/placeholder.jpg') ?>" class="img-fluid mb-2" alt="Bild">
        <div><b>Kategorie:</b> <?= esc($auktion['category']) ?></div>
        <div><b>Ort:</b> <?= esc($auktion['location']) ?></div>
        <div><b>Erstellt von:</b> <?= esc($auktion['username']) ?></div>
        <div><b>Endet am:</b> <?= esc($auktion['deadline']) ?></div>
    </div>
    <div class="col-md-6">
        <div class="mb-3"><b>Beschreibung:</b><br><?= nl2br(esc($auktion['description'])) ?></div>
    </div>
</div>

<h4>Angebote</h4>
<?php if (!$angebote): ?>
    <div class="alert alert-info">Keine Angebote vorhanden.</div>
<?php endif; ?>
<ul class="list-group mb-3">
    <?php foreach ($angebote as $a): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>
                <?= esc($a['username']) ?>: <?= esc(number_format($a['price'], 2, ',', '.')) ?> &euro;
                <?php if (is_admin()): ?>
                    <a href="/admin/angebote.php?action=delete&id=<?= $a['id'] ?>"
                       class="btn btn-xs btn-danger ms-2"
                       style="font-size:0.8em;padding:2px 8px;"
                       onclick="return confirm('Wirklich löschen?')">[Löschen]</a>
                <?php endif; ?>
            </span>
            <span class="text-muted"><?= esc($a['created_at']) ?></span>
        </li>
    <?php endforeach; ?>
</ul>

<?php if (is_logged_in()): ?>
<h5>Angebot machen</h5>
<form method="post" action="angebot_machen.php" class="mb-4" autocomplete="off">
    <input type="hidden" name="auction_id" value="<?= esc($auktion['id']) ?>">
    <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
    <div class="mb-2">
        <input type="number" name="price" min="1" step="0.01" class="form-control" placeholder="Ihr Angebot in Euro" required>
    </div>
    <div class="mb-2">
        <textarea name="message" class="form-control" placeholder="Nachricht (optional)" maxlength="1000"></textarea>
    </div>
    <button class="btn btn-yellow" type="submit">Angebot abschicken</button>
</form>
<?php endif; ?>

<h5>Kommentare</h5>
<ul class="list-group mb-3">
    <?php foreach ($comments as $c): ?>
        <li class="list-group-item">
            <b><?= esc($c['username']) ?></b> <small class="text-muted"><?= esc($c['created_at']) ?></small>
            <?php if (is_admin()): ?>
                <a href="/admin/kommentare.php?action=delete&id=<?= $c['id'] ?>"
                   class="btn btn-xs btn-danger ms-2"
                   style="font-size:0.8em;padding:2px 8px;"
                   onclick="return confirm('Wirklich löschen?')">[Löschen]</a>
            <?php endif; ?>
            <br>
            <?= nl2br(esc($c['comment'])) ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php if (is_logged_in()): ?>
<form method="post" action="kommentar_absenden.php" autocomplete="off">
    <input type="hidden" name="auction_id" value="<?= esc($auktion['id']) ?>">
    <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
    <div class="mb-2">
        <textarea name="comment" class="form-control" placeholder="Ihr Kommentar" maxlength="1000" required></textarea>
    </div>
    <button class="btn btn-yellow" type="submit">Kommentar absenden</button>
</form>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>