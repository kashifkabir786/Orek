<?php
/**
 * XML Sitemap Generator
 * 
 * This script generates an XML sitemap for better SEO crawling
 */

// Include database connection
require_once('Connections/orek.php');

// Set content type
header('Content-Type: text/xml; charset=utf-8');

// Start XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// Base URL of the site
$base_url = 'https://' . $_SERVER['HTTP_HOST'];

// Homepage
$xml .= '  <url>' . PHP_EOL;
$xml .= '    <loc>' . $base_url . '/</loc>' . PHP_EOL;
$xml .= '    <changefreq>daily</changefreq>' . PHP_EOL;
$xml .= '    <priority>1.0</priority>' . PHP_EOL;
$xml .= '  </url>' . PHP_EOL;

// Static pages
$static_pages = array(
    'about-us.php' => 0.8,
    'contact-us.php' => 0.8,
    'cart.php' => 0.6,
    'checkout.php' => 0.5,
    'login.php' => 0.7,
    'register.php' => 0.7,
    'product-list.php' => 0.9,
    'product-list-gift.php' => 0.8,
    'blogs.php' => 0.8
);

foreach ($static_pages as $page => $priority) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . $base_url . '/' . $page . '</loc>' . PHP_EOL;
    $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
    $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

// Category pages
$query_categories = "SELECT category_id, date_updated FROM category";
$categories_result = mysqli_query($orek, $query_categories) or die(mysqli_error($orek));

while ($row_category = mysqli_fetch_assoc($categories_result)) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . $base_url . '/product-list.php?category_id=' . $row_category['category_id'] . '</loc>' . PHP_EOL;
    
    // Last modified date if available
    if (isset($row_category['date_updated']) && $row_category['date_updated'] != '0000-00-00 00:00:00') {
        $xml .= '    <lastmod>' . date('Y-m-d', strtotime($row_category['date_updated'])) . '</lastmod>' . PHP_EOL;
    }
    
    $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
    $xml .= '    <priority>0.8</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

// Product pages
$query_products = "SELECT item_id, date_updated FROM item WHERE listing_status = 'Active'";
$products_result = mysqli_query($orek, $query_products) or die(mysqli_error($orek));

while ($row_product = mysqli_fetch_assoc($products_result)) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . $base_url . '/product-details.php?item_id=' . $row_product['item_id'] . '</loc>' . PHP_EOL;
    
    // Last modified date if available
    if (isset($row_product['date_updated']) && $row_product['date_updated'] != '0000-00-00 00:00:00') {
        $xml .= '    <lastmod>' . date('Y-m-d', strtotime($row_product['date_updated'])) . '</lastmod>' . PHP_EOL;
    }
    
    $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
    $xml .= '    <priority>0.7</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

// Blog pages
$query_blogs = "SELECT blog_id, date FROM blogs";
$blogs_result = mysqli_query($orek, $query_blogs) or die(mysqli_error($orek));

while ($row_blog = mysqli_fetch_assoc($blogs_result)) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . $base_url . '/blog-details.php?blog_id=' . $row_blog['blog_id'] . '</loc>' . PHP_EOL;
    
    // Last modified date if available
    if (isset($row_blog['date']) && $row_blog['date'] != '0000-00-00 00:00:00') {
        $xml .= '    <lastmod>' . date('Y-m-d', strtotime($row_blog['date'])) . '</lastmod>' . PHP_EOL;
    }
    
    $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
    $xml .= '    <priority>0.6</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

// Close XML
$xml .= '</urlset>';

// Write to file
file_put_contents('sitemap.xml', $xml);

// Also create a robots.txt file if it doesn't exist
if (!file_exists('robots.txt')) {
    $robots = "User-agent: *\n";
    $robots .= "Disallow: /Connections/\n";
    $robots .= "Disallow: /admin/\n";
    $robots .= "Disallow: /assets/js/\n";
    $robots .= "Disallow: /assets/css/\n";
    $robots .= "Allow: /assets/img/\n\n";
    $robots .= "# Block old WordPress paths that no longer exist\n";
    $robots .= "Disallow: /wp-content/\n";
    $robots .= "Disallow: /wp-admin/\n";
    $robots .= "Disallow: /wp-includes/\n\n";
    $robots .= "Sitemap: " . $base_url . "/sitemap.xml";
    
    file_put_contents('robots.txt', $robots);
}

echo "Sitemap generated successfully at " . date('Y-m-d H:i:s');
?> 