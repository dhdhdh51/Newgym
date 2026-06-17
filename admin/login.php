<?php
/**
 * Admin Login Page
 * Handles authentication for the admin panel
 */
require_once '../config.php';
require_once '../functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_id'] = $user['id'];
                    redirect('dashboard.php');
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $error = 'A system error occurred. Please try again.';
            }
        }
    }
}

$gymName = getSetting('gym_name') ?? 'Gym Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo e($gymName); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap">
    <style>
        /* ===== Tokens (standalone, maroon palette) ===== */
        :root {
            --primary: #7B1E2B;
            --primary-dark: #5E1620;
            --primary-accent: #A52234;
            --primary-soft: #F4E6E8;
            --bg: #F1F3F7;
            --surface: #FFFFFF;
            --text: #16181B;
            --text-muted: #6B7280;
            --border: #E6E8EC;
            --danger: #DC3545;
            --r-sm: 8px;
            --r-lg: 16px;
            --grad-primary: linear-gradient(135deg, var(--primary), var(--primary-accent));
            --font-body: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            --font-head: 'Oswald', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        /* ===== Reset ===== */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background:
                radial-gradient(1200px 600px at 100% -10%, var(--primary-soft), transparent 60%),
                radial-gradient(900px 500px at -10% 110%, #E9EBF1, transparent 55%),
                var(--bg);
        }

        /* ===== Login card ===== */
        .login-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 60px rgba(16, 24, 40, 0.14);
        }

        .login-header { text-align: center; margin-bottom: 28px; }

        .login-header i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            margin-bottom: 14px;
            font-size: 1.8rem;
            color: #fff;
            background: var(--grad-primary);
            border-radius: var(--r-lg);
            box-shadow: 0 10px 22px rgba(123, 30, 43, 0.32);
        }

        .login-header h1 {
            font-family: var(--font-head);
            font-weight: 600;
            font-size: 1.5rem;
            letter-spacing: 0.4px;
            color: var(--text);
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* ===== Form ===== */
        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--text);
            font-size: 0.88rem;
        }

        .form-group .input-wrapper { position: relative; }

        .form-group .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 44px;
            min-height: 46px;
            border: 1px solid var(--border);
            border-radius: var(--r-sm);
            background: var(--surface);
            color: var(--text);
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .form-group .input-wrapper:focus-within i { color: var(--primary); }

        /* ===== Submit button ===== */
        .btn-login {
            width: 100%;
            padding: 14px;
            min-height: 50px;
            background: var(--grad-primary);
            color: #fff;
            border: none;
            border-radius: var(--r-sm);
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(123, 30, 43, 0.32);
            filter: brightness(1.03);
        }

        .btn-login:active { transform: translateY(0); }

        .btn-login:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* ===== Error message ===== */
        .error-message {
            background: #FCEBEC;
            color: #8A1C26;
            border: 1px solid transparent;
            border-left: 4px solid var(--danger);
            padding: 12px 14px;
            border-radius: var(--r-sm);
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        /* ===== Responsive ===== */
        @media (max-width: 480px) {
            body { padding: 14px; }
            .login-container { padding: 28px 22px; border-radius: var(--r-lg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-dumbbell"></i>
            <h1><?php echo e($gymName); ?></h1>
            <p>Admin Panel Login</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter username" required value="<?php echo e($_POST['username'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</body>
</html>
