<?php
// register.php — Registration form
require_once 'includes/db.php';

$pageTitle  = 'Register — Nature-Drop';
$activePage = '';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $address  = trim($_POST['address']  ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    // Validation
    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $pdo  = getDB();
            // Check duplicate email
            $chk  = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $chk->execute([$email]);
            if ($chk->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $ins  = $pdo->prepare("INSERT INTO users (name, email, phone, address, password) VALUES (?,?,?,?,?)");
                $ins->execute([$name, $email, $phone, $address, $hash]);

                // Auto-login after register
                $_SESSION['user_id']   = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                header('Location: index.php');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card" style="max-width:520px;">
        <p style="text-align:center;font-size:2.5rem;margin-bottom:0.5rem;">🌿</p>
        <h1 class="auth-title" data-en="Create Account" data-gu="ખાતું બનાવો">Create Account</h1>
        <p class="auth-sub" data-en="Join 50,000+ Nature-Drop customers" data-gu="50,000+ Nature-Drop ગ્રાહકોમાં જોડાઓ">Join 50,000+ Nature-Drop customers</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="validate">

            <div class="form-group">
                <label class="form-label" for="name" data-en="Full Name *" data-gu="પૂરું નામ *">Full Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input"
                    placeholder="Kiran Patel"
                    required
                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    autocomplete="name"
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="email" data-en="Email *" data-gu="ઇમેઇલ *">Email *</label>
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
                    <label class="form-label" for="phone" data-en="Phone" data-gu="ફોન">Phone</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-input"
                        placeholder="+91 98765 43210"
                        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                        autocomplete="tel"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address" data-en="Delivery Address" data-gu="ડિલિવરી સરનામું">Delivery Address</label>
                <textarea
                    id="address"
                    name="address"
                    class="form-input"
                    rows="2"
                    placeholder="House No., Street, City, Pin"
                    style="resize:vertical;"
                ><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password" data-en="Password *" data-gu="પાસવર્ડ *">Password *</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Min. 6 characters"
                        required
                        autocomplete="new-password"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="confirm" data-en="Confirm Password *" data-gu="પાસવર્ડ પ્રમાણ *">Confirm Password *</label>
                    <input
                        type="password"
                        id="confirm"
                        name="confirm"
                        class="form-input"
                        placeholder="Repeat password"
                        required
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <button type="submit" class="btn btn-primary form-submit" data-en="Create My Account" data-gu="મારું ખાતું બનાવો">Create My Account</button>
        </form>

        <p class="auth-footer">
            <span data-en="Already have an account?" data-gu="પહેલેથી ખાતું છે?">Already have an account?</span>
            <a href="login.php" data-en=" Sign in" data-gu=" સાઇન ઇન"> Sign in</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>