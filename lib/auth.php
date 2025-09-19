<?php
function is_logged_in(): bool {
  return isset($_SESSION['user_id']);
}

function auth_required(array $roles = []): void {
  if (!is_logged_in()) {
    header('Location: /login.php');
    exit;
  }
  if ($roles) {
    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, $roles, true)) {
      http_response_code(403);
      echo "Forbidden";
      exit;
    }
  }
}

function login(PDO $pdo, string $username, string $password): bool {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();
  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    return true;
  }
  return false;
}

function logout(): void {
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
  }
  session_destroy();
}
