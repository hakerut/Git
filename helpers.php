<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function flash($message = "", $type = "success") {
    if ($message) {
        $_SESSION['flash'] = ['msg' => $message, 'type' => $type];
    } elseif (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-' . esc($flash['type']) . '">' . esc($flash['msg']) . '</div>';
    }
    return '';
}
?>