<?php
// Стартиране на сесията, ако още не е стартирана
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверява дали потребителят е логнат
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Проверява дали потребителят е админ
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Ескейп на HTML
function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

// Генерира и връща CSRF token
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Проверява CSRF токена
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Пренасочване
function redirect($url) {
    header("Location: $url");
    exit;
}

// Flash съобщения (поддържа множество съобщения)
function flash($message = "", $type = "success") {
    if ($message) {
        $_SESSION['flash'][] = ['msg' => $message, 'type' => $type];
    } elseif (!empty($_SESSION['flash'])) {
        $out = '';
        foreach ($_SESSION['flash'] as $f) {
            $out .= '<div class="alert alert-' . esc($f['type']) . '">' . esc($f['msg']) . '</div>';
        }
        unset($_SESSION['flash']);
        return $out;
    }
    return '';
}
?>