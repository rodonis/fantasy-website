<?php
declare(strict_types=1);

class AuthController {
    public function loginForm(array $p): void {
        $tweaks = get_tweaks();
        $error  = null;
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/login.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function login(array $p): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (Auth::login($username, $password)) {
            $next = $_GET['next'] ?? '/';
            header('Location: ' . $next);
            exit;
        }
        $tweaks = get_tweaks();
        $error  = 'Invalid username or password.';
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/login.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function logout(array $p): void {
        Auth::logout();
        header('Location: /');
        exit;
    }

    public function registerForm(array $p): void {
        if (!($_ENV['ALLOW_SIGNUP'] ?? getenv('ALLOW_SIGNUP'))) {
            http_response_code(403);
            echo 'Registration is closed.';
            return;
        }
        $tweaks = get_tweaks();
        $error  = null;
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/register.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function register(array $p): void {
        if (!($_ENV['ALLOW_SIGNUP'] ?? getenv('ALLOW_SIGNUP'))) {
            http_response_code(403);
            echo 'Registration is closed.';
            return;
        }
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';
        $tweaks   = get_tweaks();
        $error    = null;

        if ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (!Auth::register($username, $password)) {
            $error = 'Username already taken or too short.';
        } else {
            Auth::login($username, $password);
            header('Location: /');
            exit;
        }
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/register.php';
        require __DIR__ . '/../views/layout-close.php';
    }
}
