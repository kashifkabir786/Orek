(function ($) {
  "use strict";

  // Sticky menu
  var $window = $(window);
  $window.on("scroll", function () {
    var scroll = $window.scrollTop();
    if (scroll < 300) {
      $(".sticky").removeClass("is-sticky");
    } else {
      $(".sticky").addClass("is-sticky");
    }
  });

  // tooltip active js
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Background Image JS start
  var bgSelector = $(".bg-img");
  bgSelector.each(function (index, elem) {
    var element = $(elem),
      bgSource = element.data("bg");
    element.css("background-image", "url(" + bgSource + ")");
  });

  // Off Canvas Open close
  $(".mobile-menu-btn").on("click", function () {
    $("body").addClass("fix");
    $(".off-canvas-wrapper").addClass("open");
  });

  $(".btn-close-off-canvas,.off-canvas-overlay").on("click", function () {
    $("body").removeClass("fix");
    $(".off-canvas-wrapper").removeClass("open");
  });

  // offcanvas mobile menu
  var $offCanvasNav = $(".mobile-menu"),
    $offCanvasNavSubMenu = $offCanvasNav.find(".dropdown");

  /* Remove auto append of menu-expand - now handled by PHP */
  
  /*Close Off Canvas Sub Menu*/
  $offCanvasNavSubMenu.slideUp();

  /* Clean up any duplicate menu-expand elements */
  $(".mobile-menu .menu-item-has-children").each(function() {
    var $this = $(this);
    if ($this.find('> .menu-expand').length > 1) {
      $this.find('> .menu-expand').slice(1).remove();
    }
  });

  /*Category Sub Menu Toggle*/
  $offCanvasNav.on("click", "li .menu-expand", function (e) {
    e.preventDefault();
    e.stopPropagation();
    
    var $this = $(this);
    var $parent = $this.parent();
    var $dropdown = $this.siblings('.dropdown');
    
    if ($dropdown.is(':visible')) {
      $parent.removeClass('active');
      $dropdown.slideUp(300);
      $this.removeClass('active');
    } else {
      $parent.addClass('active');
      $dropdown.slideDown(300);
      $this.addClass('active');
      
      // Close other open dropdowns
      $parent.siblings().removeClass('active')
        .find('.dropdown').slideUp(300);
      $parent.siblings().find('.menu-expand').removeClass('active');
    }
  });

  // Allow direct clicks on category links
  $('.mobile-menu .menu-item-has-children > a').on('click', function(e) {
    // Let the link work normally to navigate to product list
    e.stopPropagation();
  });

  // hero slider active js
  $(".hero-slider-active").slick({
    fade: true,
    speed: 1000,
    dots: false,
    autoplay: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 992,
        settings: {
          arrows: false,
          dots: true,
        },
      },
    ],
  });

  // Hero main slider active js
  $(".hero-slider-active-4").slick({
    autoplay: true,
    speed: 1000,
    arrows: false,
    slidesToShow: 4,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
          dots: true,
        },
      },
    ],
  });

  // product carousel active js
  $(".product-carousel-4").slick({
    speed: 1000,
    autoplay: true,
    slidesToShow: 4,
    adaptiveHeight: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          arrows: false,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          arrows: false,
        },
      },
    ],
  });

  // product carousel active
  $(".product-carousel-4_2").slick({
    speed: 1000,
    slidesToShow: 4,
    autoplay: true,
    rows: 2,
    adaptiveHeight: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          arrows: false,
          rows: 1,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          arrows: false,
          rows: 1,
        },
      },
    ],
  });

  // product banner active js
  $(".product-banner-carousel").slick({
    autoplay: true,
    speed: 1000,
    arrows: false,
    slidesToShow: 4,
    adaptiveHeight: true,
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
        },
      },
    ],
  });

  // group product carousel active
  $(".group-list-carousel").each(function () {
    var $this = $(this);
    var $arrowContainer = $(this)
      .parent()
      .siblings(".section-title-append")
      .find(".slick-append");
    $this.slick({
      infinite: true,
      rows: 4,
      prevArrow:
        '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
      nextArrow:
        '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
      appendArrows: $arrowContainer,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
          },
        },
      ],
    });
  });

  // blog carousel active start
  $(".group-list-carousel--3").slick({
    autoplay: true,
    speed: 1000,
    rows: 3,
    slidesToShow: 3,
    adaptiveHeight: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 1,
        },
      },
    ],
  });

  // blog carousel active start
  $(".blog-carousel-2").slick({
    speed: 1000,
    dots: true,
    arrows: false,
    autoplay: true,
  });

  // testimonial cariusel active js
  $(".testimonial-content-carousel").slick({
    arrows: false,
    asNavFor: ".testimonial-thumb-carousel",
  });

  // product details slider nav active
  $(".testimonial-thumb-carousel").slick({
    slidesToShow: 3,
    asNavFor: ".testimonial-content-carousel",
    centerMode: true,
    arrows: false,
    centerPadding: 0,
    focusOnSelect: true,
  });

  // blog carousel active
  $(".blog-carousel-active").slick({
    autoplay: true,
    speed: 1000,
    slidesToShow: 3,
    adaptiveHeight: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 1,
        },
      },
    ],
  });

  //  Hot deals carousel active start
  $(".deals-carousel-active").slick({
    autoplay: true,
    speed: 1000,
    slidesToShow: 3,
    adaptiveHeight: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 576,
        settings: {
          arrows: false,
          slidesToShow: 1,
        },
      },
    ],
  });

  //  Hot deals carousel active start
  $(".deals-carousel-active--two").slick({
    autoplay: true,
    speed: 1000,
    slidesToShow: 4,
    adaptiveHeight: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 576,
        settings: {
          arrows: false,
          slidesToShow: 1,
        },
      },
    ],
  });

  // brand logo carousel active js
  $(".brand-logo-carousel").slick({
    speed: 1000,
    slidesToShow: 5,
    adaptiveHeight: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fa-solid fa-angle-right"></i></button>',
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 4,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
          arrows: false,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          arrows: false,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          arrows: false,
        },
      },
    ],
  });

  // product details slider active
  $(".product-large-slider").slick({
    fade: true,
    arrows: false,
    speed: 1000,
    asNavFor: ".pro-nav",
  });

  // product details slider nav active
  $(".pro-nav").slick({
    slidesToShow: 4,
    asNavFor: ".product-large-slider",
    centerMode: true,
    speed: 1000,
    centerPadding: 0,
    focusOnSelect: true,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="lnr lnr-chevron-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="lnr lnr-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 3,
        },
      },
    ],
  });

  //nice select active start
  $("select").niceSelect();

  // Image Zoom
  // $(".img-zoom").zoom();

  // offcanvas minicart button js
  $(".minicart-btn").on("click", function () {
    $("body").addClass("fix");
    $(".minicart-inner").addClass("show");
  });

  $(".offcanvas-close, .minicart-close,.offcanvas-overlay").on(
    "click",
    function () {
      $("body").removeClass("fix");
      $(".minicart-inner").removeClass("show");
    }
  );

  // Data countdown active js
  $("[data-countdown]").each(function () {
    var $this = $(this),
      finalDate = $(this).data("countdown");
    $this.countdown(finalDate, function (event) {
      $this.html(
        event.strftime(
          '<div class="single-countdown"><span class="single-countdown__time">%D</span><span class="single-countdown__text">Days</span></div><div class="single-countdown"><span class="single-countdown__time">%H</span><span class="single-countdown__text">Hours</span></div><div class="single-countdown"><span class="single-countdown__time">%M</span><span class="single-countdown__text">Mins</span></div><div class="single-countdown"><span class="single-countdown__time">%S</span><span class="single-countdown__text">Secs</span></div>'
        )
      );
    });
  });

  // quantity change js
  $(".pro-qty").prepend('<span class="dec qtybtn">-</span>');
  $(".pro-qty").append('<span class="inc qtybtn">+</span>');
  $(".qtybtn").on("click", function () {
    var $button = $(this);
    var oldValue = $button.parent().find("input").val();
    if ($button.hasClass("inc")) {
      var newVal = parseFloat(oldValue) + 1;
    } else {
      // Don't allow decrementing below zero
      if (oldValue > 0) {
        var newVal = parseFloat(oldValue) - 1;
      } else {
        newVal = 0;
      }
    }
    $button.parent().find("input").val(newVal);
  });

  // product view mode change js
  $(".product-view-mode a").on("click", function (e) {
    e.preventDefault();
    var shopProductWrap = $(".shop-product-wrap");
    var viewMode = $(this).data("target");
    $(".product-view-mode a").removeClass("active");
    $(this).addClass("active");
    shopProductWrap.removeClass("grid-view list-view").addClass(viewMode);
  });

  // pricing filter
  var rangeSlider = $(".price-range"),
    amount = $("#amount"),
    minPrice = rangeSlider.data("min"),
    maxPrice = rangeSlider.data("max");
  rangeSlider.slider({
    range: true,
    min: minPrice,
    max: maxPrice,
    values: [minPrice, maxPrice],
    slide: function (event, ui) {
      amount.val("$" + ui.values[0] + " - $" + ui.values[1]);
    },
  });
  amount.val(
    " $" +
      rangeSlider.slider("values", 0) +
      " - $" +
      rangeSlider.slider("values", 1)
  );

  // Checkout Page accordion
  $("#create_pwd").on("change", function () {
    $(".account-create").slideToggle("100");
  });

  $("#ship_to_different").on("change", function () {
    $(".ship-to-different").slideToggle("100");
  });

  // Payment Method Accordion
  $('input[name="paymentmethod"]').on("click", function () {
    var $value = $(this).attr("value");
    $(".payment-method-details").slideUp();
    $('[data-method="' + $value + '"]').slideDown();
  });

  // Scroll to top active js
  $(window).on("scroll", function () {
    if ($(this).scrollTop() > 600) {
      $(".scroll-top").removeClass("not-visible");
    } else {
      $(".scroll-top").addClass("not-visible");
    }
  });
  $(".scroll-top").on("click", function (event) {
    $("html,body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  // Search trigger js
  $(".search-trigger").on("click", function () {
    $(".header-search-box").toggleClass("search-box-open");
  });

  // Mail-chimp for dynamic newsletter
  $("#mc-form").ajaxChimp({
    language: "en",
    callback: mailChimpResponse,
    // ADD YOUR MAILCHIMP URL BELOW HERE!
    url: "https://devitems.us11.list-manage.com/subscribe/post?u=6bbb9b6f5827bd842d9640c82&amp;id=05d85f18ef",
  });

  // mail-chimp active js
  function mailChimpResponse(resp) {
    if (resp.result === "success") {
      $(".mailchimp-success")
        .html("" + resp.msg)
        .fadeIn(900);
      $(".mailchimp-error").fadeOut(400);
    } else if (resp.result === "error") {
      $(".mailchimp-error")
        .html("" + resp.msg)
        .fadeIn(900);
    }
  }

  // Instagram feed carousel active
  $(".instagram-carousel").slick({
    slidesToShow: 6,
    slidesToScroll: 2,
    autoplay: true,
    speed: 1000,
    dots: false,
    arrows: false,
    responsive: [
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 4,
        },
      },
    ],
  });

  // Custom fix for mobile zoom issues
  function setupMobileScrollFix() {
    // Only apply on touch devices
    if ('ontouchstart' in window) {
      // Reset zoom on page scroll to prevent users getting stuck
      let lastScrollTop = 0;
      $(window).on('scroll', function() {
        let st = $(this).scrollTop();
        // Detect scroll direction and distance
        if (Math.abs(st - lastScrollTop) > 30) {
          // Close any open zooms when user is scrolling (more than 30px)
          $('.click-zoom').each(function() {
            if ($(this).has('.zoomImg').length) {
              // Programmatically trigger click to close zoom
              $(this).trigger('click');
            }
          });
        }
        lastScrollTop = st;
      });
      
      // Fix for zoom container becoming unresponsive
      $(document).on('click touchend', '.zoomImg', function(e) {
        e.stopPropagation();
        $(this).parent().trigger('click');
      });
    }
  }

  // Call the function on document ready
  $(document).ready(function() {
    setupMobileScrollFix();
  });
})(jQuery);

