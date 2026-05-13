<?php
// login.php — Login form
require_once 'includes/db.php';

$pageTitle  = 'Login — Nature-Drop';
$activePage = '';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Please enter your email and password.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <p style="text-align:center;font-size:2.5rem;margin-bottom:0.5rem;">💧</p>
        <h1 class="auth-title" data-en="Welcome Back" data-gu="પાછા આવ્યા">Welcome Back</h1>
        <p class="auth-sub" data-en="Sign in to your NatureDrop account" data-gu="તમારા NatureDrop ખાતામાં સાઇન ઇન કરો">Sign in to your NatureDrop account</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="validate">
            <div class="form-group">
                <label class="form-label" for="email" data-en="Email Address" data-gu="ઇમેઇલ સરનામું">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="you@example.com"
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password" data-en="Password" data-gu="પાસવર્ડ">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn-primary form-submit" data-en="Sign In" data-gu="સાઇન ઇન">Sign In</button>
        </form>

        <p class="auth-footer">
            <span data-en="Don't have an account?" data-gu="ખાતું નથી?">Don't have an account?</span>
            <a href="register.php" data-en="Register here" data-gu="અહીં નોંધણી કરો"> Register here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>