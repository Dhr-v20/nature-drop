<?php
// cart.php — Shopping cart with live quantity controls
require_once 'includes/db.php';

if (!isLoggedIn()) {
    header('Location: login.php?next=cart.php');
    exit;
}

$pageTitle  = 'Your Cart — Nature-Drop';
$activePage = 'cart';
$uid        = currentUserId();
$pdo        = getDB();

// Load cart rows with product details
$stmt = $pdo->prepare(
    "SELECT c.id AS cart_id, c.quantity, p.id AS product_id,
            p.name, p.price, p.image, p.stock, p.category
     FROM cart c
     JOIN products p ON p.id = c.product_id
     WHERE c.user_id = ?
     ORDER BY c.added_at DESC"
);
$stmt->execute([$uid]);
$cartItems = $stmt->fetchAll();

$emojis = [
    'crystal-500.jpg'      => '💧',
    'premium-1l.jpg'       => '🏔️',
    'family-5l.jpg'        => '🫧',
    'sparkling-330.jpg'    => '✨',
    'dispenser.jpg'        => '🚰',
    'subscription-20l.jpg' => '📦',
];

$subtotal = array_sum(array_map(fn($r) => $r['price'] * $r['quantity'], $cartItems));
$tax      = round($subtotal * 0.05, 2);   // 5% GST
$total    = $subtotal + $tax;

include 'includes/header.php';
?>

<div class="page-hero" style="padding-bottom:1rem;">
    <span class="section-label">🛒 Your Cart</span>
    <h1 class="page-hero-title" data-en="Shopping Cart" data-gu="ખરીદી કાર્ટ">Shopping Cart</h1>
</div>

<section class="section" style="padding-top:1rem;">
<div class="section-inner">

<?php if (empty($cartItems)): ?>
<!-- ── EMPTY CART ── -->
<div class="card" style="text-align:center;padding:5rem 2rem;">
    <p style="font-size:4rem;margin-bottom:1rem;">🛒</p>
    <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:300;margin-bottom:0.75rem;">Your cart is empty</h2>
    <p style="color:var(--text-light);margin-bottom:2rem;">Add some pure water products to get started.</p>
    <a href="products.php" class="btn btn-primary btn-lg">Browse Products</a>
</div>

<?php else: ?>
<!-- ── CART LAYOUT ── -->
<div class="cart-layout">

    <!-- LEFT: Items -->
    <div class="cart-items-col">
        <div id="cart-items-list">
        <?php foreach ($cartItems as $item):
            $emoji   = $emojis[$item['image']] ?? '💧';
            $lineTotal = $item['price'] * $item['quantity'];
        ?>
        <div class="cart-row card" data-pid="<?= $item['product_id'] ?>" id="cart-row-<?= $item['product_id'] ?>">
            <!-- Emoji thumb -->
            <div class="cart-thumb"><?= $emoji ?></div>

            <!-- Info -->
            <div class="cart-info">
                <span class="badge" style="margin-bottom:0.3rem;"><?= ucfirst($item['category']) ?></span>
                <h3 class="cart-name"><?= htmlspecialchars($item['name']) ?></h3>
                <span class="cart-unit-price">₹<?= number_format($item['price'],2) ?> / unit</span>
            </div>

            <!-- Qty controls -->
            <div class="cart-qty-wrap">
                <button class="qty-btn" onclick="changeQty(<?= $item['product_id'] ?>, -1)">−</button>
                <span class="qty-val" id="qty-<?= $item['product_id'] ?>"><?= $item['quantity'] ?></span>
                <button class="qty-btn" onclick="changeQty(<?= $item['product_id'] ?>, +1)">+</button>
            </div>

            <!-- Line total -->
            <div class="cart-line-total" id="lt-<?= $item['product_id'] ?>">
                ₹<?= number_format($lineTotal, 2) ?>
            </div>

            <!-- Remove -->
            <button class="cart-remove" onclick="removeItem(<?= $item['product_id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
        </div>

        <div style="margin-top:1rem;display:flex;gap:1rem;flex-wrap:wrap;">
            <a href="products.php" class="btn btn-outline">← Continue Shopping</a>
            <button class="btn btn-outline" style="color:#e55a5a;border-color:rgba(229,90,90,0.3);" onclick="clearCart()">
                🗑 Clear Cart
            </button>
        </div>
    </div>

    <!-- RIGHT: Summary -->
    <div class="cart-summary card">
        <h2 style="font-family:var(--font-display);font-size:1.6rem;font-weight:300;margin-bottom:1.5rem;">Order Summary</h2>

        <div class="summary-row">
            <span>Subtotal (<span id="sum-count"><?= array_sum(array_column($cartItems,'quantity')) ?></span> items)</span>
            <span id="sum-subtotal">₹<?= number_format($subtotal,2) ?></span>
        </div>
        <div class="summary-row">
            <span>GST (5%)</span>
            <span id="sum-tax">₹<?= number_format($tax,2) ?></span>
        </div>
        <div class="summary-row">
            <span>Delivery</span>
            <span style="color:var(--accent);">FREE</span>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-row summary-total">
            <span>Grand Total</span>
            <span id="sum-total">₹<?= number_format($total,2) ?></span>
        </div>

        <a href="checkout.php" class="btn btn-primary" style="width:100%;margin-top:1.5rem;justify-content:center;padding:1rem;">
            Proceed to Checkout →
        </a>
        <p style="font-size:0.78rem;color:var(--text-light);text-align:center;margin-top:0.8rem;">
            🔒 Secure checkout · ₹0 delivery fee
        </p>
    </div>
