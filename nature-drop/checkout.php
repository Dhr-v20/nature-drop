<?php
// checkout.php — Checkout form → creates order → redirects to invoice
require_once 'includes/db.php';

if (!isLoggedIn()) {
    header('Location: login.php?next=checkout.php');
    exit;
}

$uid = currentUserId();
$pdo = getDB();

// ── AUTO-MIGRATION: add missing columns silently ──────────────────────────
// This fixes "Unknown column" errors when upgrading from v1 schema.
function ndMigrate(PDO $pdo): void {
    // --- orders table ---
    $existingCols = $pdo->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);
    $orderAlter = [];
    if (!in_array('invoice_number',  $existingCols)) $orderAlter[] = "ADD COLUMN invoice_number  VARCHAR(30)   NULL          AFTER id";
    if (!in_array('tax_amount',      $existingCols)) $orderAlter[] = "ADD COLUMN tax_amount      DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER total_amount";
    if (!in_array('discount_amount', $existingCols)) $orderAlter[] = "ADD COLUMN discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER tax_amount";
    if (!in_array('grand_total',     $existingCols)) $orderAlter[] = "ADD COLUMN grand_total     DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER discount_amount";
    if (!in_array('delivery_address',$existingCols)) $orderAlter[] = "ADD COLUMN delivery_address TEXT NULL     AFTER supply_method";
    if (!in_array('notes',           $existingCols)) $orderAlter[] = "ADD COLUMN notes           TEXT NULL     AFTER delivery_address";
    if (!empty($orderAlter)) {
        $pdo->exec("ALTER TABLE orders " . implode(', ', $orderAlter));
        // Backfill grand_total from total_amount
        $pdo->exec("UPDATE orders SET grand_total = total_amount WHERE grand_total = 0");
    }
    // Unique index — ignore if already exists
    try { $pdo->exec("ALTER TABLE orders ADD UNIQUE INDEX idx_nd_inv (invoice_number)"); } catch (PDOException $e) {}

    // --- order_items table ---
    $oiCols = $pdo->query("SHOW COLUMNS FROM order_items")->fetchAll(PDO::FETCH_COLUMN);
    $oiAlter = [];
    if (!in_array('product_name', $oiCols)) $oiAlter[] = "ADD COLUMN product_name VARCHAR(150) NOT NULL DEFAULT '' AFTER product_id";
    if (!in_array('subtotal',     $oiCols)) $oiAlter[] = "ADD COLUMN subtotal     DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER unit_price";
    if (!empty($oiAlter)) {
        $pdo->exec("ALTER TABLE order_items " . implode(', ', $oiAlter));
        $pdo->exec("UPDATE order_items SET subtotal = unit_price * quantity WHERE subtotal = 0");
    }

    // --- cart table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        user_id    INT NOT NULL,
        product_id INT NOT NULL,
        quantity   INT NOT NULL DEFAULT 1,
        added_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_user_product (user_id, product_id),
        FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
}

try { ndMigrate($pdo); } catch (PDOException $e) { error_log('NatureDrop migrate: ' . $e->getMessage()); }
// ─────────────────────────────────────────────────────────────────────────

// Load cart
$stmt = $pdo->prepare(
    "SELECT c.quantity, p.id AS product_id, p.name, p.price, p.stock
     FROM cart c JOIN products p ON p.id=c.product_id
     WHERE c.user_id=? ORDER BY c.added_at DESC"
);
$stmt->execute([$uid]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$subtotal = array_sum(array_map(fn($r) => $r['price'] * $r['quantity'], $cartItems));
$tax      = round($subtotal * 0.05, 2);
$total    = round($subtotal + $tax, 2);

// Load user details
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$userStmt->execute([$uid]);
$user = $userStmt->fetch();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addr    = trim($_POST['address']  ?? '');
    $method  = $_POST['supply_method'] ?? 'home_delivery';
    $notes   = trim($_POST['notes']    ?? '');
    $allowed = ['home_delivery','store_pickup','subscription'];

    if (!$addr) {
        $error = 'Please enter a delivery address.';
    } elseif (!in_array($method, $allowed)) {
        $error = 'Invalid supply method.';
    } else {
        try {
            $pdo->beginTransaction();

            // Invoice number: ND-YYYYMMDD-XXXXXX
            $inv = 'ND-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $ins = $pdo->prepare(
                "INSERT INTO orders
                    (user_id, invoice_number, total_amount, tax_amount,
                     discount_amount, grand_total, supply_method, delivery_address, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $ins->execute([$uid, $inv, $subtotal, $tax, 0.00, $total, $method, $addr, $notes]);
            $orderId = $pdo->lastInsertId();

            // Insert each order item
            $insItem = $pdo->prepare(
                "INSERT INTO order_items
                    (order_id, product_id, product_name, quantity, unit_price, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            foreach ($cartItems as $item) {
                $sub = round($item['price'] * $item['quantity'], 2);
                $insItem->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $sub,
                ]);
                // Safely decrement stock
                $pdo->prepare(
                    "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
                )->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
            }

            // Clear cart
            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$uid]);
            $pdo->commit();

            header("Location: invoice.php?order=" . urlencode($inv));
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'Order failed. Please try again. (' . $e->getMessage() . ')';
        }
    }
}

$pageTitle  = 'Checkout — Nature-Drop';
$activePage = '';
include 'includes/header.php';
?>

<div class="page-hero" style="padding-bottom:1rem;">
    <span class="section-label">💳 Checkout</span>
    <h1 class="page-hero-title">Complete Your Order</h1>
