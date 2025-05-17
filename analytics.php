<?php
/**
 * Orek Analytics System
 * 
 * Collects comprehensive visitor analytics data across all pages
 * Designed to be included once at the top of each page or in a common include file
 */

// Include database connection
require_once('Connections/orek.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic visitor information
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$page_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
$visit_time = date('Y-m-d H:i:s');

// Session and cookie information
$session_id = session_id();
$visitor_id = isset($_COOKIE['orek_visitor_id']) ? $_COOKIE['orek_visitor_id'] : NULL;

// Create visitor ID if not exists
if (!$visitor_id) {
    $visitor_id = uniqid('visitor_', true);
    setcookie('orek_visitor_id', $visitor_id, time() + (86400 * 365), "/"); // 1 year expiry
}

// User data - check if logged in
$user_id = NULL;
$is_logged_in = isset($_SESSION['email']) ? 1 : 0;
if ($is_logged_in) {
    // Get user_id from email
    $email = mysqli_real_escape_string($orek, $_SESSION['email']);
    $user_query = "SELECT user_id FROM user WHERE email = '$email'";
    $user_result = mysqli_query($orek, $user_query);
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_row = mysqli_fetch_assoc($user_result);
        $user_id = $user_row['user_id'];
    }
}

// Device and browser detection
$device_type = 'unknown';
$browser = 'unknown';
$os = 'unknown';

// Detect device type
if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $user_agent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4))) {
    $device_type = 'mobile';
} else if (preg_match('/tablet|ipad|playbook|silk/i', $user_agent)) {
    $device_type = 'tablet';
} else {
    $device_type = 'desktop';
}

// Detect browser
if (preg_match('/MSIE|Trident/i', $user_agent)) {
    $browser = 'Internet Explorer';
} elseif (preg_match('/Firefox/i', $user_agent)) {
    $browser = 'Firefox';
} elseif (preg_match('/Chrome/i', $user_agent)) {
    $browser = 'Chrome';
} elseif (preg_match('/Safari/i', $user_agent)) {
    $browser = 'Safari';
} elseif (preg_match('/Opera/i', $user_agent)) {
    $browser = 'Opera';
} elseif (preg_match('/Edge/i', $user_agent)) {
    $browser = 'Edge';
}

// Detect operating system
if (preg_match('/windows|win32/i', $user_agent)) {
    $os = 'Windows';
} elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
    $os = 'Mac OS';
} elseif (preg_match('/linux/i', $user_agent)) {
    $os = 'Linux';
} elseif (preg_match('/android/i', $user_agent)) {
    $os = 'Android';
} elseif (preg_match('/iphone|ipad|ipod/i', $user_agent)) {
    $os = 'iOS';
}