</div>
<?php endif; ?>

</div>
</section>

<style>
.cart-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 2rem;
    align-items: start;
}
.cart-items-col { display:flex; flex-direction:column; gap:1rem; }
.cart-row {
    display: grid;
    grid-template-columns: 64px 1fr auto auto auto;
    align-items: center;
    gap: 1.2rem;
    padding: 1.2rem 1.5rem;
    transition: all 0.3s ease;
}
.cart-row.removing {
    opacity: 0;
    transform: translateX(20px);
}
.cart-thumb {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, rgba(10,53,87,0.8), rgba(27,168,213,0.2));
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
}
.cart-name {
    font-family: var(--font-display);
    font-size: 1.1rem; font-weight: 400;
    margin-bottom: 0.2rem;
}
.cart-unit-price { font-size:0.82rem; color:var(--text-light); }
.cart-qty-wrap {
    display:flex; align-items:center; gap:0;
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--glass-border);
    border-radius: 100px;
    overflow: hidden;
}
.qty-btn {
    background: none; border: none; color: var(--mist);
    width: 34px; height: 34px; cursor: pointer;
    font-size: 1.1rem; font-weight: 500;
    transition: background 0.2s, color 0.2s;
    display: flex; align-items: center; justify-content: center;
}
.qty-btn:hover { background: rgba(92,225,200,0.15); color: var(--accent); }
.qty-val { min-width: 32px; text-align:center; font-weight:500; font-size:0.95rem; }
.cart-line-total {
    font-family: var(--font-display);
    font-size: 1.15rem; font-weight: 400;
    color: var(--foam); white-space: nowrap;
    min-width: 80px; text-align:right;
}
.cart-remove {
    background: none; border: none; color: var(--text-light);
    cursor: pointer; font-size: 1rem; padding: 0.3rem;
    border-radius: 50%; width: 28px; height: 28px;
    display:flex; align-items:center; justify-content:center;
    transition: background 0.2s, color 0.2s;
}
.cart-remove:hover { background: rgba(229,90,90,0.15); color: #e55a5a; }

/* Summary */
.cart-summary { padding: 2rem; position: sticky; top: calc(var(--nav-h) + 1rem); }
.summary-row {
    display:flex; justify-content:space-between;
    font-size:0.9rem; color:var(--mist);
    padding: 0.5rem 0;
}
.summary-divider { border-top:1px solid var(--glass-border); margin:1rem 0; }
.summary-total { font-size:1.1rem; font-weight:600; color:var(--white); }

/* Toast */
#cart-toast {
    position:fixed; bottom:2rem; right:2rem; z-index:9999;
    background: rgba(5,25,45,0.95);
    border:1px solid var(--glass-border);
    backdrop-filter:blur(20px);
    border-radius: var(--radius-md);
    padding: 1rem 1.5rem;
    display:flex; align-items:center; gap:0.75rem;
    font-size:0.9rem;
    transform: translateY(100px); opacity:0;
    transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
    box-shadow: var(--shadow-deep);
    max-width: 320px;
}
#cart-toast.show { transform:translateY(0); opacity:1; }
#cart-toast.toast-ok   { border-color: rgba(92,225,200,0.4); }
#cart-toast.toast-err  { border-color: rgba(229,90,90,0.4); }
#cart-toast .toast-icon { font-size:1.3rem; }

