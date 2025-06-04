<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

if (!isLoggedIn()) {
    header("Location: /login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken($_POST['csrf_token']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $city = trim($_POST['city']);
    $area = floatval($_POST['area']);
    $budget = floatval($_POST['budget']);
    $category = trim($_POST['category']);
    $end_time = $_POST['end_time'];
    $errors = [];

    // Валидация
    if (!$title || !$description || !$city || !$budget || !$category || !$end_time) {
        $errors[] = "Всички полета са задължителни.";
    }
    if (strtotime($end_time) <= time()) {
        $errors[] = "Крайната дата трябва да е в бъдещето.";
    }
    if ($budget <= 0) {
        $errors[] = "Бюджетът трябва да е положителен.";
    }

    // Проверка за лимит при free акаунт
    $user_id = $_SESSION['user_id'];
    $active_count = getActiveAuctionsCount($user_id);
    if (!userHasPremium($user_id) && $active_count >= 5) {
        $errors[] = "Free акаунт може да има до 5 активни аукциона.";
    }

    if (empty($errors)) {
        // Качване на снимка (примерно)
        $img_url = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            $img_url = uploadAuctionImage($_FILES['image']);
        }
        $stmt = $pdo->prepare("INSERT INTO auctions (user_id, title, description, city, area, budget, category, end_time, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $description, $city, $area, $budget, $category, $end_time, $img_url]);
        header("Location: /auctions/my.php");
        exit;
    }
}
$csrf_token = generateCsrfToken();
?>
<!-- HTML форма с полета за аукцион и CSRF токен -->