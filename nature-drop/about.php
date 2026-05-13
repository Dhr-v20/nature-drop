<?php
// about.php — About / company details page
require_once 'includes/db.php';

$pageTitle  = 'About Us — Nature-Drop';
$activePage = 'about';

// Handle contact form submission
$contactMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $contactMsg = 'success';
        } catch (Exception $e) {
            $contactMsg = 'error';
        }
    } else {
        $contactMsg = 'empty';
    }
}

include 'includes/header.php';
?>

<!-- Page Hero -->
<div class="page-hero about-hero">
    <span class="section-label" data-en="Our Story" data-gu="અમારી વાર્તા">Our Story</span>
    <h1 class="page-hero-title" data-en="Born from a love<br>of pure water" data-gu="શુદ્ધ પાણીના પ્રેમ<br>માંથી જન્મ્યું">Born from a love<br>of pure water</h1>
    <p class="page-hero-sub" data-en="Founded in Siddhapur, Gujarat — a land celebrated for its water traditions — Nature-Drop is our promise to the world." data-gu="સિદ્ધપુર, ગુજરાતમાં સ્થપાયેલ — પાણીની પ્રથાઓ માટે ઉજ્જવળ — Nature-Drop એ વિશ્વ સાથે અમારું વચન છે.">
        Founded in Siddhapur, Gujarat — a land celebrated for its water traditions — Nature-Drop is our promise to the world.
    </p>
</div>

