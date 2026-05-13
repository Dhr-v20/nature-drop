<?php
// products.php — Full product listing with Add to Cart + Buy Now
require_once 'includes/db.php';

$pageTitle  = 'Products — Nature-Drop';
$activePage = 'products';

$pdo = getDB();
$cat = $_GET['category'] ?? '';
$allowed = ['bottle','dispenser','accessory'];

if ($cat && in_array($cat, $allowed)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category=? ORDER BY price ASC");
    $stmt->execute([$cat]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category, price ASC");
    $cat  = '';
}
$products = $stmt->fetchAll();

include 'includes/header.php';

$emojis = [
    'crystal-500.jpg'      => '💧',
    'premium-1l.jpg'       => '🏔️',
    'family-5l.jpg'        => '🫧',
    'sparkling-330.jpg'    => '✨',
    'dispenser.jpg'        => '🚰',
    'subscription-20l.jpg' => '📦',
];
?>

<div class="page-hero">
    <span class="section-label" data-en="Our Range" data-gu="અમારી શ્રેણી">Our Range</span>
    <h1 class="page-hero-title" data-en="Pure Water Products" data-gu="શુદ્ધ પાણી ઉત્પાદનો">Pure Water Products</h1>
    <p class="page-hero-sub" data-en="Every product is backed by our 172-point quality guarantee." data-gu="દરેક ઉત્પાદ અમારી 172-પૉઇન્ટ ગેરેન્ટી દ્વારા.">Every product is backed by our 172-point quality guarantee.</p>
</div>

<section class="section" style="padding-top:1rem;">
<div class="section-inner">

    <!-- Category filters -->
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:2.5rem;">
        <a href="products.php"                    class="btn <?= !$cat?'btn-primary':'btn-outline' ?> btn-sm">All</a>
        <a href="products.php?category=bottle"    class="btn <?= $cat==='bottle'?'btn-primary':'btn-outline' ?> btn-sm">💧 Bottles</a>
        <a href="products.php?category=dispenser" class="btn <?= $cat==='dispenser'?'btn-primary':'btn-outline' ?> btn-sm">🚰 Dispensers</a>
        <a href="products.php?category=accessory" class="btn <?= $cat==='accessory'?'btn-primary':'btn-outline' ?> btn-sm">📦 Accessories</a>
        <?php if (isLoggedIn()): ?>
        <a href="cart.php" class="btn btn-outline btn-sm" style="margin-left:auto;">🛒 View Cart <span id="hdr-cart-count" class="cart-pill"></span></a>
        <?php endif; ?>
    </div>

    <?php if (empty($products)): ?>
    <div class="alert alert-error">No products found in this category.</div>
    <?php else: ?>

    <div class="products-grid">
    <?php foreach ($products as $p):
        $emoji = $emojis[$p['image']] ?? '💧';
        $inStock = $p['stock'] > 0;
    ?>
    <div class="card product-card" id="pcard-<?= $p['id'] ?>">
        <div class="product-img-wrap"><?= $emoji ?></div>
        <div class="product-body">
            <span class="badge"><?= ucfirst(htmlspecialchars($p['category'])) ?></span>
            <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
            <p class="product-desc"><?= htmlspecialchars($p['description']) ?></p>

            <!-- Qty selector -->
            <?php if (isLoggedIn() && $inStock): ?>
            <div class="prod-qty-row">
                <span style="font-size:0.8rem;color:var(--text-light);">Qty:</span>
                <div class="cart-qty-wrap">
                    <button class="qty-btn" onclick="adjustQty(<?= $p['id'] ?>,-1)">−</button>
                    <span class="qty-val" id="pqty-<?= $p['id'] ?>">1</span>
                    <button class="qty-btn" onclick="adjustQty(<?= $p['id'] ?>,+1)">+</button>
                </div>
            </div>
            <?php endif; ?>

            <div class="product-footer" style="margin-top:0.75rem;">
                <div>
                    <div class="product-price">₹<?= number_format($p['price'],2) ?> <small>/ unit</small></div>
                    <small style="font-size:0.75rem;color:<?= $inStock?'var(--accent)':'#e55a5a' ?>;">
                        <?= $inStock ? '✅ In Stock ('.$p['stock'].' left)' : '❌ Out of Stock' ?>
                    </small>
                </div>

                <?php if (isLoggedIn()): ?>
                    <?php if ($inStock): ?>
                    <div style="display:flex;flex-direction:column;gap:0.4rem;">
                        <button
                            class="btn btn-primary btn-sm"
                            id="atc-btn-<?= $p['id'] ?>"
                            onclick="addToCart(<?= $p['id'] ?>, <?= $p['price'] ?>, '<?= addslashes(htmlspecialchars($p['name'])) ?>')"
                        >🛒 Add to Cart</button>
                        <button
                            class="btn btn-gold btn-sm"
                            onclick="buyNow(<?= $p['id'] ?>, <?= $p['price'] ?>, '<?= addslashes(htmlspecialchars($p['name'])) ?>')"
                        >⚡ Buy Now</button>
                    </div>
                    <?php else: ?>
                    <button class="btn btn-outline btn-sm" disabled style="opacity:0.4;cursor:not-allowed;">Out of Stock</button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline btn-sm">🔐 Login to Buy</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Bulk pricing table -->
    <div style="margin-top:4rem;">
        <div class="section-header">
            <span class="section-label">Bulk Pricing</span>
            <h2 class="section-title">The More You Buy, The More You Save</h2>
        </div>
        <div class="card" style="overflow:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--glass-border);">
                        <th style="padding:1rem 1.5rem;text-align:left;color:var(--accent);font-weight:500;">Order Size</th>
                        <th style="padding:1rem;text-align:center;color:var(--accent);font-weight:500;">100ml</th>
                        <th style="padding:1rem;text-align:center;color:var(--accent);font-weight:500;">300ml Premium</th>
                        <th style="padding:1rem;text-align:center;color:var(--accent);font-weight:500;">500ml Family</th>
                        <th style="padding:1rem 1.5rem;text-align:center;color:var(--accent);font-weight:500;">Discount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $tiers=[['1–11 units','₹9.99','₹19.49','₹69.99','—'],['12–23 units','₹100.85','₹120.20','₹150.50','5%'],['24–47 units','₹160.75','₹180.00','₹200.00','10%'],['48+ units','₹400.59','₹500.79','₹600.50','20%'],['Subscription ∞','₹200.49','₹400.59','₹1500.99','25%']];
                    foreach($tiers as $row): ?>
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                        <td style="padding:0.9rem 1.5rem;font-weight:500;"><?= $row[0] ?></td>
                        <td style="padding:0.9rem;text-align:center;color:var(--mist);"><?= $row[1] ?></td>
                        <td style="padding:0.9rem;text-align:center;color:var(--mist);"><?= $row[2] ?></td>
                        <td style="padding:0.9rem;text-align:center;color:var(--mist);"><?= $row[3] ?></td>
                        <td style="padding:0.9rem 1.5rem;text-align:center;color:var(--accent);font-weight:600;"><?= $row[4] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</section>

<!-- Toast -->
<div id="prod-toast"></div>

<style>
.prod-qty-row {
    display:flex;align-items:center;gap:0.75rem;
    margin:0.75rem 0 0;
}
.cart-qty-wrap{display:flex;align-items:center;gap:0;background:rgba(255,255,255,0.06);border:1px solid var(--glass-border);border-radius:100px;overflow:hidden;}
.qty-btn{background:none;border:none;color:var(--mist);width:30px;height:30px;cursor:pointer;font-size:1rem;font-weight:500;transition:background .2s,color .2s;display:flex;align-items:center;justify-content:center;}
.qty-btn:hover{background:rgba(92,225,200,0.15);color:var(--accent);}
.qty-val{min-width:28px;text-align:center;font-weight:500;font-size:0.88rem;}

.cart-pill{
    display:inline-flex;align-items:center;justify-content:center;
    background:var(--aqua);color:var(--deep);
    font-size:0.7rem;font-weight:700;
    width:18px;height:18px;border-radius:50%;
    margin-left:4px;vertical-align:middle;
}

#prod-toast{
    position:fixed;bottom:2rem;right:2rem;z-index:9999;
    display:flex;flex-direction:column;gap:0.5rem;
    pointer-events:none;
}
.toast-item{
    background:rgba(5,25,45,0.97);
    border:1px solid var(--glass-border);
    backdrop-filter:blur(20px);
    border-radius:var(--radius-md);
    padding:0.9rem 1.3rem;
    font-size:0.88rem;
    transform:translateY(20px);opacity:0;
    transition:all 0.35s cubic-bezier(0.4,0,0.2,1);
    box-shadow:var(--shadow-deep);
    pointer-events:auto;
    max-width:300px;
}
.toast-item.show{transform:translateY(0);opacity:1;}
.toast-item.ok {border-color:rgba(92,225,200,0.45);}
.toast-item.err{border-color:rgba(229,90,90,0.45);}
.toast-item.info{border-color:rgba(212,175,106,0.45);}
</style>

