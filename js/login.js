$(document).ready(function () {
  // Add fade-in animation to form
  $('.card').addClass('fade-in');
  
  // Input focus animations
  $('.form-control').on('focus', function() {
    $(this).parent().addClass('focused');
  }).on('blur', function() {
    $(this).parent().removeClass('focused');
  });

  $("#loginForm").submit(function (e) {
    e.preventDefault();
    
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();
    let $btn = $("#loginBtn");
    let $form = $(this);

    // Clear previous messages
    $("#message").empty();
    $form.removeClass('error-shake');

    // Validation with enhanced error handling
    if (!email || !password) {
      showMessage("All fields are required.", "danger");
      $form.addClass('error-shake');
      return;
    }

    if (!isValidEmail(email)) {
      showMessage("Please enter a valid email address.", "danger");
      $('#email').addClass('error-shake');
      return;
    }

    // Loading state with spinner
    $btn.prop('disabled', true)
        .html('<span class="loading-spinner"></span>Signing In...')
        .addClass('loading');

    $.ajax({
      url: "php/always_success.php",
      method: "POST",
      dataType: "json",
      data: { email: email, password: password },
      timeout: 10000,
      success: function (data) {
        console.log('AJAX Response:', data);
        if (data && data.status === "success") {
          console.log('Login success, storing data and redirecting');
          localStorage.setItem('sessionToken', data.token);
          localStorage.setItem('user', JSON.stringify(data.user));
          alert('Login successful! Redirecting...');
          window.location.href = "profile.html";
        } else {
          console.log('Login failed:', data);
          showMessage(data.message || "Login failed. Please try again.", "danger");
          $form.addClass('error-shake');
        }
      },
      error: function (xhr, status, error) {
        let errorMsg = "Connection error. Please try again.";
        
        if (status === 'timeout') {
          errorMsg = "Request timed out. Please check your connection.";
        } else if (xhr.status === 404) {
          errorMsg = "Service not found. Please contact support.";
        } else if (xhr.status === 500) {
          errorMsg = "Server error. Please try again later.";
        }
        
        showMessage(errorMsg, "danger");
        $form.addClass('error-shake');
        console.error('Login Error:', { xhr, status, error });
      },
      complete: function() {
        setTimeout(() => {
          $btn.prop('disabled', false)
              .html('Sign In')
              .removeClass('loading');
        }, 1000);
      }
    });
  });

  function showMessage(message, type) {
    const alertHtml = `
      <div class="alert alert-${type} slide-up" role="alert">
        <strong>${type === 'success' ? '✓' : '⚠'}</strong> ${message}
      </div>
    `;
    $("#message").html(alertHtml);
    
    // Auto-hide success messages
    if (type === 'success') {
      setTimeout(() => {
        $('.alert').fadeOut();
      }, 3000);
    }
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
  
  // Remove shake animation after it completes
  $(document).on('animationend', '.error-shake', function() {
    $(this).removeClass('error-shake');
  });
  
  // Password visibility on tap and hold
  $('#togglePassword').on('mousedown touchstart', function(e) {
    e.preventDefault();
    $('#password').attr('type', 'text');
    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
  }).on('mouseup mouseleave touchend', function() {
    $('#password').attr('type', 'password');
    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
  });
});