<?php
// index.php — Home Page (5 sections)
require_once 'includes/db.php';

$pageTitle  = 'Nature-Drop | Pure Mountain Spring Water';
$activePage = 'home';

// Fetch featured products
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM products LIMIT 3");
$featured = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- ══════════════════════════════════════════
     SECTION 1 — HERO
════════════════════════════════════════════ -->
<section class="hero">
    <div class="hero-bg"></div>

    <div class="hero-content">
        <span class="hero-eyebrow" data-en="🌿 100% Natural · Zero Additives" data-gu="🌿 100% કુદરતી · શૂન્ય ઉમેરો">🌿 100% Natural · Zero Additives</span>

        <h1 class="hero-title">
            <span data-en="Drink the Purest Water on Earth" data-gu="પૃથ્વી પર સૌથી શુદ્ધ પાણી પીઓ">Drink the Purest Water on Earth</span>
        </h1>

        <p class="hero-subtitle" data-en="Sourced from deep Himalayan aquifers, bottled at the spring. Delivered to your doorstep in eco-friendly bottles." data-gu="ઊંડા હિમાલયન જળ-સ્ત્રોતોમાંથી, ઝરણ પર જ બોટલ કરવામાં આવ્યું. ઇકો-ફ્રેન્ડ્લી બોટલ્સમાં તમારા દ્વારે ડિલિવર.">
            Sourced from deep Himalayan aquifers, bottled at the spring. Delivered to your doorstep in eco-friendly bottles.
        </p>

        <div class="hero-cta">
            <a href="products.php" class="btn btn-primary btn-lg">
                <span data-en="Shop Now" data-gu="હવે ખરીદો">Shop Now</span>
            </a>
            <a href="supply-method.php" class="btn btn-outline btn-lg">
                <span data-en="How It Works" data-gu="કેવી રીતે કામ કરે છે">How It Works</span>
            </a>
        </div>
    </div>

    <!-- Decorative water drop -->
    <div class="hero-visual" aria-hidden="true">
        <svg class="water-drop-svg" viewBox="0 0 300 380" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="dropGrad" cx="40%" cy="35%" r="65%">
                    <stop offset="0%"   stop-color="#5ce1c8" stop-opacity="0.5"/>
                    <stop offset="50%"  stop-color="#1ba8d5" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#0a3557" stop-opacity="0.2"/>
                </radialGradient>
                <filter id="blur2">
                    <feGaussianBlur stdDeviation="2"/>
                </filter>
            </defs>
            <!-- Main drop -->
            <path d="M150,20 C150,20 40,160 40,230 C40,300 90,360 150,360 C210,360 260,300 260,230 C260,160 150,20 150,20 Z"
                  fill="url(#dropGrad)" stroke="rgba(92,225,200,0.4)" stroke-width="1.5"/>
            <!-- Shine -->
            <ellipse cx="118" cy="160" rx="18" ry="30" fill="rgba(255,255,255,0.18)" transform="rotate(-20 118 160)"/>
            <!-- Inner bubbles -->
            <circle cx="170" cy="270" r="8"  fill="rgba(92,225,200,0.2)" stroke="rgba(92,225,200,0.4)" stroke-width="1"/>
            <circle cx="120" cy="300" r="5"  fill="rgba(92,225,200,0.15)" stroke="rgba(92,225,200,0.3)" stroke-width="1"/>
            <circle cx="190" cy="230" r="4"  fill="rgba(255,255,255,0.15)"/>
        </svg>
    </div>
</section>

<!-- ══════════════════════════════════════════
     SECTION 2 — FEATURES / WHY US
════════════════════════════════════════════ -->
<section class="section">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label" data-en="Why Nature-Drop" data-gu="શા માટે Nature-Drop">Why Nature-Drop</span>
            <h2 class="section-title" data-en="Water Worth Drinking" data-gu="પીવા યોગ્ય પાણી">Water Worth Drinking</h2>
            <p class="section-sub" data-en="Every bottle carries our promise — pure, sustainable, and delivered with care." data-gu="દરેક બોટલ અમારું વચન ધરાવે છે — શુદ્ધ, ટકાઉ અને કાળજીથી ડિલિવર.">Every bottle carries our promise — pure, sustainable, and delivered with care.</p>
        </div>

        <div class="features-grid">
            <?php
            $features = [
                ['🏔️', 'Mountain Sourced',    'Himalayan spring water glacier-fed at 3,800m altitude.',           'પર્વત-ઉત્પ્ત'],
                ['🧪', 'Lab Tested',           '172-point quality check before every batch leaves our facility.',  'લેબ-પ્રમાણિત'],
                ['♻️', 'Eco Packaging',        'Bottles made from 100% recycled or biodegradable materials.',       'ઇકો-પેકેજ'],
                ['🚚', 'Same-Day Delivery',    'Order before noon and receive by evening in select areas.',         'ત્વરિત ડિલિવરી'],
                ['💧', 'Zero Plastic Waste',   'Refill programme: return empties, earn ₹5 credit per bottle.',      'ઝીરો પ્લાસ્ટિક'],
                ['📦', 'Subscription Plans',   'Monthly doorstep delivery. Pause or cancel anytime.',               'સબ્સ્ક્રિપ્શન'],
            ];
            foreach ($features as $f): ?>
            <div class="card feature-card">
                <span class="feature-icon"><?= $f[0] ?></span>
                <h3 class="feature-title" data-en="<?= htmlspecialchars($f[1]) ?>"><?= htmlspecialchars($f[1]) ?></h3>
                <p class="feature-desc"><?= htmlspecialchars($f[2]) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     SECTION 3 — FEATURED PRODUCTS
