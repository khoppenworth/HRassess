<?php
session_start();
require_once __DIR__ . '/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'dashboard.php'));
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: ' . ($user['role']==='admin'?'admin/dashboard.php':'dashboard.php'));
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Enter username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang='en'>
<head><meta charset='UTF-8'><title>EPSS Login</title></head>
<body>
<h2>Login</h2>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method='post'>
<input name='username' placeholder='Username' required><br>
<input type='password' name='password' placeholder='Password' required><br>
<button type='submit'>Login</button>
</form>
</body>
</html>