@media(max-width:900px){
    .cart-layout { grid-template-columns:1fr; }
    .cart-summary { position:static; }
}
@media(max-width:560px){
    .cart-row { grid-template-columns:48px 1fr; grid-template-rows:auto auto auto; gap:0.75rem; }
    .cart-line-total,.cart-remove { grid-column:2; }
    .cart-qty-wrap { grid-column:1/-1; justify-self:start; }
}
</style>

<!-- Toast -->
<div id="cart-toast"><span class="toast-icon">✅</span><span id="toast-msg"></span></div>

<script>
// Prices from PHP (for client-side recalc)
const PRICES = {
    <?php foreach($cartItems as $r): ?>
    <?= $r['product_id'] ?>: <?= $r['price'] ?>,
    <?php endforeach; ?>
};
const QTY = {
    <?php foreach($cartItems as $r): ?>
    <?= $r['product_id'] ?>: <?= $r['quantity'] ?>,
    <?php endforeach; ?>
};

function showToast(msg, ok=true) {
    const t = document.getElementById('cart-toast');
    document.getElementById('toast-msg').textContent = msg;
    t.querySelector('.toast-icon').textContent = ok ? '✅' : '❌';
    t.className = 'show ' + (ok ? 'toast-ok' : 'toast-err');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.className = '', 3000);
}

async function apiCall(body) {
    const r = await fetch('cart-api.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify(body)
    });
    return r.json();
}

function updateSummary() {
    let subtotal = 0, count = 0;
    for (const [pid, qty] of Object.entries(QTY)) {
        if (qty > 0) {
            subtotal += PRICES[pid] * qty;
            count += qty;
        }
    }
    const tax   = subtotal * 0.05;
    const total = subtotal + tax;
    document.getElementById('sum-count').textContent    = count;
    document.getElementById('sum-subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('sum-tax').textContent      = '₹' + tax.toFixed(2);
    document.getElementById('sum-total').textContent    = '₹' + total.toFixed(2);

    // Update navbar badge if present
    const badge = document.getElementById('cart-badge');
    if (badge) badge.textContent = count;
}

async function changeQty(pid, delta) {
    const current = QTY[pid] || 1;
    const newQty  = current + delta;
    if (newQty < 1) { removeItem(pid); return; }

    QTY[pid] = newQty;
    document.getElementById('qty-' + pid).textContent = newQty;
    document.getElementById('lt-'  + pid).textContent = '₹' + (PRICES[pid] * newQty).toFixed(2);
    updateSummary();

    const res = await apiCall({action:'update', product_id:pid, quantity:newQty});
    if (!res.ok) showToast(res.msg || 'Error', false);
}

async function removeItem(pid) {
    const row = document.getElementById('cart-row-' + pid);
    if (row) { row.classList.add('removing'); }

    const res = await apiCall({action:'remove', product_id:pid});
    setTimeout(() => {
        if (row) row.remove();
        delete QTY[pid];
        delete PRICES[pid];
        updateSummary();
        if (res.ok) showToast('Item removed');

        // If cart empty, reload
        if (Object.keys(QTY).length === 0) {
            setTimeout(() => location.reload(), 600);
        }
    }, 350);
}

async function clearCart() {
    if (!confirm('Remove all items from cart?')) return;
    const pids = Object.keys(QTY);
    for (const pid of pids) {
        await apiCall({action:'remove', product_id:parseInt(pid)});
        delete QTY[pid];
    }
    location.reload();
}
</script>

<?php include 'includes/footer.php'; ?>