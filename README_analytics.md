# ओरेक एनालिटिक्स सिस्टम (Orek Analytics System)

## परिचय

ओरेक एनालिटिक्स सिस्टम आपकी वेबसाइट के प्रदर्शन और उपयोगकर्ता व्यवहार को ट्रैक करने के लिए एक व्यापक समाधान है। यह सिस्टम आपको महत्वपूर्ण डेटा प्रदान करता है जिससे आप अपनी वेबसाइट और व्यापार रणनीतियों को बेहतर बना सकते हैं।

## स्थापना निर्देश

1. सभी पेजों पर पहले `session-2.php` और फिर `init.php` फाइल को शामिल करें:
   ```php
   <?php 
   // डेटाबेस कनेक्शन
   require_once('Connections/orek.php');
   
   // सेशन प्रबंधन
   require_once('session-2.php');
   
   // अन्य आवश्यक फंक्शन्स और एनालिटिक्स
   require_once('init.php');
   ?>
   ```

2. डेटाबेस में आवश्यक टेबल्स बनाएँ:
   ```sql
   -- Orek Analytics System Database Tables
   -- Execute this SQL to set up the required tables for tracking

   -- Table for basic visit data
   CREATE TABLE IF NOT EXISTS site_visits (
       id INT AUTO_INCREMENT PRIMARY KEY,
       ip_address VARCHAR(45) NOT NULL,
       user_agent TEXT,
       page_url VARCHAR(255) NOT NULL,
       referrer VARCHAR(255),
       visit_time DATETIME NOT NULL,
       session_id VARCHAR(255) NOT NULL,
       visitor_id VARCHAR(255) NOT NULL,
       device_type VARCHAR(20) NOT NULL,
       browser VARCHAR(50) NOT NULL,
       os VARCHAR(50) NOT NULL,
       is_logged_in TINYINT(1) NOT NULL DEFAULT 0,
       user_id INT,
       traffic_source VARCHAR(50) NOT NULL DEFAULT 'Direct',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       INDEX idx_visitor_id (visitor_id),
       INDEX idx_session_id (session_id),
       INDEX idx_visit_time (visit_time),
       INDEX idx_user_id (user_id),
       INDEX idx_page_url (page_url),
       INDEX idx_traffic_source (traffic_source)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Table for performance metrics
   CREATE TABLE IF NOT EXISTS performance_metrics (
       id INT AUTO_INCREMENT PRIMARY KEY,
       visitor_id VARCHAR(255) NOT NULL,
       page_url VARCHAR(255) NOT NULL,
       load_time INT NOT NULL DEFAULT 0,
       render_time INT NOT NULL DEFAULT 0,
       total_time INT NOT NULL DEFAULT 0,
       screen_width INT NOT NULL DEFAULT 0,
       screen_height INT NOT NULL DEFAULT 0,
       color_depth INT NOT NULL DEFAULT 0,
       viewport_width INT NOT NULL DEFAULT 0,
       viewport_height INT NOT NULL DEFAULT 0,
       timestamp DATETIME NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       INDEX idx_visitor_id (visitor_id),
       INDEX idx_page_url (page_url),
       INDEX idx_timestamp (timestamp)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Table for user interaction events
   CREATE TABLE IF NOT EXISTS user_events (
       id INT AUTO_INCREMENT PRIMARY KEY,
       visitor_id VARCHAR(255) NOT NULL,
       event_type VARCHAR(50) NOT NULL,
       page_url VARCHAR(255) NOT NULL,
       timestamp DATETIME NOT NULL,
       element_type VARCHAR(50),
       element_id VARCHAR(100),
       element_class VARCHAR(255),
       element_text TEXT,
       link_url VARCHAR(255),
       scroll_depth INT,
       time_spent INT,
       status VARCHAR(20),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       INDEX idx_visitor_id (visitor_id),
       INDEX idx_event_type (event_type),
       INDEX idx_page_url (page_url),
       INDEX idx_timestamp (timestamp)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Table for aggregated daily statistics
   CREATE TABLE IF NOT EXISTS analytics_daily_summary (
       id INT AUTO_INCREMENT PRIMARY KEY,
       date DATE NOT NULL,
       page_url VARCHAR(255) NOT NULL,
       page_views INT NOT NULL DEFAULT 0,
       unique_visitors INT NOT NULL DEFAULT 0,
       avg_time_on_page INT NOT NULL DEFAULT 0,
       bounce_rate DECIMAL(5,2) NOT NULL DEFAULT 0,
       desktop_views INT NOT NULL DEFAULT 0,
       mobile_views INT NOT NULL DEFAULT 0,
       tablet_views INT NOT NULL DEFAULT 0,
       direct_traffic INT NOT NULL DEFAULT 0,
       search_traffic INT NOT NULL DEFAULT 0,
       referral_traffic INT NOT NULL DEFAULT 0,
       social_traffic INT NOT NULL DEFAULT 0,
       avg_load_time INT NOT NULL DEFAULT 0,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       UNIQUE KEY idx_date_page (date, page_url),
       INDEX idx_date (date),
       INDEX idx_page_url (page_url)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ```

