<?php
require_once __DIR__ . '/includes/helpers.php';
session_destroy();
flash("Erfolgreich abgemeldet!", "success");
redirect('index.php');