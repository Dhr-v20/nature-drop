<?php
// supply-method.php — Supply options
require_once 'includes/db.php';

$pageTitle  = 'Supply Methods — Nature-Drop';
$activePage = 'supply';

include 'includes/header.php';
?>

<div class="page-hero">
    <span class="section-label" data-en="Delivery & Supply" data-gu="ડિલિવરી અને સપ્લાય">Delivery & Supply</span>
    <h1 class="page-hero-title" data-en="How Your Water<br>Reaches You" data-gu="તમારું પાણી<br>કેવી રીતે પહોંચે છે">How Your Water<br>Reaches You</h1>
    <p class="page-hero-sub" data-en="Choose the supply method that fits your lifestyle. Switch anytime." data-gu="તમારી જીવનશૈલીને અનુરૂપ સપ્લાય પદ્ધતિ પસંદ કરો. ગમે ત્યારે બદલો.">Choose the supply method that fits your lifestyle. Switch anytime.</p>
</div>

<section class="section">
    <div class="section-inner">

        <!-- Three supply methods -->
        <div class="supply-grid">

            <!-- Home Delivery -->
            <div class="card supply-card">
                <span class="supply-icon">🚚</span>
                <h2 class="supply-title" data-en="Home Delivery" data-gu="ઘરે ડિલિવરી">Home Delivery</h2>
                <p class="supply-desc" data-en="We bring Nature-Drop directly to your home. Order online and choose your time slot. Same-day delivery available before noon." data-gu="અમે Nature-Drop સીધા તમારા ઘરે લાવીએ છીએ. ઓનલાઈન ઑર્ડર આપો અને ટાઈમ સ્લૉટ પસંદ કરો.">
                    We bring Nature-Drop directly to your home. Order online and choose your time slot. Same-day delivery available before noon.
                </p>
                <div class="supply-steps">
                    <?php
                    $steps = [
                        ['Select products', 'Browse our range and add to cart.'],
                        ['Choose slot',     'Pick morning (8–12) or evening (4–8) delivery.'],
                        ['We deliver',      'Our eco-driver arrives at your door.'],
                        ['Return empties',  'Hand back empty bottles and earn ₹5 credit each.'],
                    ];
                    foreach ($steps as $i => $s): ?>
                    <div class="supply-step">
                        <div class="step-num"><?= $i+1 ?></div>
                        <div class="step-text"><strong><?= $s[0] ?></strong> — <?= $s[1] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="products.php" class="btn btn-primary" style="margin-top:1.5rem;">
                    <span data-en="Order Now" data-gu="હવે ઑર્ડર કરો">Order Now</span>
                </a>
            </div>

            <!-- Store Pickup -->
            <div class="card supply-card">
                <span class="supply-icon">🏪</span>
                <h2 class="supply-title" data-en="Store Pickup" data-gu="સ્ટોર પિકઅપ">Store Pickup</h2>
                <p class="supply-desc" data-en="Reserve online and collect from our nearest pickup point at your convenience. No delivery fees, instant availability." data-gu="ઓનલાઈન રિઝર્વ કરો અને અમારા નજીકના પિકઅપ પૉઈન્ટ પરથી ઉઠાવો. ડિલિવરી ફી નહીં.">
                    Reserve online and collect from our nearest pickup point at your convenience. No delivery fees, instant availability.
                </p>
                <div class="supply-steps">
                    <?php
                    $steps2 = [
                        ['Reserve online',     'Place your order and choose "Store Pickup".'],
                        ['Get confirmation',   'Receive an SMS with your order code.'],
                        ['Visit the store',    'Go to any NatureDrop point in your city.'],
                        ['Collect & go',       'Show your code, collect your bottles, done!'],
                    ];
                    foreach ($steps2 as $i => $s): ?>
                    <div class="supply-step">
                        <div class="step-num"><?= $i+1 ?></div>
                        <div class="step-text"><strong><?= $s[0] ?></strong> — <?= $s[1] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="products.php" class="btn btn-outline" style="margin-top:1.5rem;">
                    <span data-en="Find a Store" data-gu="સ્ટોર શોધો">Find a Store</span>
                </a>
            </div>

            <!-- Subscription -->
            <div class="card supply-card" style="border-color:rgba(92,225,200,0.4);">
                <span class="supply-icon">🔄</span>
                <h2 class="supply-title" data-en="Monthly Subscription" data-gu="માસિક સબ્સ્ક્રિપ્શન">Monthly Subscription</h2>
                <p class="supply-desc" data-en="Set it and forget it. We auto-refill your supply every month and pick up your empties. Save up to 25% vs single orders." data-gu="સેટ કરો અને ભૂલો. અમે દર મહિને તમારી સપ્લાય ઓટો-રિફિલ કરીએ છીએ. 25% સુધી બચત.">
                    Set it and forget it. We auto-refill your supply every month and pick up your empties. Save up to 25% vs single orders.
                </p>
                <div class="supply-steps">
                    <?php
                    $steps3 = [
                        ['Pick a plan',     'Choose 10L, 20L or 50L monthly volume.'],
                        ['We auto-deliver', 'Arrives on your chosen date every month.'],
                        ['Easy management', 'Pause, skip or cancel from your dashboard.'],
                        ['Eco-exchange',    'Empties collected free with every delivery.'],
                    ];
                    foreach ($steps3 as $i => $s): ?>
                    <div class="supply-step">
                        <div class="step-num"><?= $i+1 ?></div>
                        <div class="step-text"><strong><?= $s[0] ?></strong> — <?= $s[1] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="register.php" class="btn btn-primary" style="margin-top:1.5rem;">
                    <span data-en="Subscribe — Save 25%" data-gu="સબ્સ્ક્રાઇબ — 25% બચત">Subscribe — Save 25%</span>
                </a>
            </div>
        </div>

        <!-- Coverage map placeholder -->
        <div style="margin-top:4rem;">
            <div class="section-header">
                <span class="section-label" data-en="Coverage" data-gu="કવરેજ">Coverage</span>
                <h2 class="section-title" data-en="Service Areas" data-gu="સેવા વિસ્તારો">Service Areas</h2>
            </div>
            <div class="card" style="padding:3rem;text-align:center;">
                <p style="font-size:4rem;margin-bottom:1rem;">📍</p>
                <p style="color:var(--text-light);max-width:500px;margin:0 auto 1.5rem;" data-en="Currently serving Siddhapur, Patan, Mehsana, Unjha, and surrounding areas. Expanding to Ahmedabad by Q3 2026." data-gu="હાલ સિદ્ધપુર, પાટણ, મહેસાણા, ઉંઝા અને આસપાસના વિસ્તારોની સેવા. Q3 2026 સુધીમાં અમદાવાદ.">
                    Currently serving Siddhapur, Patan, Mehsana, Unjha, and surrounding areas. Expanding to Ahmedabad by Q3 2026.
                </p>
                <a href="about.php#contact" class="btn btn-outline" data-en="Check Your Area" data-gu="તમારો વિસ્તાર ચેક કરો">Check Your Area</a>
            </div>
        </div>

        <!-- FAQ -->
        <div style="margin-top:4rem;">
            <div class="section-header">
                <span class="section-label">FAQ</span>
                <h2 class="section-title" data-en="Common Questions" data-gu="સામાન્ય પ્રશ્નો">Common Questions</h2>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem;max-width:700px;margin:0 auto;">
                <?php
                $faqs = [
                    ['Can I change my delivery date?',     'Yes, you can reschedule up to 24 hours before delivery from your account dashboard.'],
                    ['What if I miss a delivery?',         'We will attempt re-delivery the next day at no charge. Alternatively you can pick up from the nearest store.'],
                    ['Are the bottles sanitised?',         'Yes. Every refillable bottle goes through an ISO-certified 5-stage sterilisation process between uses.'],
                    ['How do I pause my subscription?',    'Log in → My Subscription → Pause. No questions asked, no fees charged.'],
                ];
                foreach ($faqs as $faq): ?>
                <div class="card" style="padding:1.5rem;" onclick="this.querySelector('.faq-ans').style.display = this.querySelector('.faq-ans').style.display==='none'?'block':'none'" style="cursor:pointer;">
                    <div style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;">
                        <strong><?= htmlspecialchars($faq[0]) ?></strong>
                        <span style="color:var(--accent);">+</span>
                    </div>
                    <p class="faq-ans" style="display:none;color:var(--text-light);font-size:0.9rem;margin-top:0.8rem;line-height:1.6;">
                        <?= htmlspecialchars($faq[1]) ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>