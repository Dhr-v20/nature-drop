<?php
// invoice.php — Printable invoice / order confirmation
require_once 'includes/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$uid     = currentUserId();
$invNum  = trim($_GET['order'] ?? '');

if (!$invNum) {
    header('Location: index.php');
    exit;
}

$pdo = getDB();

// Fetch order (must belong to current user)
$stmt = $pdo->prepare("SELECT * FROM orders WHERE invoice_number=? AND user_id=?");
$stmt->execute([$invNum, $uid]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Fetch items
$items = $pdo->prepare("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
$items->execute([$order['id']]);
$items = $items->fetchAll();

// Fetch user
$user = $pdo->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$uid]);
$user = $user->fetch();

$emojis = [
    'crystal-500.jpg'      => '💧',
    'premium-1l.jpg'       => '🏔️',
    'family-5l.jpg'        => '🫧',
    'sparkling-330.jpg'    => '✨',
    'dispenser.jpg'        => '🚰',
    'subscription-20l.jpg' => '📦',
];

$statusColor = [
    'pending'    => '#d4af6a',
    'processing' => '#1ba8d5',
    'shipped'    => '#5ce1c8',
    'delivered'  => '#4ade80',
    'cancelled'  => '#e55a5a',
];
$sc = $statusColor[$order['status']] ?? '#7ab4cc';

$pageTitle  = 'Invoice ' . htmlspecialchars($invNum) . ' — Nature-Drop';
$activePage = '';
include 'includes/header.php';
?>

