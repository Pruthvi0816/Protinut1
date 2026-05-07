                                                <!-- footer start -->
<footer class="position-top bg_img pb-70" data-background="assets/img/bg/footer_bg.png">
    <div class="container">
        <div id="contact" class="contact pb-100">
            <div class="row">
                <div class="col-lg-7">
                    <div class="xb-contact contact-mt--255">
                        <div class="contact-title mb-35">
                            <span><img src="assets/img/icon/directbox-notif.svg" alt="">Contact Us</span>
                            <h3>Do you have questions or want more <br> information?</h3>
                        </div>
                        <form class="contact-from" id="contactForm" action="contact_action.php" method="POST">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="xb-item--field">
                                        <span><img src="assets/img/icon/c_user.svg" alt=""></span>
                                        <input type="text" name="name" placeholder="Your Name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="xb-item--field">
                                        <span><img src="assets/img/icon/c_mail.svg" alt=""></span>
                                        <input type="email" name="email" placeholder="Your Email" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="xb-item--field">
                                        <span><img src="assets/img/icon/c_call.svg" alt=""></span>
                                        <input type="text" name="phone" placeholder=">+91 96999 61011">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="xb-item--field">
                                        <span><img src="assets/img/icon/c_message.svg" alt=""></span>
                                        <textarea name="message" cols="30" rows="10" placeholder="Write Your Message..."
                                            required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="thm-btn thm-btn--black" type="submit">Send Message</button>
                                    <div id="contactFeedback" class="mt-2" style="display:none;"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="contact-info contact-mt--255 mt-md-30">
                        <div class="xb-item--head">
                            <div class="xb-item--address">
                                <h3><img src="assets/img/icon/location.svg" alt="">our address</h3>
                                <p>PLOT NO 3 NEAR FILTER TAKI MIDC,DHARASHIV-413501,MAHARASHTRA. <br> </p>
                            </div>
                            <div class="xb-item--open">
                                <p>Monday - Friday <br>
                                    09:00AM - 10:00PM</p>
                                <a href="mailto:support@protinut.com"><img src="assets/img/icon/sms-tracking.svg"
                                        alt="">Protinut.in@gmail.com</a>
                            </div>
                            <ul class="xb-item--social ul_li mt-30">
                                <!--<li><a href="#!"><i class="fab fa-telegram-plane"></i></a></li>-->
                                <!--<li><a href="#!"><i class="fab fa-whatsapp"></i></a></li>-->
                                <!--<li><a href="#!"><i class="fab fa-facebook-f"></i></a></li>-->
                                 <li><a href="https://www.instagram.com/protinut.in?igsh=MW1lZDFkYnFuYWtleA=="><i class="fab fa-instagram"></i></a></li>
                            </ul>
                        </div>
                        <div class="xb-item--cta" data-background="assets/img/bg/cta_bg.jpg">
                            <p>Our help desk is available for you <br> every day, 09:00AM - 10:00PM</p>
                            <h3>+91 830 830 230 1</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-inner">
            <div class="footer-logo mb-25 text-center">
                <a href="index.php" class="site-logo-text">Protinut</a>
            </div>
            <div class="sec-title sec-title--white text-center mb-50">
                <h2 class="title">in a healthy body, healthy mind</h2>
            </div>
            <ul class="footer-nav ul_li_center">
                <li><a href="#!">all products</a></li>
                <!--<li><a href="#!">track order</a></li>-->
                <!--<li><a href="#!">my account</a></li>-->
                <!--<li><a href="#!">gift cards</a></li>-->
                <li><a href="#!">our story</a></li>
                <!--<li><a href="#!">careers</a></li>-->
                <li><a href="#!">contact</a></li>
            </ul>
            <div class="footer-bottom mt-50 ul_li_between">
                <div class="footer-copyright mt-30">
                    Copyright &copy; 2026 Protinut All rights reserved.
                </div>
                <ul class="footer-links ul_li mt-30">
                    <li><a href="#!">terms of conditions</a></li>
                    <li><a href="#!">privacy policy</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- footer end -->


</div>

<!-- jquery include -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/swiper.min.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/touchspin.js"></script>
<script src="assets/js/jquery-ui.min.js"></script>
<script src="assets/js/jquery.inview.min.js"></script>
<script src="assets/js/jquery.easing.js"></script>
<script src="assets/js/scrollspy.js"></script>
<script src="assets/js/main.js"></script>

<!-- Auth Modal JS for non-logged-in users -->
<script>
    $(document).ready(function () {
        // Show auth modal
        window.showAuthModal = function () {
            $('#authModal').fadeIn(300);
            $('body').css('overflow', 'hidden');
        };
        // Hide auth modal
        $('#authModalClose, #authModalOverlay').on('click', function () {
            $('#authModal').fadeOut(300);
            $('body').css('overflow', '');
        });



        // If not logged in, intercept buy/cart actions
        if (typeof isLoggedIn !== 'undefined' && !isLoggedIn) {
            // Intercept add-to-cart form submissions
            $(document).on('submit', 'form[action="cart_action.php"]', function (e) {
                e.preventDefault();
                showAuthModal();
            });

            // Intercept BUY NOW and add-to-cart links
            $(document).on('click', 'a.thm-btn[href="shop-single.php"], a.xb-item--cart[href="shop-single.php"]', function (e) {
                e.preventDefault();
                showAuthModal();
            });

            // Intercept cart icon click in header
            $(document).on('click', '.header-shop-cart > a', function (e) {
                e.preventDefault();
                showAuthModal();
            });
        }
    });
</script>
<?php if (isset($pageFooterScripts)) {
    echo $pageFooterScripts;
} ?>

</body>

</html>        
                