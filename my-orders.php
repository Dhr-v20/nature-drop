<?php
// my-orders.php — List of all user invoices / orders
require_once 'includes/db.php';

if (!isLoggedIn()) {
    header('Location: login.php?next=my-orders.php');
    exit;
}

$uid = currentUserId();
$pdo = getDB();

$orders = $pdo->prepare(
    "SELECT o.*, COUNT(oi.id) AS item_count
     FROM orders o
     LEFT JOIN order_items oi ON oi.order_id = o.id
     WHERE o.user_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC"
);
$orders->execute([$uid]);
$orders = $orders->fetchAll();

$pageTitle  = 'My Orders — Nature-Drop';
$activePage = '';
include 'includes/header.php';

$statusColor = [
    'pending'    => ['bg'=>'rgba(212,175,106,0.12)','border'=>'rgba(212,175,106,0.4)','text'=>'#d4af6a'],
    'processing' => ['bg'=>'rgba(27,168,213,0.12)', 'border'=>'rgba(27,168,213,0.4)', 'text'=>'#1ba8d5'],
    'shipped'    => ['bg'=>'rgba(92,225,200,0.12)', 'border'=>'rgba(92,225,200,0.4)', 'text'=>'#5ce1c8'],
    'delivered'  => ['bg'=>'rgba(74,222,128,0.12)', 'border'=>'rgba(74,222,128,0.4)', 'text'=>'#4ade80'],
    'cancelled'  => ['bg'=>'rgba(229,90,90,0.12)',  'border'=>'rgba(229,90,90,0.4)',  'text'=>'#e55a5a'],
];
$methodIcon = ['home_delivery'=>'🚚','store_pickup'=>'🏪','subscription'=>'🔄'];
?>

<div class="page-hero" style="padding-bottom:1rem;">
    <span class="section-label">📋 Order History</span>
    <h1 class="page-hero-title" data-en="My Orders & Invoices" data-gu="મારા ઑર્ડર અને ઇન્વૉઇસ">My Orders & Invoices</h1>
    <p class="page-hero-sub">Track your orders and download invoices.</p>
</div>

<section class="section" style="padding-top:1rem;">
<div class="section-inner" style="max-width:920px;">

<?php if (empty($orders)): ?>
<div class="card" style="text-align:center;padding:5rem 2rem;">
    <p style="font-size:4rem;margin-bottom:1rem;">📦</p>
    <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:300;margin-bottom:0.75rem;">No orders yet</h2>
    <p style="color:var(--text-light);margin-bottom:2rem;">Your order history will appear here.</p>
    <a href="products.php" class="btn btn-primary btn-lg">Start Shopping</a>
</div>

<?php else: ?>

<!-- Summary strip -->
<div class="stats-row" style="margin-bottom:2rem;">
    <?php
    $total_orders  = count($orders);
    $total_spent   = array_sum(array_column($orders,'grand_total'));
    $delivered_cnt = count(array_filter($orders, fn($o)=>$o['status']==='delivered'));
    $pending_cnt   = count(array_filter($orders, fn($o)=>$o['status']==='pending'||$o['status']==='processing'));
    ?>
    <div class="card stat-card"><span class="stat-num"><?= $total_orders ?></span><span class="stat-label">Total Orders</span></div>
    <div class="card stat-card"><span class="stat-num">₹<?= number_format($total_spent,0) ?></span><span class="stat-label">Total Spent</span></div>
    <div class="card stat-card"><span class="stat-num" style="color:#4ade80;"><?= $delivered_cnt ?></span><span class="stat-label">Delivered</span></div>
    <div class="card stat-card"><span class="stat-num" style="color:#d4af6a;"><?= $pending_cnt ?></span><span class="stat-label">In Progress</span></div>
</div>

<!-- Orders list -->
<div style="display:flex;flex-direction:column;gap:1rem;">
<?php foreach ($orders as $order):
    $sc = $statusColor[$order['status']] ?? $statusColor['pending'];
    $mi = $methodIcon[$order['supply_method']] ?? '📦';
?>
<div class="card order-row">
    <div class="order-row-top">
        <!-- Invoice # and date -->
        <div class="order-id-col">
            <span class="order-inv-num"><?= htmlspecialchars($order['invoice_number']) ?></span>
            <span class="order-date">🗓 <?= date('d M Y, g:i A', strtotime($order['created_at'])) ?></span>
        </div>

        <!-- Status badge -->
        <span class="order-status-badge" style="background:<?= $sc['bg'] ?>;border-color:<?= $sc['border'] ?>;color:<?= $sc['text'] ?>;">
            <?= ucfirst($order['status']) ?>
        </span>
    </div>

    <div class="order-row-body">
        <div class="order-meta-col">
            <span class="order-meta-item"><?= $mi ?> <?= ucwords(str_replace('_',' ',$order['supply_method'])) ?></span>
            <span class="order-meta-item">🛍 <?= $order['item_count'] ?> item<?= $order['item_count']!=1?'s':'' ?></span>
        </div>

        <div class="order-total-col">
            <span class="order-grand">₹<?= number_format($order['grand_total'],2) ?></span>
            <span class="order-tax-note">incl. 5% GST</span>
        </div>

        <div class="order-actions-col">
            <a href="invoice.php?order=<?= urlencode($order['invoice_number']) ?>" class="btn btn-outline btn-sm">
                📄 View Invoice
            </a>
            <button onclick="window.open('invoice.php?order=<?= urlencode($order['invoice_number']) ?>','_blank')" class="btn btn-sm" style="background:rgba(212,175,106,0.15);border:1px solid rgba(212,175,106,0.35);color:#d4af6a;">
                🖨 Print
            </button>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<div style="margin-top:2rem;display:flex;gap:1rem;flex-wrap:wrap;">
    <a href="products.php" class="btn btn-outline">🛍 Continue Shopping</a>
    <a href="cart.php"     class="btn btn-primary">🛒 Go to Cart</a>
</div>

</div>
</section>

<style>
.order-row { padding:1.5rem 2rem; }
.order-row-top {
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:1rem; padding-bottom:1rem;
    border-bottom:1px solid var(--glass-border);
}
.order-inv-num {
    font-family:var(--font-display); font-size:1.1rem; font-weight:400;
    color:var(--aqua); display:block; margin-bottom:0.2rem;
}
.order-date { font-size:0.8rem; color:var(--text-light); }
.order-status-badge {
    display:inline-block; padding:0.3rem 0.9rem;
    border-radius:100px; border:1px solid;
    font-size:0.75rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase;
}

.order-row-body {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:1rem;
}
.order-meta-col { display:flex; flex-direction:column; gap:0.3rem; }
.order-meta-item { font-size:0.85rem; color:var(--mist); }
.order-total-col { text-align:right; }
.order-grand { font-family:var(--font-display); font-size:1.4rem; font-weight:300; color:var(--accent); display:block; }
.order-tax-note { font-size:0.75rem; color:var(--text-light); }
.order-actions-col { display:flex; gap:0.5rem; flex-wrap:wrap; }

@media(max-width:600px){
    .order-row-body { flex-direction:column; align-items:flex-start; }
    .order-total-col { text-align:left; }
}
</style>

<?php include 'includes/footer.php'; ?>