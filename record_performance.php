<?php
/**
 * वेबसाइट परफॉरमेंस ट्रैकिंग स्क्रिप्ट
 * 
 * यह स्क्रिप्ट वेबसाइट के परफॉरमेंस मेट्रिक्स को रिकॉर्ड करता है
 * जैसे पेज लोड टाइम, DOM रेंडरिंग टाइम, आदि।
 */

// डेटाबेस कनेक्शन
require_once('Connections/orek.php');

// जरूरी होने पर सेशन स्टार्ट करें
session_start();

// केवल POST अनुरोधों की अनुमति दें
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'केवल POST अनुरोध स्वीकार किए जाते हैं']);
    exit;
}

// परफॉरमेंस डेटा प्राप्त करें
$pageUrl = isset($_POST['page_url']) ? $_POST['page_url'] : '';
$loadTime = isset($_POST['load_time']) ? (int)$_POST['load_time'] : 0;
$domTime = isset($_POST['dom_time']) ? (int)$_POST['dom_time'] : 0;
$renderTime = isset($_POST['render_time']) ? (int)$_POST['render_time'] : 0;
$sessionId = isset($_POST['session_id']) ? $_POST['session_id'] : '';

// जरूरी फील्ड्स की जांच करें
if (empty($pageUrl) || empty($sessionId)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'अपूर्ण डेटा प्रदान किया गया है']);
    exit;
}

// अतिरिक्त डेटा प्राप्त करें
$userId = null;
if (isset($_SESSION['MM_Username'])) {
    // उपयोगकर्ता ईमेल से उनका आईडी प्राप्त करें
    $userEmail = $_SESSION['MM_Username'];
    $query = "SELECT Cid FROM customer WHERE Email = ?";
    
    if ($stmt = mysqli_prepare($orek, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userId);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
}

// उपयोगकर्ता का IP पता और यूजर एजेंट प्राप्त करें
$ipAddress = $_SERVER['REMOTE_ADDR'];
$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

// उपकरण का प्रकार निर्धारित करें (मोबाइल/डेस्कटॉप)
$deviceType = 'desktop';
if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))) {
    $deviceType = 'mobile';
}

// टाइमस्टैम्प प्राप्त करें
$timestamp = date('Y-m-d H:i:s');

// डेटाबेस में परफॉरमेंस डेटा इन्सर्ट करें
$query = "INSERT INTO performance_metrics 
          (user_id, session_id, page_url, load_time, dom_time, render_time, 
          ip_address, user_agent, device_type, timestamp) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$success = false;
$error = '';

try {
    if ($stmt = mysqli_prepare($orek, $query)) {
        mysqli_stmt_bind_param(
            $stmt, 
            "issiiissss", 
            $userId, 
            $sessionId, 
            $pageUrl, 
            $loadTime, 
            $domTime, 
            $renderTime, 
            $ipAddress, 
            $userAgent, 
            $deviceType, 
            $timestamp
        );
        
        $success = mysqli_stmt_execute($stmt);
        
        if (!$success) {
            $error = mysqli_error($orek);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error = mysqli_error($orek);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// जेसन प्रतिक्रिया भेजें
header('Content-Type: application/json');

if ($success) {
    echo json_encode(['success' => true, 'message' => 'परफॉरमेंस डेटा सफलतापूर्वक रिकॉर्ड किया गया है']);
} else {
    echo json_encode(['success' => false, 'message' => 'परफॉरमेंस डेटा रिकॉर्ड करते समय त्रुटि: ' . $error]);
}
?> 