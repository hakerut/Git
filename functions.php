<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// HTML escaping
function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// CSRF token generation and verification
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Flash messages
function flash($msg = null, $type = "info") {
    if ($msg !== null) {
        $_SESSION['flash'][] = ['msg' => $msg, 'type' => $type];
    } else {
        if (!empty($_SESSION['flash'])) {
            $out = '';
            foreach ($_SESSION['flash'] as $f) {
                $out .= '<div class="alert alert-' . esc($f['type']) . '">' . esc($f['msg']) . '</div>';
            }
            unset($_SESSION['flash']);
            return $out;
        }
    }
    return '';
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

// Login check
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

// Admin check
function is_admin() {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}