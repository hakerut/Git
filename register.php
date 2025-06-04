<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$errors = [];
$username = $email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf($csrf)) {
        $errors[] = "Ungültiges CSRF-Token.";
    }
    if (strlen($username) < 3) {
        $errors[] = "Benutzername zu kurz.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ungültige Email-Adresse.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Passwort zu kurz (mindestens 6 Zeichen).";
    }
    if ($password !== $password2) {
        $errors[] = "Passwörter stimmen nicht überein.";
    }

    // Check if username/email is taken
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e");
    $stmt->execute([':u' => $username, ':e' => $email]);
    if ($stmt->fetch()) {
        $errors[] = "Benutzername oder Email bereits vergeben.";
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:u, :e, :p)");
        $stmt->execute([':u' => $username, ':e' => $email, ':p' => $hash]);
        flash("Registrierung erfolgreich! Jetzt anmelden.", "success");
        redirect('login.php');
    }
}
?>

<h2>Registrieren</h2>
<form method="post" class="col-md-6 col-12">
    <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= implode('<br>', array_map('esc', $errors)) ?></div>
    <?php endif; ?>
    <div class="mb-3">
        <label>Benutzername</label>
        <input type="text" class="form-control" name="username" value="<?= esc($username) ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" class="form-control" name="email" value="<?= esc($email) ?>" required>
    </div>
    <div class="mb-3">
        <label>Passwort</label>
        <input type="password" class="form-control" name="password" required>
    </div>
    <div class="mb-3">
        <label>Passwort wiederholen</label>
        <input type="password" class="form-control" name="password2" required>
    </div>
    <button class="btn btn-yellow" type="submit">Registrieren</button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>