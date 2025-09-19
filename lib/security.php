<?php
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function csrf_validate(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = $_POST['csrf'] ?? '';
    if (!$t || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
      http_response_code(400);
      echo "Bad CSRF token";
      exit;
    }
  }
}

// simple placeholder
function rate_limit_check(): void {
  // implement if needed (IP/user-based throttling)
}