3. सुनिश्चित करें कि `analytics.php`, `track_events.php` और `track_performance.php` फाइलें आपके प्रोजेक्ट के रूट डायरेक्टरी में मौजूद हैं।

## फाइल संरचना

### 1. session-2.php
- सेशन प्रबंधन संभालता है
- आधारभूत लॉगिन चेक करता है
- कुछ सामान्य उपयोगिता फंक्शन प्रदान करता है (dateformat, dateconvert)

### 2. init.php
- सभी पृष्ठों पर आवश्यक फंक्शन्स और एनालिटिक्स आरंभ करता है
- `session-2.php` के बाद शामिल किया जाना चाहिए
- एनालिटिक्स ट्रैकिंग प्रारंभ करता है
- अतिरिक्त उपयोगिता फंक्शन प्रदान करता है

### 3. analytics.php
- पृष्ठ दृश्य ट्रैकिंग
- उपयोगकर्ता सत्र प्रबंधन
- डिवाइस और ब्राउज़र जानकारी एकत्र करना

### 4. track_events.php
- उपयोगकर्ता इंटरेक्शन ट्रैकिंग एंडपॉइंट
- क्लिक, स्क्रॉल, और अन्य इवेंट्स संभालता है

### 5. track_performance.php
- पृष्ठ प्रदर्शन ट्रैकिंग
- लोड समय, रेंडरिंग समय आदि एकत्र करता है

## सिस्टम कंपोनेंट्स

### 1. मुख्य एनालिटिक्स (Core Analytics)
- उपयोगकर्ता यात्राओं का ट्रैकिंग
- सेशन प्रबंधन
- रेफरल स्रोतों का विश्लेषण
- भौगोलिक डेटा संग्रह

### 2. प्रदर्शन ट्रैकिंग (Performance Tracking)
- पेज लोडिंग समय
- डोम रेंडरिंग मेट्रिक्स
- रिसोर्स लोडिंग समय
- एपीआई प्रतिक्रिया समय

### 3. उपयोगकर्ता इंटरेक्शन ट्रैकिंग (User Interaction)
- क्लिक इवेंट्स
- स्क्रॉल गतिविधि
- फॉर्म इंटरेक्शन
- कार्ट और खरीदारी व्यवहार

## रिपोर्ट्स

एनालिटिक्स डैशबोर्ड पर निम्नलिखित रिपोर्ट्स उपलब्ध हैं:
- दैनिक/साप्ताहिक/मासिक यात्रा सारांश
- उपयोगकर्ता प्रतिधारण दर
- प्रदर्शन मेट्रिक्स
- उपयोगकर्ता व्यवहार विश्लेषण
- बिक्री और कन्वर्ज़न डेटा
- डिवाइस और ब्राउज़र उपयोग

