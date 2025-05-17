/**
 * ओरेक एनालिटिक्स सिस्टम - ट्रैकिंग स्क्रिप्ट
 * 
 * उपयोगकर्ता इवेंट्स को ट्रैक करने और परफॉरमेंस डेटा भेजने के लिए स्क्रिप्ट
 */

(function() {
    // यूनिक सेशन आईडी जनरेट करें
    function generateSessionId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    
    // लोकल स्टोरेज में सेशन आईडी की जांच करें या नया जनरेट करें
    var sessionId = localStorage.getItem('orek_session_id');
    if (!sessionId) {
        sessionId = generateSessionId();
        localStorage.setItem('orek_session_id', sessionId);
    }
    
    // सर्वर को इवेंट भेजने का फंक्शन
    function sendEvent(eventType, eventTarget, eventData) {
        var data = {
            event_type: eventType,
            event_target: eventTarget || '',
            page_url: window.location.href,
            event_data: JSON.stringify(eventData || {}),
            session_id: sessionId
        };
        
        // AJAX अनुरोध भेजें
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/record_event.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        // डेटा को उचित फॉर्मेट में परिवर्तित करें
        var formData = Object.keys(data).map(function(key) {
            return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
        }).join('&');
        
        xhr.send(formData);
    }
    
    // पेज व्यू इवेंट रिकॉर्ड करें
    function recordPageView() {
        sendEvent('page_view', document.title, {
            referrer: document.referrer,
            path: window.location.pathname
        });
    }
    
    // क्लिक इवेंट ट्रैकिंग
    function setupClickTracking() {
        document.addEventListener('click', function(e) {
            var target = e.target;
            
            // सभी क्लिक्स को ट्रैक न करें, केवल महत्वपूर्ण एलिमेंट्स को ट्रैक करें
            var trackableElements = [
                'a', 'button', '.btn', '.product-item', '.add-to-cart', 
                '#checkout-button', '.category-link'
            ];
            
            var shouldTrack = false;
            var eventTarget = '';
            
            // जांचें कि क्या हमें इस एलिमेंट को ट्रैक करना चाहिए
            for (var i = 0; i < trackableElements.length; i++) {
                var selector = trackableElements[i];
                if (selector.startsWith('.') || selector.startsWith('#')) {
                    // क्लास या आईडी के आधार पर सेलेक्टर
                    if (target.closest(selector)) {
                        shouldTrack = true;
                        eventTarget = selector;
                        break;
                    }
                } else {
                    // टैग नाम के आधार पर सेलेक्टर
                    if (target.tagName.toLowerCase() === selector || target.closest(selector)) {
                        shouldTrack = true;
                        eventTarget = target.tagName.toLowerCase();
                        break;
                    }
                }
            }
            
            if (shouldTrack) {
                var eventData = {
                    text: target.textContent ? target.textContent.trim() : '',
                    href: target.href || (target.closest('a') ? target.closest('a').href : ''),
                    id: target.id || '',
                    classes: target.className || ''
                };
                
                // विशेष केस: प्रोडक्ट क्लिक
                if (target.closest('.product-item')) {
                    var productItem = target.closest('.product-item');
                    var productId = productItem.dataset.productId || '';
                    var productName = productItem.querySelector('.product-title') ? 
                        productItem.querySelector('.product-title').textContent.trim() : '';
                    var productPrice = productItem.querySelector('.price') ?
                        productItem.querySelector('.price').textContent.trim() : '';
                    
                    eventData.product_id = productId;
                    eventData.product_name = productName;
                    eventData.product_price = productPrice;
                    
                    eventTarget = 'product';
                }
                
                // विशेष केस: 'add to cart' क्लिक
                if (target.closest('.add-to-cart')) {
                    eventType = 'add_to_cart';
                } else {
                    eventType = 'click';
                }
                
                sendEvent(eventType, eventTarget, eventData);
            }
        });
    }
    
    // फॉर्म सबमिशन ट्रैकिंग
    function setupFormTracking() {
        document.addEventListener('submit', function(e) {
            var form = e.target;
            var formId = form.id || '';
            var formAction = form.action || '';
            var formData = {
                form_id: formId,
                form_action: formAction,
                form_name: form.name || ''
            };
            
            // फॉर्म टाइप की पहचान करें
            var formType = 'generic';
            if (form.id.includes('login') || form.action.includes('login')) {
                formType = 'login';
            } else if (form.id.includes('register') || form.action.includes('register')) {
                formType = 'register';
            } else if (form.id.includes('checkout') || form.action.includes('checkout')) {
                formType = 'checkout';
            } else if (form.id.includes('search') || form.querySelector('input[type="search"]')) {
                formType = 'search';
                // सर्च क्वेरी को शामिल करें
                var searchInput = form.querySelector('input[type="search"]') || form.querySelector('input[name="search"]');
                if (searchInput) {
                    formData.search_query = searchInput.value;
                }
            }
            
            formData.form_type = formType;
            sendEvent('form_submit', formType, formData);
        });
    }
    
    // स्क्रॉल ट्रैकिंग
    function setupScrollTracking() {
        var scrollDepths = [25, 50, 75, 100];
        var scrollDepthsReached = {};
        
        // प्रत्येक स्क्रॉल डेप्थ के लिए प्रारंभिक स्थिति सेट करें
        scrollDepths.forEach(function(depth) {
            scrollDepthsReached[depth] = false;
        });
        
        // स्क्रॉल इवेंट पर स्क्रॉल डेप्थ की गणना करें
        window.addEventListener('scroll', function() {
            // स्क्रॉल की स्थिति की गणना करें
            var scrollPosition = window.scrollY;
            var windowHeight = window.innerHeight;
            var documentHeight = Math.max(
                document.body.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.scrollHeight,
                document.documentElement.offsetHeight,
                document.documentElement.clientHeight
            );
            
            var scrollPercentage = (scrollPosition / (documentHeight - windowHeight)) * 100;
            
            // प्रत्येक स्क्रॉल डेप्थ के लिए जांचें
            scrollDepths.forEach(function(depth) {
                if (!scrollDepthsReached[depth] && scrollPercentage >= depth) {
                    scrollDepthsReached[depth] = true;
                    sendEvent('scroll', 'page', {
                        scroll_depth: depth,
                        page_height: documentHeight,
                        scroll_position: scrollPosition
                    });
                }
            });
        }, { passive: true });
    }
    
    // परफॉरमेंस डेटा ट्रैकिंग
    function trackPerformance() {
        window.addEventListener('load', function() {
            // परफॉरमेंस डेटा प्राप्त करें यदि उपलब्ध हो
            if (window.performance && window.performance.timing) {
                var timing = window.performance.timing;
                
                // प्रमुख मेट्रिक्स की गणना करें
                var loadTime = timing.loadEventEnd - timing.navigationStart;
                var domTime = timing.domComplete - timing.domLoading;
                var renderTime = timing.domContentLoadedEventEnd - timing.navigationStart;
                
                // इसे रिकॉर्ड करने के लिए AJAX अनुरोध भेजें
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/record_performance.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                var data = {
                    page_url: window.location.href,
                    load_time: loadTime,
                    dom_time: domTime,
                    render_time: renderTime,
                    session_id: sessionId
                };
                
                // डेटा को उचित फॉर्मेट में परिवर्तित करें
                var formData = Object.keys(data).map(function(key) {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
                }).join('&');
                
                xhr.send(formData);
            }
        });
    }
    
    // ट्रैकिंग फंक्शंस को प्रारंभ करें
    function initializeTracking() {
        recordPageView();
        setupClickTracking();
        setupFormTracking();
        setupScrollTracking();
        trackPerformance();
    }
    
    // DOM लोडेड होने पर ट्रैकिंग शुरू करें
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeTracking);
    } else {
        initializeTracking();
    }
})(); 