<script>
const QTY = {};

function getQty(pid){
    return parseInt(document.getElementById('pqty-'+pid)?.textContent||'1');
}
function adjustQty(pid,delta){
    const el = document.getElementById('pqty-'+pid);
    if(!el) return;
    let v = parseInt(el.textContent)+delta;
    if(v<1) v=1;
    el.textContent = v;
}

function showToast(msg, type='ok'){
    const container = document.getElementById('prod-toast');
    const t = document.createElement('div');
    t.className = 'toast-item '+type;
    t.textContent = msg;
    container.appendChild(t);
    requestAnimationFrame(()=>{ requestAnimationFrame(()=>{ t.classList.add('show'); }); });
    setTimeout(()=>{
        t.classList.remove('show');
        setTimeout(()=>t.remove(), 400);
    }, 3000);
}

function updateCartBadge(count){
    document.querySelectorAll('#hdr-cart-count, #nav-cart-badge').forEach(el=>{
        if(el){ el.textContent = count; el.style.display = count>0?'inline-flex':'none'; }
    });
}

async function addToCart(pid, price, name) {
    const qty = getQty(pid);
    const btn = document.getElementById('atc-btn-'+pid);
    if(btn){ btn.disabled=true; btn.textContent='Adding…'; }

    try {
        const res = await fetch('cart-api.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({action:'add', product_id:pid, quantity:qty})
        });
        const data = await res.json();

        if(data.ok){
            showToast('🛒 '+data.msg, 'ok');
            updateCartBadge(data.cart_count);
            if(btn){
                btn.textContent='✅ Added!';
                setTimeout(()=>{ btn.disabled=false; btn.textContent='🛒 Add to Cart'; }, 1800);
            }
        } else if(data.redirect){
            window.location.href = data.redirect;
        } else {
            showToast('❌ '+(data.msg||'Error'), 'err');
            if(btn){ btn.disabled=false; btn.textContent='🛒 Add to Cart'; }
        }
    } catch(e){
        showToast('❌ Network error', 'err');
        if(btn){ btn.disabled=false; btn.textContent='🛒 Add to Cart'; }
    }
}

async function buyNow(pid, price, name){
    const qty = getQty(pid);
    showToast('⚡ Adding to cart & redirecting…', 'info');

    try {
        const res = await fetch('cart-api.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({action:'add', product_id:pid, quantity:qty})
        });
        const data = await res.json();
        if(data.ok || !data.redirect){
            setTimeout(()=>{ window.location.href='checkout.php'; }, 600);
        } else {
            window.location.href = data.redirect;
        }
    } catch(e){
        window.location.href = 'cart.php';
    }
}

// Load cart count on page load (if logged in)
<?php if (isLoggedIn()): ?>
(async()=>{
    try{
        const res = await fetch('cart-api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'count'})});
        const d   = await res.json();
        if(d.ok) updateCartBadge(d.cart_count);
    }catch(e){}
})();
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>