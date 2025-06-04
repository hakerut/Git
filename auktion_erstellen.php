<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
    flash("Bitte zuerst anmelden.", "danger");
    redirect('login.php');
}

$errors = [];
$title = $description = $category = $location = $deadline = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf($csrf)) {
        $errors[] = "Ungültiges CSRF-Token.";
    }
    if (strlen($title) < 4) {
        $errors[] = "Titel zu kurz.";
    }
    if (!$deadline || strtotime($deadline) < time()) {
        $errors[] = "Bitte gültiges Enddatum wählen.";
    }

    // Datei-Upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Nur Bilder (.jpg, .png, .gif) erlaubt.";
        } else {
            $newname = 'assets/auction_' . time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $newname);
            $image = '/' . $newname;
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO auctions (user_id, title, description, category, location, image, deadline) 
            VALUES (:uid, :t, :d, :c, :l, :i, :dl)");
        $stmt->execute([
            ':uid' => $_SESSION['user_id'],
            ':t' => $title,
            ':d' => $description,
            ':c' => $category,
            ':l' => $location,
            ':i' => $image,
            ':dl' => $deadline
        ]);
        flash("Auktion erfolgreich erstellt!", "success");
        redirect('meine_auktionen.php');
    }
}
?>

<h2>Auktion erstellen</h2>
<form method="post" enctype="multipart/form-data" class="col-md-8 col-12">
    <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= implode('<br>', array_map('esc', $errors)) ?></div>
    <?php endif; ?>
    <div class="mb-3">
        <label>Titel</label>
        <input type="text" class="form-control" name="title" value="<?= esc($title) ?>" required>
    </div>
    <div class="mb-3">
        <label>Beschreibung</label>
        <textarea class="form-control" name="description"><?= esc($description) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Kategorie</label>
        <input type="text" class="form-control" name="category" value="<?= esc($category) ?>">
    </div>
    <div class="mb-3">
        <label>Ort</label>
        <input type="text" class="form-control" name="location" value="<?= esc($location) ?>">
    </div>
    <div class="mb-3">
        <label>Enddatum</label>
        <input type="datetime-local" class="form-control" name="deadline" value="<?= esc($deadline) ?>" required>
    </div>
    <div class="mb-3">
        <label>Bild (optional)</label>
        <input type="file" class="form-control" name="image" accept="image/*">
    </div>
    <button class="btn btn-yellow" type="submit">Auktion erstellen</button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>