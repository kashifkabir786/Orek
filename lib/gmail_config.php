<?php
// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com'); // Gmail SMTP सर्वर
define('SMTP_PORT', 587); // Gmail SMTP पोर्ट (587 TLS के लिए)
define('SMTP_USERNAME', 'your-gmail@gmail.com'); // आपका Gmail ईमेल
define('SMTP_PASSWORD', 'your-app-password'); // आपका Gmail ऐप पासवर्ड (2FA सक्षम होने पर आवश्यक)
define('SMTP_FROM_EMAIL', 'your-gmail@gmail.com'); // भेजने वाला ईमेल (SMTP_USERNAME के समान)
define('SMTP_FROM_NAME', 'Orek'); // भेजने वाले का नाम
define('SMTP_SECURE', 'tls'); // Gmail के लिए 'tls'
?> 