function updateCartCount(count) {
  $(".notification").each(function () {
    if ($(this).closest("a").find(".fa-bag-shopping, .pe-7s-shopbag").length) {
      $(this).text(count);
    }
  });
}
function removeCartItem(cartId, itemId) {
  if (!cartId || !itemId) {
    console.error("Invalid parameters:", { cartId, itemId });
    return;
  }

  cartId = parseInt(cartId);
  itemId = parseInt(itemId);

  $.ajax({
    url: "cart_operations.php",
    type: "POST",
    dataType: "json",
    data: {
      action: "remove",
      cart_id: cartId,
      item_id: itemId,
    },
    beforeSend: function () {
      // Show loading spinner
      $(
        `.minicart-remove[onclick="removeCartItem(${cartId}, ${itemId})"]`
      ).html('<i class="fa fa-spinner fa-spin"></i>');
    },
    success: function (response) {
      if (response.success) {
        // Remove item from mini cart
        $(`.minicart-remove[onclick="removeCartItem(${cartId}, ${itemId})"]`)
          .closest(".minicart-item")
          .fadeOut(400, function () {
            $(this).remove();

            // Update cart totals
            $.ajax({
              url: "get_cart_totals.php",
              type: "GET",
              success: function (totalsResponse) {
                $(".minicart-pricing-box").html(totalsResponse);
              },
            });

            // Update cart count
            $.ajax({
              url: "get_cart_count.php",
              type: "GET",
              dataType: "json",
              success: function (countResponse) {
                if (countResponse.success) {
                  updateCartCount(countResponse.count);
                }
              },
            });
          });

        // Show success message
        const messageDiv = $("<div>")
          .addClass("cart-message")
          .html('<i class="fa fa-check-circle"></i> Item removed successfully!')
          .appendTo("body");

        setTimeout(function () {
          messageDiv.fadeOut(function () {
            $(this).remove();
          });
        }, 3000);
      } else {
        // Show error message
        const messageDiv = $("<div>")
          .addClass("cart-message error")
          .html(
            '<i class="fa fa-times-circle"></i> ' +
              (response.message || "Error removing item")
          )
          .appendTo("body");

        setTimeout(function () {
          messageDiv.fadeOut(function () {
            $(this).remove();
          });
        }, 3000);

        // Restore remove button
        $(
          `.minicart-remove[onclick="removeCartItem(${cartId}, ${itemId})"]`
        ).html('<i class="fa-solid fa-xmark"></i>');
      }
    },
    error: function (xhr, status, error) {
      console.error("Error details:", {
        status,
        error,
        response: xhr.responseText,
      });

      // Show error message
      const messageDiv = $("<div>")
        .addClass("cart-message error")
        .html(
          '<i class="fa fa-times-circle"></i> Connection error. Please try again.'
        )
        .appendTo("body");

      setTimeout(function () {
        messageDiv.fadeOut(function () {
          $(this).remove();
        });
      }, 3000);

      // Restore remove button
      $(
        `.minicart-remove[onclick="removeCartItem(${cartId}, ${itemId})"]`
      ).html('<i class="fa-solid fa-xmark"></i>');
    },
  });
}
$(document).ready(function () {
  $("#mc-submit").on("click", function () {
    const email = $("#mc-email").val();
    const button = $(this);
    const originalText = button.text();

    if (!email) {
      showNewsletterMessage("Please enter your email address", false);
      return;
    }

    button.prop("disabled", true).text("Subscribing...");

    $.ajax({
      url: "newsletter_subscribe.php",
      type: "POST",
      dataType: "json",
      data: {
        email: email,
      },
      success: function (response) {
        showNewsletterMessage(response.message, response.success);
        if (response.success) {
          $("#mc-email").val("");
        }
      },
      error: function () {
        showNewsletterMessage("Something went wrong. Please try again.", false);
      },
      complete: function () {
        button.prop("disabled", false).text(originalText);
      },
    });
  });

  function showNewsletterMessage(message, isSuccess) {
    const alertClass = isSuccess ? "alert-success" : "alert-danger";
    $("#newsletter-message")
      .html(`<div class="alert ${alertClass}">${message}</div>`)
      .fadeIn();

    // Hide message after 3 seconds
    setTimeout(function () {
      $("#newsletter-message").fadeOut();
    }, 3000);
  }
});
