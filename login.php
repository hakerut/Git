<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php'; // Тук е връзката с базата

if (is_logged_in()) {
    redirect('/'); // Ако вече е логнат, го прати на началната
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF защита
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = "Ungültiges CSRF-Token.";
    } else {
        // Валидирай входа
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username && $password) {
            // Търси user в базата
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :u OR email = :u");
            $stmt->execute(['u' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                // Успешен login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                flash("Willkommen, " . esc($user['username']) . "!", "success");
                redirect("/");
            } else {
                $error = "Benutzername oder Passwort falsch.";
            }
        } else {
            $error = "Bitte alle Felder ausfüllen.";
        }
    }
}
?>
<?php require_once __DIR__ . '/header.php'; ?>
<h2>Anmelden</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>
<form method="post" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
    <div class="mb-3">
        <label for="username" class="form-label">Benutzername oder E-Mail</label>
        <input type="text" class="form-control" id="username" name="username" required autofocus>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Passwort</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Anmelden</button>
</form>
<?php require_once __DIR__ . '/footer.php'; ?>