// Determine source of traffic
$traffic_source = 'Direct';
$search_engines = ['google', 'bing', 'yahoo', 'baidu', 'duckduckgo'];
$social_media = ['facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'youtube'];

if ($referrer) {
    $referrer_host = parse_url($referrer, PHP_URL_HOST);
    if ($referrer_host) {
        // Check if referrer is a search engine
        foreach ($search_engines as $engine) {
            if (stripos($referrer_host, $engine) !== false) {
                $traffic_source = 'Search';
                break;
            }
        }
        
        // Check if referrer is social media
        foreach ($social_media as $social) {
            if (stripos($referrer_host, $social) !== false) {
                $traffic_source = 'Social';
                break;
            }
        }
        
        // If not search or social, it's a referral
        if ($traffic_source === 'Direct') {
            $traffic_source = 'Referral';
        }
    }
}

// Save visit data to database efficiently
// We'll use INSERT DELAYED if available (or simple INSERT if not)
// This reduces the processing time by not waiting for the DB write to complete
$query = "INSERT INTO site_visits 
          (ip_address, user_agent, page_url, referrer, visit_time, session_id, visitor_id, 
           device_type, browser, os, is_logged_in, user_id, traffic_source) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($orek, $query);

if ($stmt) {
    mysqli_stmt_bind_param(
        $stmt, 
        'ssssssssssiis', 
        $ip_address, $user_agent, $page_url, $referrer, $visit_time, $session_id, $visitor_id,
        $device_type, $browser, $os, $is_logged_in, $user_id, $traffic_source
    );
    
    // Execute but don't stop page processing if it fails
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// JavaScript for advanced tracking - loads asynchronously
// Only output this JS once per page load
if (!defined('ANALYTICS_JS_LOADED')) {
    define('ANALYTICS_JS_LOADED', true);
?>

<!-- Orek Analytics JavaScript Tracking -->
<script>
// Wait until document is loaded
window.addEventListener('load', function() {
    // Allow analytics to run asynchronously
    setTimeout(function() {
        try {
            // Performance metrics
            const perfData = {};
            
            // Basic performance data
            if (window.performance && window.performance.timing) {
                const timing = window.performance.timing;
                perfData.pageLoadTime = timing.domContentLoadedEventEnd - timing.navigationStart;
                perfData.pageRenderTime = timing.domComplete - timing.domLoading;
                perfData.totalTime = timing.loadEventEnd - timing.navigationStart;
            }
            
            // Screen and viewport data
            perfData.screenWidth = window.screen.width;
            perfData.screenHeight = window.screen.height;
            perfData.colorDepth = window.screen.colorDepth;
            perfData.viewportWidth = window.innerWidth;
            perfData.viewportHeight = window.innerHeight;
            
            // Send performance data
            sendData('track_performance.php', {
                visitor_id: getCookie('orek_visitor_id'),
                page_url: window.location.href,
                ...perfData
            });
            
            // Set up interaction tracking
            trackUserInteractions();
        } catch (e) {
            // Silent fail - analytics should never break the site
            console.error('Analytics error:', e);
        }
    }, 1000); // Delay to prioritize page load
});

// Helper function to get cookies
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Send data to the server
function sendData(url, data) {
    // Use sendBeacon if available (more reliable for analytics)
    if (navigator.sendBeacon) {
        const blob = new Blob([JSON.stringify(data)], { type: 'application/json' });
        navigator.sendBeacon(url, blob);
    } else {
        // Fallback to fetch API
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
            // Keep fetch from blocking page unload
            keepalive: true
        }).catch(e => console.error('Analytics send error:', e));
    }
}

// Track user interactions
function trackUserInteractions() {
    // Variables to track state
    let lastScrollDepth = 0;
    let scrollTimer;
    let pageActive = true;
    let startTime = new Date();
    let interactions = 0;
    
    // Track clicks
    document.addEventListener('click', function(e) {
        interactions++;
        const target = e.target.closest('a, button, [role="button"], input[type="submit"]');
        if (!target) return;
        
        const data = {
            visitor_id: getCookie('orek_visitor_id'),
            event_type: 'click',
            element_type: target.tagName.toLowerCase(),
            element_id: target.id || '',
            element_class: target.className || '',
            element_text: target.innerText || target.value || '',
            page_url: window.location.href,
            timestamp: new Date().toISOString()
        };
        
        // For links, track the href
        if (target.tagName.toLowerCase() === 'a' && target.href) {
            data.link_url = target.href;
            
            // For external links, track before navigation
            if (target.hostname !== window.location.hostname) {
                sendData('track_events.php', data);
            }
        }
        
        // Only send data every 5 interactions to reduce server load
        if (interactions % 5 === 0) {
            sendData('track_events.php', data);
        }
    });
    
    // Track scroll depth
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {
            // Calculate scroll percentage
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollDepth = Math.round((scrollTop / scrollHeight) * 100);
            
            // Only track significant changes (10% increments)
            if (Math.abs(scrollDepth - lastScrollDepth) >= 10) {
                lastScrollDepth = scrollDepth;
                
                sendData('track_events.php', {
                    visitor_id: getCookie('orek_visitor_id'),
                    event_type: 'scroll',
                    scroll_depth: scrollDepth,
                    page_url: window.location.href,
                    timestamp: new Date().toISOString()
                });
            }
        }, 500);
    });
    
    // Track page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            pageActive = false;
            const timeSpent = Math.round((new Date() - startTime) / 1000);
            
            sendData('track_events.php', {
                visitor_id: getCookie('orek_visitor_id'),
                event_type: 'visibility_change',
                status: 'hidden',
                time_spent: timeSpent,
                page_url: window.location.href,
                timestamp: new Date().toISOString()
            });
        } else {
            pageActive = true;
            startTime = new Date();
            
            sendData('track_events.php', {
                visitor_id: getCookie('orek_visitor_id'),
                event_type: 'visibility_change',
                status: 'visible',
                page_url: window.location.href,
                timestamp: new Date().toISOString()
            });
        }
    });
    
    // Track page exit
    window.addEventListener('beforeunload', function() {
        if (pageActive) {
            const timeSpent = Math.round((new Date() - startTime) / 1000);
            
            sendData('track_events.php', {
                visitor_id: getCookie('orek_visitor_id'),
                event_type: 'page_exit',
                time_spent: timeSpent,
                page_url: window.location.href,
                timestamp: new Date().toISOString()
            });
        }
    });
}
</script>
<?php
}
?> 