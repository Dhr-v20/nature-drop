<?php
// includes/footer.php — Common footer
?>

<!-- ───── FOOTER ───── -->
<footer class="footer">
    <div class="footer-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="var(--footer-bg)"/>
        </svg>
    </div>

    <div class="footer-body">
        <div class="footer-brand">
            <div class="footer-logo">💧 Nature<span class="logo-accent">Drop</span></div>
            <p class="footer-tagline" data-en="Pure water. Pure planet." data-gu="શુદ્ધ પાણી. શુદ્ધ ગ્રહ.">Pure water. Pure planet.</p>
        </div>

        <div class="footer-links">
            <h4 data-en="Quick Links" data-gu="ઝડપી લિંક્સ">Quick Links</h4>
            <ul>
                <li><a href="index.php"         data-en="Home"     data-gu="હોમ">Home</a></li>
                <li><a href="products.php"      data-en="Products" data-gu="ઉત્પાદનો">Products</a></li>
                <li><a href="supply-method.php" data-en="Supply"   data-gu="સપ્લાય">Supply Methods</a></li>
                <li><a href="about.php"         data-en="About"    data-gu="અમારા વિશે">About Us</a></li>
            </ul>
        </div>

        <div class="footer-links">
            <h4 data-en="Account" data-gu="એકાઉન્ટ">Account</h4>
            <ul>
                <li><a href="login.php"    data-en="Login"    data-gu="લૉગ ઇન">Login</a></li>
                <li><a href="register.php" data-en="Register" data-gu="નોંધણી">Register</a></li>
            </ul>
        </div>

        <div class="footer-contact">
            <h4 data-en="Contact" data-gu="સંપર્ક">Contact</h4>
            <p>📧 hello@naturedrop.com</p>
            <p>📞 +91 98765 43210</p>
            <p>📍 <span data-en="Siddhapur, Gujarat, India" data-gu="સિદ્ધપુર, ગુજરાત, ભારત">Siddhapur, Gujarat, India</span></p>
            <div class="footer-social">
                <a href="#" aria-label="Instagram">🌿</a>
                <a href="#" aria-label="Twitter">🐦</a>
                <a href="#" aria-label="Facebook">💙</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> NatureDrop. 
           <span data-en="All rights reserved." data-gu="સર્વ હક્કો સુરક્ષિત.">All rights reserved.</span>
        </p>
        <p class="footer-eco">🌱 <span data-en="100% recyclable packaging" data-gu="100% રિસાઇક્લેબલ પેકેજિંગ">100% recyclable packaging</span></p>
    </div>
</footer>

<script src="js/script.js"></script>
<!-- Inline fallback -->
<script>
if (typeof initBubbles === 'undefined') {
    document.write('<script src="\/nature-drop\/js\/script.js"><\/script>');
}
</script>
</body>
</html>