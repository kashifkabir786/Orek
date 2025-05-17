/**
 * Coupon Popup Script
 * Shows a popup with discount offer after 5 seconds
 */

document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const couponPopup = document.getElementById('couponPopupOverlay');
    const couponCloseBtn = document.getElementById('couponPopupClose');
    const hiddenCouponInput = document.getElementById('hiddenCouponCode');
    const couponForm = document.getElementById('couponEmailForm');
    
    // Always clear session storage for testing
    sessionStorage.removeItem('couponPopupShown');
    
    // Function to show popup
    function showPopup() {
        // Check if popup has been shown before in this session
        const popupShown = sessionStorage.getItem('couponPopupShown');
        
        // Only show if not shown before in this session
        if (!popupShown && couponPopup) {
            couponPopup.classList.add('show');
            sessionStorage.setItem('couponPopupShown', 'true');
            
            // Fetch coupon code from server (but don't display it)
            fetchCouponCode();
        }
    }
    
    // Function to hide popup
    function hidePopup() {
        if (couponPopup) {
            couponPopup.classList.remove('show');
        }
    }
    
    // Function to fetch coupon code
    function fetchCouponCode() {
        fetch('get_coupon.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Set hidden input value
                    if (hiddenCouponInput) {
                        hiddenCouponInput.value = data.coupon_code;
                    }
                } else {
                    // No active coupons found, set default coupon code
                    if (hiddenCouponInput) {
                        hiddenCouponInput.value = 'WELCOME10';
                    }
                }
            })
            .catch(error => {
                // Set default coupon code on error
                if (hiddenCouponInput) {
                    hiddenCouponInput.value = 'WELCOME10';
                }
            });
    }
    
    // Function to handle form submission
    function handleFormSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(couponForm);
        
        // Show loading message
        const submitBtn = couponForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;
        
        // Submit form data via AJAX
        fetch('send_coupon_email.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Check if response is OK
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            // Try to parse as JSON, but handle text response as well
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, convert text response to JSON format
                return response.text().then(text => {
                    try {
                        // Try to parse as JSON anyway
                        return JSON.parse(text);
                    } catch (e) {
                        // If parsing fails, create a JSON-like object
                        console.warn('Response is not valid JSON:', text);
                        return { success: false, message: 'Invalid server response' };
                    }
                });
            }
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data && data.success) {
                // Show success message
                alert('Success! Your exclusive coupon code has been sent to your email.');
                hidePopup();
            } else {
                // Show error message
                alert(data && data.message ? data.message : 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request. Please try again later.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalBtnText;
            submitBtn.disabled = false;
        });
    }
    
    // Event listeners
    if (couponCloseBtn) {
        couponCloseBtn.addEventListener('click', hidePopup);
    }
    
    if (couponForm) {
        couponForm.addEventListener('submit', handleFormSubmit);
    }
    
    // Show popup after 5 seconds
    setTimeout(showPopup, 5000);
    
    // Close popup when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === couponPopup) {
            hidePopup();
        }
    });
}); 