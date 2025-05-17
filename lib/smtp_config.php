<?php
// SMTP Configuration
define('SMTP_HOST', 'smtp.hostinger.com'); // Hostinger SMTP सर्वर
define('SMTP_PORT', 465); // SMTP पोर्ट (465 SSL के लिए, 587 TLS के लिए)
define('SMTP_USERNAME', 'care@orek.in'); // आपका Hostinger ईमेल
define('SMTP_PASSWORD', 'Orek@2023'); // आपका Hostinger ईमेल पासवर्ड
define('SMTP_FROM_EMAIL', 'care@orek.in'); // भेजने वाला ईमेल (यह SMTP_USERNAME के समान होना चाहिए)
define('SMTP_FROM_NAME', 'Orek'); // भेजने वाले का नाम
define('SMTP_SECURE', 'ssl'); // 'ssl' पोर्ट 465 के लिए, 'tls' पोर्ट 587 के लिए
?> 