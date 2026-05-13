<?php
// includes/header.php — Common header & navbar
// Usage: include at top of every page AFTER setting $pageTitle and $activePage

if (!isset($pageTitle))  $pageTitle  = 'Nature-Drop | Pure Water Delivered';
if (!isset($activePage)) $activePage = 'home';
?>
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Nature-Drop — Pure mountain spring water delivered to your door. Eco-friendly bottles, home delivery, and subscription plans.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/nature-drop/css/style.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
    .nav-cart-link{position:relative;display:inline-flex;align-items:center;padding:.4rem .6rem;border-radius:var(--radius-sm);font-size:1.2rem;color:var(--mist);transition:color .25s;text-decoration:none;}
    .nav-cart-link:hover{color:var(--accent);}
    .nav-cart-badge{position:absolute;top:-4px;right:-6px;background:var(--aqua);color:var(--deep);font-size:.65rem;font-weight:700;min-width:17px;height:17px;border-radius:50%;display:flex;align-items:center;justify-content:center;line-height:1;}
    </style>
    <link rel="stylesheet" href="css/style.css">
<script>
document.addEventListener("DOMContentLoaded",()=>{
  const badge=document.getElementById("nav-cart-badge");
  if(!badge)return;
  fetch("cart-api.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({action:"count"})})
  .then(r=>r.json()).then(d=>{ if(d.ok&&d.cart_count>0){badge.textContent=d.cart_count;badge.style.display="flex";} }).catch(()=>{});
});
</script>
</head>
<body>

<!-- Ambient bubbles container (JS populates) -->
<div id="bubbles-container" aria-hidden="true"></div>

<!-- ───── NAVBAR ───── -->
<nav class="navbar" id="navbar">
    <div class="nav-inner">

        <!-- Logo -->
        <a href="index.php" class="nav-logo">
            <span class="logo-drop">💧</span>
            <span class="logo-text">Nature<span class="logo-accent">Drop</span></span>
        </a>

        <!-- Nav links -->
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php"          class="nav-link <?= $activePage==='home'    ? 'active':'' ?>"><span data-en="Home"          data-gu="હોમ">Home</span></a></li>
            <li><a href="products.php"       class="nav-link <?= $activePage==='products'? 'active':'' ?>"><span data-en="Products"      data-gu="ઉત્પાદનો">Products</span></a></li>
            <li><a href="supply-method.php"  class="nav-link <?= $activePage==='supply'  ? 'active':'' ?>"><span data-en="Supply"        data-gu="સપ્લાય">Supply</span></a></li>
            <li><a href="about.php"          class="nav-link <?= $activePage==='about'   ? 'active':'' ?>"><span data-en="About"         data-gu="અમારા વિશે">About</span></a></li>
        </ul>

        <!-- Right controls -->
        <div class="nav-right">
            <!-- Language toggle -->
            <button class="lang-btn" id="langToggle" title="Toggle Language">
                <span id="langLabel">EN / ગુ</span>
            </button>

            <?php if (isLoggedIn()): ?>
                <!-- Cart icon with live badge -->
                <a href="cart.php" class="nav-cart-link" id="nav-cart-link" title="Your Cart">
                    🛒 <span class="nav-cart-badge" id="nav-cart-badge" style="display:none;">0</span>
                </a>
                <span class="nav-user">👤 <?= htmlspecialchars(currentUserName()) ?></span>
                <a href="my-orders.php" class="btn btn-outline btn-sm" style="font-size:0.8rem;">📋 Orders</a>
                <a href="logout.php" class="btn btn-outline btn-sm">
                    <span data-en="Logout" data-gu="લૉગ આઉટ">Logout</span>
                </a>
            <?php else: ?>
                <a href="login.php"    class="btn btn-outline btn-sm">
                    <span data-en="Login"    data-gu="લૉગ ઇન">Login</span>
                </a>
                <a href="register.php" class="btn btn-primary btn-sm">
                    <span data-en="Register" data-gu="નોંધણી">Register</span>
                </a>
            <?php endif; ?>

            <!-- Hamburger -->
            <button class="hamburger" id="hamburger" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>
<!-- end NAVBAR -->