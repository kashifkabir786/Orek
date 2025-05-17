 <footer class="footer-widget-area">
     <div class="footer-top section-padding">
         <div class="container">
             <div class="row">
                 <div class="col-lg-4 col-md-6">
                     <div class="widget-item">
                         <div class="widget-title">
                             <div class="widget-logo">
                                 <a href="index.php">
                                     <img src="assets/img/logo/logo.png" width="200px" alt="brand logo" />
                                 </a>
                             </div>
                         </div>
                         <div class="widget-item">
                             <div class="widget-body social-link">
                                 <a href="https://www.facebook.com/share/1BvPk2MvX5/"><i
                                         class="fa-brands fa-facebook-f"></i></a>
                                 <a href="https://www.instagram.com/orekdotin"><i
                                         class="fa-brands fa-instagram"></i></a>
                                 <a href="https://wa.me/917992381874"><i class="fa-brands fa-whatsapp"></i></a>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-2 col-md-6">
                     <div class="widget-item">
                         <h6 class="widget-title">Help</h6>
                         <div class="widget-body">
                             <address class="contact-block">
                                 <ul>
                                     <li>
                                         <a href="contact.php"> Email Us</a>
                                     </li>
                                     <li>
                                         <a href="">Help & FAQ
                                         </a>
                                     </li>
                                     <li>
                                         <a href="">Make a Return</a>
                                     </li>
                                     <li>
                                         <a href="">Shipping Policy</a>
                                     </li>
                                 </ul>
                             </address>
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-2 col-md-6">
                     <div class="widget-item">
                         <h6 class="widget-title">Quick Link</h6>
                         <div class="widget-body">
                             <address class="contact-block">
                                 <ul>
                                     <li><a href="terms-condition.php">Terms & Conditions</a></li>
                                     <li><a href="privacy-policy.php">Privacy Policy</a></li>
                                     <li><a href="return-policy.php">Return Policy</a></li>
                                 </ul>
                             </address>
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-2 col-md-6">
                     <div class="widget-item">
                         <h6 class="widget-title">Company</h6>
                         <div class="widget-body">
                             <address class="contact-block">
                                 <ul>
                                     <li><a href="#">We are hiring</a></li>
                                     <li><a href="#">Press Links</a></li>
                                 </ul>
                             </address>
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-2 col-md-6">
                     <div class="widget-item">
                         <h6 class="widget-title">Company</h6>
                         <div class="widget-body">
                             <address class="contact-block">
                                 <ul>
                                     <li><a href="index.php">Home</a></li>
                                     <li><a href="about.php">About Us</a></li>
                                     <li><a href="contact.php">Contact Us</a></li>
                                 </ul>
                             </address>
                         </div>
                     </div>
                 </div>
                 <div class="row align-items-center mt-20">
                     <div class="col-md-6">
                         <div class="newsletter-wrapper">
                             <h6 class="widget-title-text">Signup for newsletter</h6>
                             <form class="newsletter-inner" id="mc-form">
                                 <input type="email" class="news-field" id="mc-email" autocomplete="off"
                                     placeholder="Enter your email address" required>
                                 <button type="button" class="news-btn" id="mc-submit">Subscribe</button>
                             </form>
                             <div id="newsletter-message"></div>
                             <!-- mail-chimp-alerts Start -->
                             <div class="mailchimp-alerts">
                                 <div class="mailchimp-submitting"></div><!-- mail-chimp-submitting end -->
                                 <div class="mailchimp-success"></div><!-- mail-chimp-success end -->
                                 <div class="mailchimp-error"></div><!-- mail-chimp-error end -->
                             </div>
                             <!-- mail-chimp-alerts end -->
                         </div>
                     </div>
                     <div class="col-md-6">
                         <div class="footer-payment">
                             <img src="assets/img/payment.webp" alt="payment method">
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="footer-bottom">
         <div class="container">
             <div class="row">
                 <div class="col-12">
                     <div class="copyright-text text-center">
                         <p>
                             All Right Reserved By Orek 2024
                         </p>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </footer>

<!-- Structured data for SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Orek",
    "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/",
    "logo": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/assets/img/logo/logo.png",
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "",
        "email": "orekaccessories@gmail.com",
        "contactType": "customer service"
    },
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "India"
    },
    "sameAs": [
        "https://www.facebook.com/orekaccessories",
        "https://www.instagram.com/orekaccessories"
    ]
}
</script>

<!-- Website schema for breadcrumbs -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Orek",
    "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/product-list.php?search={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

<?php
// Add breadcrumb structured data if needed
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page != 'index.php'):
    // Get page title based on file name
    $page_title = ucwords(str_replace(['-', '.php'], [' ', ''], $current_page));
    
    // Get item name if on product details page
    if ($current_page == 'product-details.php' && isset($_GET['item_id'])) {
        $item_id = $_GET['item_id'];
        $query_item = "SELECT item_name FROM item WHERE item_id = " . intval($item_id);
        $result_item = mysqli_query($orek, $query_item);
        if ($result_item && mysqli_num_rows($result_item) > 0) {
            $row_item = mysqli_fetch_assoc($result_item);
            $page_title = $row_item['item_name'];
        }
    }
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "<?php echo $page_title; ?>",
            "item": "https://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
        }
    ]
}
</script>
<?php endif; ?>