<!-- Print styles -->
<style>
@media print {
    .navbar, .footer, .no-print, #bubbles-container { display:none !important; }
    body { background:#fff !important; color:#000 !important; }
    .invoice-wrap { max-width:100% !important; box-shadow:none !important; border:none !important; }
    .inv-header { background:#05192d !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
}

.invoice-wrap {
    max-width: 860px;
    margin: 2rem auto 4rem;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    backdrop-filter: blur(12px);
    box-shadow: var(--shadow-deep);
}

/* Header band */
.inv-header {
    background: linear-gradient(135deg, var(--deep), var(--ocean));
    padding: 2.5rem 3rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 2px solid var(--aqua);
}
.inv-brand { font-family:var(--font-display); }
.inv-brand-name { font-size: 2rem; font-weight:300; }
.inv-brand-name span { color:var(--accent); }
.inv-brand-tag { font-size:0.8rem; color:var(--text-light); margin-top:0.25rem; letter-spacing:0.1em; }
.inv-meta { text-align:right; }
.inv-number { font-family:var(--font-display); font-size:1.4rem; color:var(--aqua); }
.inv-date { font-size:0.82rem; color:var(--text-light); margin-top:0.25rem; }
.inv-status {
    display:inline-block; margin-top:0.6rem;
    padding:0.25rem 0.8rem; border-radius:100px;
    font-size:0.75rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase;
    background:rgba(255,255,255,0.08); border:1px solid;
}

/* Body */
.inv-body { padding: 2.5rem 3rem; }

.inv-parties {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--glass-border);
}
.inv-party-label {
    font-size:0.72rem; letter-spacing:0.15em; text-transform:uppercase;
    color:var(--accent); margin-bottom:0.6rem; font-weight:500;
}
.inv-party-name { font-size:1.05rem; font-weight:500; margin-bottom:0.25rem; }
.inv-party-detail { font-size:0.85rem; color:var(--text-light); line-height:1.7; }

/* Items table */
.inv-table { width:100%; border-collapse:collapse; margin-bottom:2rem; }
.inv-table th {
    font-size:0.75rem; letter-spacing:0.12em; text-transform:uppercase;
    color:var(--accent); font-weight:500; padding:0.75rem 1rem;
    border-bottom:1px solid var(--glass-border); text-align:left;
}
.inv-table th:last-child, .inv-table td:last-child { text-align:right; }
.inv-table td {
    padding: 1rem; font-size:0.9rem; color:var(--mist);
    border-bottom: 1px solid rgba(255,255,255,0.04);
    vertical-align: middle;
}
.inv-table tr:last-child td { border-bottom:none; }
.inv-table .item-name { color:var(--foam); font-weight:500; display:flex; align-items:center; gap:0.6rem; }
.inv-table .item-emoji { font-size:1.4rem; }

/* Totals */
.inv-totals { display:flex; justify-content:flex-end; margin-bottom:2rem; }
.inv-totals-box { min-width:280px; }
.inv-total-row {
    display:flex; justify-content:space-between;
    font-size:0.88rem; color:var(--mist); padding:0.45rem 0;
    border-bottom:1px solid rgba(255,255,255,0.04);
}
.inv-total-row:last-child { border:none; }
.inv-grand {
    display:flex; justify-content:space-between;
    padding:1rem 0 0; margin-top:0.5rem;
    border-top:2px solid var(--aqua);
    font-size:1.1rem; font-weight:600; color:var(--white);
}
.inv-grand span:last-child { color:var(--accent); }

/* Footer note */
.inv-footer {
    background: rgba(255,255,255,0.03);
    border-top:1px solid var(--glass-border);
    padding:1.5rem 3rem;
    display:flex; justify-content:space-between; align-items:center;
    font-size:0.8rem; color:var(--text-light);
}
.inv-qr { text-align:right; }
.inv-qr span { font-size:0.75rem; color:var(--text-light); display:block; }

/* Action bar */
.inv-actions {
    max-width:860px; margin: 0 auto 2rem;
    display:flex; gap:1rem; flex-wrap:wrap;
    padding: 0 0;
}

@media(max-width:600px){
    .inv-header,.inv-body,.inv-footer{ padding:1.5rem; }
    .inv-parties{ grid-template-columns:1fr; gap:1.5rem; }
    .inv-actions{ flex-direction:column; }
    .inv-header{ flex-direction:column; gap:1rem; }
    .inv-meta{ text-align:left; }
}
</style>

<section style="padding:2rem 1.5rem 0;position:relative;z-index:1;">
<!-- Action bar -->
<div class="inv-actions no-print">
    <a href="index.php" class="btn btn-outline">← Back to Home</a>
    <a href="products.php" class="btn btn-outline">🛍 Shop More</a>
    <button onclick="window.print()" class="btn btn-primary">🖨 Print Invoice</button>
    <a href="invoice.php?order=<?= urlencode($invNum) ?>&download=1" class="btn btn-gold">⬇ Download PDF</a>
</div>

<!-- Invoice card -->
<div class="invoice-wrap">

    <!-- Header -->
    <div class="inv-header">
        <div class="inv-brand">
            <div class="inv-brand-name">💧 Nature<span>Drop</span></div>
            <div class="inv-brand-tag">Pure Mountain Spring Water</div>
            <div class="inv-brand-tag" style="margin-top:0.5rem;">
                NatureDrop HQ, Siddhapur, Gujarat – 384151<br>
                hello@naturedrop.com · +91 98765 43210<br>
                GSTIN: 24XXXXX1234X1ZX
            </div>
        </div>
        <div class="inv-meta">
            <div style="font-size:0.8rem;color:var(--text-light);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:0.4rem;">Tax Invoice</div>
            <div class="inv-number"><?= htmlspecialchars($invNum) ?></div>
            <div class="inv-date">Date: <?= date('d M Y', strtotime($order['created_at'])) ?></div>
            <div class="inv-status" style="color:<?= $sc ?>;border-color:<?= $sc ?>;">
                <?= ucfirst($order['status']) ?>
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="inv-body">

        <!-- Parties -->
        <div class="inv-parties">
            <div>
                <div class="inv-party-label">Bill To</div>
                <div class="inv-party-name"><?= htmlspecialchars($user['name']) ?></div>
                <div class="inv-party-detail">
                    <?= htmlspecialchars($user['email']) ?><br>
                    <?php if ($user['phone']): ?><?= htmlspecialchars($user['phone']) ?><br><?php endif; ?>
                    <?= nl2br(htmlspecialchars($order['delivery_address'])) ?>
                </div>
            </div>
            <div>
                <div class="inv-party-label">Supply Details</div>
                <div class="inv-party-name">
                    <?php
                    $methods = ['home_delivery'=>'🚚 Home Delivery','store_pickup'=>'🏪 Store Pickup','subscription'=>'🔄 Subscription'];
                    echo $methods[$order['supply_method']] ?? ucfirst($order['supply_method']);
                    ?>
                </div>
                <div class="inv-party-detail">
                    Order ID: #<?= $order['id'] ?><br>
                    Payment: Cash on Delivery<br>
                    <?php if ($order['notes']): ?>Note: <?= htmlspecialchars($order['notes']) ?><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Items -->
        <table class="inv-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Unit Price</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): $emoji = $emojis[$item['image'] ?? ''] ?? '💧'; ?>
                <tr>
                    <td style="color:var(--text-light);"><?= $i+1 ?></td>
                    <td>
                        <span class="item-name">
                            <span class="item-emoji"><?= $emoji ?></span>
                            <?= htmlspecialchars($item['product_name']) ?>
                        </span>
                    </td>
                    <td style="text-align:center;color:var(--accent);font-weight:600;"><?= $item['quantity'] ?></td>
                    <td style="text-align:right;">₹<?= number_format($item['unit_price'],2) ?></td>
                    <td style="text-align:right;color:var(--foam);font-weight:500;">₹<?= number_format($item['subtotal'],2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="inv-totals">
            <div class="inv-totals-box">
                <div class="inv-total-row"><span>Subtotal</span><span>₹<?= number_format($order['total_amount'],2) ?></span></div>
                <div class="inv-total-row"><span>GST (5%)</span><span>₹<?= number_format($order['tax_amount'],2) ?></span></div>
                <div class="inv-total-row"><span>Discount</span><span style="color:var(--accent);">- ₹<?= number_format($order['discount_amount'],2) ?></span></div>
                <div class="inv-total-row"><span>Delivery</span><span style="color:var(--accent);">FREE</span></div>
                <div class="inv-grand">
                    <span>Grand Total</span>
                    <span>₹<?= number_format($order['grand_total'],2) ?></span>
                </div>
            </div>
        </div>

        <!-- Thank you -->
        <div style="text-align:center;padding:1.5rem;background:rgba(92,225,200,0.05);border-radius:var(--radius-md);border:1px solid rgba(92,225,200,0.15);">
            <p style="font-family:var(--font-display);font-size:1.4rem;font-weight:300;color:var(--aqua);margin-bottom:0.4rem;">Thank you for choosing NatureDrop!</p>
            <p style="font-size:0.85rem;color:var(--text-light);">Drink pure · Stay healthy · Save the planet 🌿</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="inv-footer">
        <div>
            <strong style="color:var(--mist);">Terms & Conditions</strong><br>
            Goods once sold are not returnable unless defective. For queries call +91 98765 43210.<br>
            This is a computer-generated invoice. No signature required.
        </div>
        <div class="inv-qr">
            <div style="font-size:3rem;">📋</div>
            <span>Invoice verified</span>
            <span><?= htmlspecialchars($invNum) ?></span>
        </div>
    </div>

</div><!-- /invoice-wrap -->

</section>

<?php include 'includes/footer.php'; ?>