## डेटा एकत्रीकरण

सिस्टम डेटा एकत्रित करने के लिए कई तकनीकों का उपयोग करता है:
1. PHP सर्वर-साइड ट्रैकिंग
2. जावास्क्रिप्ट क्लाइंट-साइड ट्रैकिंग
3. कुकीज़ और सेशन डेटा
4. AJAX अनुरोध

## सुरक्षा सावधानियां

- सभी संवेदनशील डेटा एनक्रिप्ट किया गया है
- उपयोगकर्ता पहचान सूचनाओं को अनाम बनाया गया है
- डेटा संरक्षण विनियमों का अनुपालन
- नियमित सुरक्षा ऑडिट

## व्यवस्थापक उपयोग

एनालिटिक्स डैशबोर्ड तक पहुंचने के लिए:
1. एडमिन पैनल में लॉगिन करें
2. मेनू से "एनालिटिक्स" चुनें
3. अपनी पसंद के अनुसार डेटा रेंज और फिल्टर्स सेट करें

## उपयोग उदाहरण

### उपयोगकर्ता यात्राओं के लिए SQL क्वेरी:
```sql
SELECT 
    DATE(visit_time) as visit_date,
    COUNT(*) as total_visits,
    COUNT(DISTINCT ip_address) as unique_visitors
FROM 
    site_visits
WHERE 
    visit_time BETWEEN '2023-01-01' AND '2023-01-31'
GROUP BY 
    DATE(visit_time)
ORDER BY 
    visit_date;
```

### प्रदर्शन डेटा के लिए SQL क्वेरी:
```sql
SELECT 
    page_url,
    AVG(load_time) as avg_load_time,
    MAX(load_time) as max_load_time,
    MIN(load_time) as min_load_time,
    COUNT(*) as views
FROM 
    performance_metrics
GROUP BY 
    page_url
ORDER BY 
    avg_load_time DESC
LIMIT 10;
```

### उपयोगकर्ता व्यवहार के लिए SQL क्वेरी:
```sql
SELECT 
    event_type,
    COUNT(*) as event_count
FROM 
    user_events
WHERE 
    timestamp > DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY 
    event_type
ORDER BY 
    event_count DESC;
```

## ट्रबलशूटिंग

यदि एनालिटिक्स डेटा सही ढंग से दर्ज नहीं हो रहा है, तो निम्न जांचें:

1. `session-2.php` और `init.php` सभी पेजों पर सही क्रम में शामिल किए गए हैं
2. जावास्क्रिप्ट त्रुटियां कंसोल में दिखाई नहीं दे रही हैं
3. डेटाबेस कनेक्शन सही काम कर रहा है
4. सर्वर पर आवश्यक अनुमतियां सेट हैं

## क्लाइंट-साइड ट्रैकिंग का उपयोग

उपयोगकर्ता इंटरेक्शन को मैन्युअल रूप से ट्रैक करने के लिए जावास्क्रिप्ट API का उपयोग करें:

```javascript
// कस्टम इवेंट ट्रैक करें
OrekTracker.trackEvent('product_view', {
    product_id: '12345',
    product_name: 'स्मार्टफोन',
    category: 'इलेक्ट्रॉनिक्स',
    price: 15000
});

// क्लिक ट्रैक करें
const button = document.getElementById('add-to-cart-btn');
OrekTracker.trackClick(button, {
    product_id: '12345',
    action: 'add_to_cart'
});

// फॉर्म सबमिशन ट्रैक करें
const form = document.getElementById('checkout-form');
OrekTracker.trackFormSubmit(form, {
    order_value: 15000,
    items_count: 3
});
```

## अधिक जानकारी

विस्तृत तकनीकी दस्तावेज़ और एपीआई संदर्भ के लिए, कृपया `docs/analytics/` निर्देशिका देखें। 