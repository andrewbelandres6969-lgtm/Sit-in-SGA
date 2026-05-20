<?php
require_once __DIR__ . '/../includes/app.php';

session_unset();
session_destroy();

header('Location: ' . app_url('index.php'));
exit();
