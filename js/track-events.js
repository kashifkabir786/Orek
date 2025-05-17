/**
 * Orek Event Tracking System
 * 
 * उपयोगकर्ता इवेंट्स ट्रैकिंग के लिए जावास्क्रिप्ट मॉड्यूल
 * पेज व्यू, क्लिक्स, फॉर्म सबमिशन्स और अन्य इवेंट्स को ट्रैक करता है
 */

// एकल इंस्टेंस सुनिश्चित करने के लिए IIFE का उपयोग
const OrekTracker = (function() {
    // सत्र आईडी जनरेट करें
    const generateSessionId = function() {
        const timestamp = new Date().getTime();
        const randomPart = Math.floor(Math.random() * 1000000000);
        return `${timestamp}-${randomPart}`;
    };

    // स्टोरेज से सत्र आईडी प्राप्त करें या नया बनाएँ
    const getSessionId = function() {
        let sessionId = sessionStorage.getItem('orek_session_id');
        
        if (!sessionId) {
            sessionId = generateSessionId();
            sessionStorage.setItem('orek_session_id', sessionId);
        }
        
        return sessionId;
    };

    // इवेंट सर्वर पर भेजें
    const sendEvent = function(eventData) {
        return fetch('/record_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(eventData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('नेटवर्क प्रतिक्रिया अच्छी नहीं थी');
            }
            return response.json();
        })
        .catch(error => {
            console.error('इवेंट भेजने में त्रुटि:', error);
        });
    };

    // पेज व्यू इवेंट ट्रैक करें
    const trackPageView = function() {
        const eventData = {
            event_type: 'page_view',
            session_id: getSessionId(),
            page_url: window.location.href,
            properties: {
                title: document.title,
                referrer: document.referrer,
                viewport_width: window.innerWidth,
                viewport_height: window.innerHeight
            }
        };
        
        return sendEvent(eventData);
    };

    // क्लिक इवेंट ट्रैक करें
    const trackClick = function(element, properties = {}) {
        const eventData = {
            event_type: 'click',
            session_id: getSessionId(),
            page_url: window.location.href,
            properties: {
                element_id: element.id || '',
                element_class: element.className || '',
                element_text: element.innerText || '',
                element_tag: element.tagName || '',
                ...properties
            }
        };
        
        return sendEvent(eventData);
    };

    // फॉर्म सबमिशन इवेंट ट्रैक करें
    const trackFormSubmit = function(form, properties = {}) {
        const formElements = {};
        
        // फॉर्म फील्ड्स एकत्र करें (संवेदनशील जानकारी को छोड़कर)
        Array.from(form.elements).forEach(element => {
            if (element.name && !element.name.toLowerCase().includes('password') && 
                !element.name.toLowerCase().includes('credit') && 
                !element.name.toLowerCase().includes('card')) {
                formElements[element.name] = element.type === 'checkbox' ? element.checked : element.value;
            }
        });
        
        const eventData = {
            event_type: 'form_submit',
            session_id: getSessionId(),
            page_url: window.location.href,
            properties: {
                form_id: form.id || '',
                form_name: form.name || '',
                form_action: form.action || '',
                form_method: form.method || '',
                form_elements: formElements,
                ...properties
            }
        };
        
        return sendEvent(eventData);
    };

    // कस्टम इवेंट ट्रैक करें
    const trackEvent = function(eventType, properties = {}) {
        const eventData = {
            event_type: eventType,
            session_id: getSessionId(),
            page_url: window.location.href,
            properties: properties
        };
        
        return sendEvent(eventData);
    };

    // ट्रैकिंग प्रारंभ करें
    const init = function() {
        // पेज लोड होने पर पेज व्यू इवेंट ट्रैक करें
        trackPageView();
        
        // पेज के अंतर्गत क्लिक्स ट्रैक करें
        document.addEventListener('click', function(event) {
            const target = event.target;
            
            // लिंक पर क्लिक्स ट्रैक करें
            if (target.tagName === 'A') {
                trackClick(target, {
                    link_href: target.href,
                    link_text: target.innerText
                });
            }
            
            // बटन पर क्लिक्स ट्रैक करें
            if (target.tagName === 'BUTTON' || 
                (target.tagName === 'INPUT' && target.type === 'submit') ||
                target.classList.contains('btn')) {
                trackClick(target);
            }
        });
        
        // फॉर्म सबमिशन ट्रैक करें
        document.addEventListener('submit', function(event) {
            trackFormSubmit(event.target);
        });
    };

    // सार्वजनिक API
    return {
        init: init,
        trackPageView: trackPageView,
        trackClick: trackClick,
        trackFormSubmit: trackFormSubmit,
        trackEvent: trackEvent
    };
})();

// डॉक्यूमेंट रेडी होने पर ट्रैकर प्रारंभ करें
document.addEventListener('DOMContentLoaded', function() {
    OrekTracker.init();
});

// ग्लोबल नेमस्पेस में एक्सपोज़ करें
window.OrekTracker = OrekTracker; 