════════════════════════════════════════════ -->
<section class="section" style="background: rgba(10,53,87,0.15);">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label" data-en="Best Sellers" data-gu="બેસ્ટ સેલ્સ">Best Sellers</span>
            <h2 class="section-title" data-en="Our Products" data-gu="અમારા ઉત્પાદનો">Our Products</h2>
        </div>

        <div class="products-grid">
            <?php
            $emojis = ['💧','🏔️','🌊','✨','🫧','📦'];
            foreach ($featured as $i => $p): ?>
            <div class="card product-card">
                <div class="product-img-wrap" style="font-size:5rem;">
                    <?= $emojis[$i % count($emojis)] ?>
                </div>
                <div class="product-body">
                    <span class="badge"><?= ucfirst(htmlspecialchars($p['category'])) ?></span>
                    <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
                    <p class="product-desc"><?= htmlspecialchars(substr($p['description'], 0, 80)) ?>…</p>
                    <div class="product-footer">
                        <div class="product-price">₹<?= number_format($p['price'], 2) ?> <small>/ unit</small></div>
                        <a href="products.php" class="btn btn-primary btn-sm">
                            <span data-en="View" data-gu="જુઓ">View</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:2.5rem;">
            <a href="products.php" class="btn btn-outline btn-lg" data-en="See All Products" data-gu="બધા ઉત્પાદનો જુઓ">See All Products</a>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     SECTION 4 — STATS
════════════════════════════════════════════ -->
<section class="section">
    <div class="section-inner">
        <div class="stats-row">
            <div class="card stat-card">
                <span class="stat-num" data-count="50000" data-suffix="+">0</span>
                <span class="stat-label" data-en="Happy Customers" data-gu="ખુશ ગ્રાહકો">Happy Customers</span>
            </div>
            <div class="card stat-card">
                <span class="stat-num" data-count="12" data-suffix=" yrs">0</span>
                <span class="stat-label" data-en="Years of Purity" data-gu="શુદ્ધતાના વર્ષ">Years of Purity</span>
            </div>
            <div class="card stat-card">
                <span class="stat-num" data-count="172" data-suffix="">0</span>
                <span class="stat-label" data-en="Quality Checks" data-gu="ગુણવત્તા ચેક">Quality Checks</span>
            </div>
            <div class="card stat-card">
                <span class="stat-num" data-count="98" data-suffix="%">0</span>
                <span class="stat-label" data-en="Customer Satisfaction" data-gu="ગ્રાહક સંતોષ">Customer Satisfaction</span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     SECTION 5 — TESTIMONIALS + CTA
════════════════════════════════════════════ -->
<section class="section" style="background:rgba(10,53,87,0.15);">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label" data-en="Testimonials" data-gu="પ્રશંસાપત્ર">Testimonials</span>
            <h2 class="section-title" data-en="What People Say" data-gu="લોકો શું કહે છે">What People Say</h2>
        </div>

        <div class="testimonials-grid">
            <?php
            $testimonials = [
                ["The taste is incomparable — you can genuinely feel the difference.", "Meera P.", "Siddhapur, GJ"],
                ["Subscription delivery is seamless. My family loves it.", "Rohan Shah", "Ahmedabad, GJ"],
                ["Best water I've had. The eco-bottle return programme is brilliant.", "Anita V.", "Mumbai, MH"],
            ];
            foreach ($testimonials as $t): ?>
            <div class="card testimonial-card">
                <p class="testimonial-quote">"<?= htmlspecialchars($t[0]) ?>"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">👤</div>
                    <div>
                        <div class="testimonial-name"><?= htmlspecialchars($t[1]) ?></div>
                        <div class="testimonial-loc"><?= htmlspecialchars($t[2]) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA Banner -->
        <div class="cta-banner" style="margin-top:4rem;">
            <h2 data-en="Start your pure water journey today" data-gu="આજે જ તમારી શુદ્ધ પાણીની સફર શરૂ કરો">Start your pure water journey today</h2>
            <p data-en="Subscribe and get 10% off your first month. Cancel anytime." data-gu="સબ્સ્ક્રાઇબ કરો અને પ્રથમ મહિનામાં 10% ઓફ મેળવો.">Subscribe and get 10% off your first month. Cancel anytime.</p>
            <a href="register.php" class="btn btn-primary btn-lg" data-en="Get Started Free" data-gu="ફ્રી શરૂ કરો">Get Started Free</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>