<section class="section">
    <div class="section-inner">

        <!-- Story grid -->
        <div class="about-grid">
            <div class="about-img-placeholder">🌊</div>
            <div class="about-text">
                <h3 data-en="Who We Are" data-gu="આપણે કોણ છીએ">Who We Are</h3>
                <p data-en="Nature-Drop was founded in 2013 by a family who had been drinking from the same underground spring for four generations. When urbanisation threatened their water source, they built a sustainable bottling facility to preserve and share it with the world." data-gu="Nature-Drop ની સ્થાપના 2013 માં એ પરિવાર દ્વારા કરવામાં આવી હતી જે ચાર પેઢીઓથી ભૂગર્ભ ઝરણ પીતા હતા.">
                    Nature-Drop was founded in 2013 by a family who had been drinking from the same underground spring for four generations. When urbanisation threatened their water source, they built a sustainable bottling facility to preserve and share it with the world.
                </p>
                <p data-en="Today we serve over 50,000 families across North Gujarat, with a mission to make clean, pure water accessible to every household — without harming the planet that provides it." data-gu="આજે અમે ઉત્તર ગુજરાતમાં 50,000 થી વધારે પરિવારોની સેવા કરીએ છીએ, ગ્રહ ને નુકસાન કર્યા વિના.">
                    Today we serve over 50,000 families across North Gujarat, with a mission to make clean, pure water accessible to every household — without harming the planet that provides it.
                </p>
                <a href="products.php" class="btn btn-primary" data-en="Shop Our Products" data-gu="ઉત્પાદ ખરીદો">Shop Our Products</a>
            </div>
        </div>

        <!-- Mission & Values -->
        <div class="features-grid" style="margin-top:2rem;">
            <?php
            $values = [
                ['🌿','Sustainability', 'Every decision we make considers the impact on our springs, our packaging, and our planet.'],
                ['🔬','Purity',         'We never compromise on water quality. 172 quality checks, every batch, every time.'],
                ['🤝','Community',      'Our local farmers, our delivery drivers, our village — people come first at NatureDrop.'],
                ['💡','Innovation',     'From smart dispensers to AI route optimisation, we embrace technology that reduces waste.'],
            ];
            foreach ($values as $v): ?>
            <div class="card feature-card">
                <span class="feature-icon"><?= $v[0] ?></span>
                <h3 class="feature-title"><?= $v[1] ?></h3>
                <p class="feature-desc"><?= $v[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Team -->
        <div style="margin-top:5rem;">
            <div class="section-header">
                <span class="section-label" data-en="Our Team" data-gu="અમારી ટીમ">Our Team</span>
                <h2 class="section-title" data-en="The People Behind the Drop" data-gu="ડ્રૉપ પાછળ ના લોકો">The People Behind the Drop</h2>
            </div>
            <div class="team-grid">
                <?php
                $team = [
                    ['🧑‍💼', 'Kiran Patel',     'Founder & CEO'],
                    ['👩‍🔬', 'Priya Desai',     'Head of Quality'],
                    ['🧑‍💻', 'Nikhil Shah',     'Tech & Logistics'],
                    ['👩‍🌾', 'Ananya Mehta',    'Sustainability Lead'],
                    ['🧑‍🍳', 'Ravi Joshi',      'Operations Manager'],
                    ['👩‍💼', 'Sneha Trivedi',   'Customer Success'],
                ];
                foreach ($team as $t): ?>
                <div class="card team-card">
                    <div class="team-avatar"><?= $t[0] ?></div>
                    <div class="team-name"><?= $t[1] ?></div>
                    <div class="team-role"><?= $t[2] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contact -->
        <div id="contact" style="margin-top:5rem;">
            <div class="section-header">
                <span class="section-label" data-en="Get in Touch" data-gu="સંપર્ક કરો">Get in Touch</span>
                <h2 class="section-title" data-en="Contact Us" data-gu="અમારો સંપર્ક કરો">Contact Us</h2>
            </div>

            <div class="contact-grid">
                <div class="contact-info">
                    <h3 data-en="We'd love to hear from you" data-gu="અમે તમારી પાસેથી સાંભળવા ઈચ્છીએ છીએ">We'd love to hear from you</h3>
                    <p data-en="Whether it's a question about our products, a delivery issue, or partnership inquiry — reach out." data-gu="ઉત્પાદ, ડિલિવરી અથવા ભાગીદારી — સંપર્ક કરો.">Whether it's a question about our products, a delivery issue, or partnership inquiry — reach out.</p>
                    <div class="contact-item"><span>📧</span><span>hello@naturedrop.com</span></div>
                    <div class="contact-item"><span>📞</span><span>+91 98765 43210</span></div>
                    <div class="contact-item"><span>🕐</span><span data-en="Mon–Sat, 9 AM – 6 PM" data-gu="સોમ–શનિ, સવારે 9 – સાંજે 6">Mon–Sat, 9 AM – 6 PM</span></div>
                    <div class="contact-item"><span>📍</span><span data-en="Nature-Drop HQ, Siddhapur, Gujarat – 384151" data-gu="Nature-Drop HQ, સિદ્ધપુર, ગુજરાત – 384151">Nature-Drop HQ, Siddhapur, Gujarat – 384151</span></div>
                </div>

                <div class="contact-form">
                    <?php if ($contactMsg === 'success'): ?>
                        <div class="alert alert-success" data-en="✅ Message sent! We'll reply within 24 hours." data-gu="✅ સંદેશ મોકલ્યો! અમે 24 કલાકમાં જવાબ આપીશું.">✅ Message sent! We'll reply within 24 hours.</div>
                    <?php elseif ($contactMsg === 'error'): ?>
                        <div class="alert alert-error">Something went wrong. Please try again.</div>
                    <?php elseif ($contactMsg === 'empty'): ?>
                        <div class="alert alert-error" data-en="Please fill in all fields." data-gu="કૃપા કરી બધા ક્ષેત્ર ભરો.">Please fill in all fields.</div>
                    <?php endif; ?>

                    <form method="POST" class="validate">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" data-en="Your Name" data-gu="તમારું નામ">Your Name</label>
                                <input type="text" name="name" class="form-input" required placeholder="Kiran Patel">
                            </div>
                            <div class="form-group">
                                <label class="form-label" data-en="Email" data-gu="ઇમેઇલ">Email</label>
                                <input type="email" name="email" class="form-input" required placeholder="you@example.com">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" data-en="Message" data-gu="સંદેશ">Message</label>
                            <textarea name="message" class="form-input" rows="5" required placeholder="How can we help?" style="resize:vertical;"></textarea>
                        </div>
                        <button type="submit" name="contact_submit" class="btn btn-primary form-submit" data-en="Send Message" data-gu="સંદેશ મોકલો">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>