</div>

<section class="section" style="padding-top:1rem;">
<div class="section-inner">

<?php if ($error): ?>
<div class="alert alert-error" style="max-width:800px;margin:0 auto 1.5rem;">
    ❌ <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="checkout-layout">

    <!-- LEFT: Form -->
    <div>
        <form method="POST" class="validate">

            <!-- Delivery info -->
            <div class="card" style="padding:2rem;margin-bottom:1.5rem;">
                <h2 style="font-family:var(--font-display);font-size:1.5rem;font-weight:300;margin-bottom:1.5rem;color:var(--aqua);">
                    📍 Delivery Details
                </h2>
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($user['name']) ?>" readonly style="opacity:0.7;cursor:default;">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" readonly style="opacity:0.7;cursor:default;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" readonly style="opacity:0.7;cursor:default;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="address">Delivery Address *</label>
                    <textarea name="address" id="address" class="form-input" rows="3" required
                        placeholder="House No., Street, City, Pin Code"><?= htmlspecialchars($_POST['address'] ?? $user['address'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Supply method -->
            <div class="card" style="padding:2rem;margin-bottom:1.5rem;">
                <h2 style="font-family:var(--font-display);font-size:1.5rem;font-weight:300;margin-bottom:1.5rem;color:var(--aqua);">
                    🚚 Supply Method
                </h2>
                <div class="supply-options">
                    <?php
                    $methods = [
                        ['home_delivery', '🚚', 'Home Delivery',       'Delivered to your address. FREE.'],
                        ['store_pickup',  '🏪', 'Store Pickup',        'Collect from nearest NatureDrop point.'],
                        ['subscription',  '🔄', 'Monthly Subscription','Auto-refill, 25% discount on total.'],
                    ];
                    $sel = $_POST['supply_method'] ?? 'home_delivery';
                    foreach ($methods as $m): ?>
                    <label class="supply-option <?= $sel===$m[0]?'selected':'' ?>">
                        <input type="radio" name="supply_method" value="<?= $m[0] ?>"
                            <?= $sel===$m[0]?'checked':'' ?> onchange="selectSupply(this)">
                        <span class="supply-opt-icon"><?= $m[1] ?></span>
                        <span>
                            <strong><?= $m[2] ?></strong>
                            <small><?= $m[3] ?></small>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Notes -->
            <div class="card" style="padding:2rem;margin-bottom:1.5rem;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label" for="notes">📝 Order Notes (optional)</label>
                    <textarea name="notes" id="notes" class="form-input" rows="2"
                        placeholder="Special instructions, delivery time preference…"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary" style="width:100%;padding:1.1rem;font-size:1rem;"
                onclick="this.disabled=true;this.textContent='Processing…';this.closest('form').submit();">
                ✅ Place Order &amp; Generate Invoice
            </button>
        </form>
    </div>

    <!-- RIGHT: Order summary -->
    <div class="card" style="padding:2rem;position:sticky;top:calc(var(--nav-h)+1rem);align-self:start;">
        <h2 style="font-family:var(--font-display);font-size:1.5rem;font-weight:300;margin-bottom:1.5rem;">Order Review</h2>

        <?php foreach ($cartItems as $item): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;
                    padding:0.6rem 0;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.88rem;">
            <span style="color:var(--mist);">
                <?= htmlspecialchars($item['name']) ?>
                <span style="color:var(--accent);">×<?= $item['quantity'] ?></span>
            </span>
            <span>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
        </div>
        <?php endforeach; ?>

        <div style="margin-top:1rem;">
            <div class="summary-row"><span>Subtotal</span><span>₹<?= number_format($subtotal,2) ?></span></div>
            <div class="summary-row"><span>GST (5%)</span><span>₹<?= number_format($tax,2) ?></span></div>
            <div class="summary-row"><span>Delivery</span><span style="color:var(--accent);">FREE</span></div>
            <div style="border-top:1px solid var(--glass-border);margin:0.75rem 0;"></div>
            <div class="summary-row summary-total"><span>Total</span><span>₹<?= number_format($total,2) ?></span></div>
        </div>
    </div>

</div>
</div>
</section>

<style>
.checkout-layout { display:grid; grid-template-columns:1fr 320px; gap:2rem; align-items:start; }
.supply-options  { display:flex; flex-direction:column; gap:0.75rem; }
.supply-option {
    display:flex; align-items:center; gap:1rem;
    padding:1rem 1.25rem; border-radius:var(--radius-md);
    border:1px solid var(--glass-border); cursor:pointer;
    transition:border-color .25s,background .25s;
    background:rgba(255,255,255,0.03);
}
.supply-option input[type=radio] { display:none; }
.supply-option.selected { border-color:var(--aqua); background:rgba(27,168,213,0.08); }
.supply-option:hover    { border-color:rgba(92,225,200,0.4); }
.supply-opt-icon { font-size:1.6rem; }
.supply-option strong { display:block; font-size:0.95rem; }
.supply-option small  { font-size:0.8rem; color:var(--text-light); }
.summary-row  { display:flex; justify-content:space-between; font-size:0.9rem; color:var(--mist); padding:0.4rem 0; }
.summary-total{ font-size:1.1rem; font-weight:600; color:var(--white); }
@media(max-width:860px){ .checkout-layout{ grid-template-columns:1fr; } }
</style>

<script>
function selectSupply(radio) {
    document.querySelectorAll('.supply-option').forEach(l => l.classList.remove('selected'));
    radio.closest('.supply-option').classList.add('selected');
}
</script>

<?php include 'includes/footer.php'; ?>