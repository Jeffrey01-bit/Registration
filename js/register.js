$(document).ready(function () {
  let emailVerified = false;
  
  // Disable register button initially
  $('#registerBtn').prop('disabled', true);
  
  let otpTimer;
  
  // OTP input handling
  $('.otp-input').on('input', function() {
    const value = $(this).val();
    if (value && /^[0-9]$/.test(value)) {
      $(this).addClass('filled');
      const nextInput = $(this).next('.otp-input');
      if (nextInput.length) {
        nextInput.focus();
      }
    } else {
      $(this).removeClass('filled').val('');
    }
  });
  
  $('.otp-input').on('keydown', function(e) {
    if (e.key === 'Backspace' && !$(this).val()) {
      const prevInput = $(this).prev('.otp-input');
      if (prevInput.length) {
        prevInput.focus().removeClass('filled').val('');
      }
    }
  });
  
  // Send OTP
  $('#sendOtpBtn, #resendOtpBtn').click(function(e) {
    e.preventDefault();
    const email = $('#email').val().trim();
    
    if (!email || !isValidEmail(email)) {
      showEmailStatus('Please enter a valid email address', 'danger');
      return;
    }
    
    $(this).prop('disabled', true).text('Sending...');
    
    $.post('php/send_otp.php', { email: email })
    .done(function(response) {
      if (response.success) {
        showEmailStatus(response.message, 'success');
        $('#otpSection').show();
        $('#email').prop('readonly', true);
        $('#sendOtpBtn').hide();
        startOtpTimer();
        $('.otp-input').val('').removeClass('filled');
        $('.otp-input').first().focus();
        
        // Development helper
        if (response.dev_note) {
          console.log('Development Mode: Use OTP 123456');
          showEmailStatus('📧 Email service not configured. Use OTP: 123456', 'info');
        }
      } else {
        showEmailStatus(response.message, 'danger');
      }
    })
    .fail(function() {
      showEmailStatus('Failed to send OTP', 'danger');
    })
    .always(function() {
      $('#sendOtpBtn, #resendOtpBtn').text('Send OTP').prop('disabled', false);
    });
  });
  
  function startOtpTimer() {
    let timeLeft = 30;
    $('#resendOtpBtn').hide();
    
    otpTimer = setInterval(function() {
      $('#timer').text(`Resend OTP in ${timeLeft}s`);
      timeLeft--;
      
      if (timeLeft < 0) {
        clearInterval(otpTimer);
        $('#timer').text('');
        $('#resendOtpBtn').show();
      }
    }, 1000);
  }
  
  // Verify OTP
  $('#verifyOtpBtn').click(function(e) {
    e.preventDefault();
    const email = $('#email').val().trim();
    let otp = '';
    
    $('.otp-input').each(function() {
      otp += $(this).val();
    });
    
    if (otp.length !== 6) {
      showOtpStatus('Please enter complete 6-digit OTP', 'danger');
      return;
    }
    
    $(this).prop('disabled', true).text('Verifying...');
    
    $.post('php/verify_otp.php', { email: email, otp: otp })
    .done(function(response) {
      if (response.success) {
        showOtpStatus('✅ Verified', 'success');
        emailVerified = true;
        $('.otp-input').prop('readonly', true);
        $('#verifyOtpBtn').text('Verified').prop('disabled', true);
        $('#registerBtn').prop('disabled', false);
        clearInterval(otpTimer);
        $('#timer').text('');
        $('#resendOtpBtn').hide();
      } else {
        showOtpStatus(response.message, 'danger');
        $('#verifyOtpBtn').text('Verify OTP').prop('disabled', false);
        $('.otp-input').val('').removeClass('filled');
        $('.otp-input').first().focus();
      }
    })
    .fail(function() {
      showOtpStatus('Verification failed', 'danger');
      $('#verifyOtpBtn').text('Verify OTP').prop('disabled', false);
    });
  });

  $("#registerForm").submit(function (e) {
    e.preventDefault();

    let username = $("#username").val().trim();
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();

    // Clear previous messages
    $("#message").empty();

    // Individual field validation
    if (!username) {
      showMessage("Username is required.", "danger");
      return;
    }

    if (!email) {
      showMessage("Email is required.", "danger");
      return;
    }

    if (!password) {
      showMessage("Password is required.", "danger");
      return;
    }

    if (!emailVerified) {
      showMessage("Please verify your email first.", "danger");
      return;
    }

    if (!isValidEmail(email)) {
      showMessage("Please enter a valid email address.", "danger");
      return;
    }

    if (password.length < 6) {
      showMessage("Password must be at least 6 characters long.", "danger");
      return;
    }

    // Generate CSRF token
    const csrfToken = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    
    $.ajax({
      url: "php/register.php",
      method: "POST",
      dataType: "json",
      data: {
        username: username,
        email: email,
        password: password,
        csrf_token: csrfToken,
      },
      success: function (response) {
        if (response.status === "success") {
          showMessage(
            "Registration successful! Redirecting to login...",
            "success"
          );
          setTimeout(function () {
            window.location.href = "login.html";
          }, 1500);
        } else {
          showMessage(response.message || "Registration failed", "danger");
        }
      },
      error: function (xhr) {
        console.error("Registration error:", xhr.responseText);
        showMessage("Server error. Please try again.", "danger");
      },
    });
  });

  function showMessage(message, type) {
    const escapedMessage = $('<div>').text(message).html();
    $("#message").html(`<div class="alert alert-${type}">${escapedMessage}</div>`);
  }
  
  function showEmailStatus(message, type) {
    const color = type === 'success' ? '#22c55e' : type === 'danger' ? '#ef4444' : '#8b5cf6';
    $('#emailStatus').html(`<small style="color: ${color}; font-size: 12px;">${message}</small>`);
  }
  
  function showOtpStatus(message, type) {
    const color = type === 'success' ? '#22c55e' : type === 'danger' ? '#ef4444' : '#8b5cf6';
    $('#otpStatus').html(`<small style="color: ${color}; font-size: 12px;">${message}</small>`);
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  // Password visibility on tap and hold
  $("#togglePassword")
    .on("mousedown touchstart", function (e) {
      e.preventDefault();
      $("#password").attr("type", "text");
      $(this).removeClass("fa-eye").addClass("fa-eye-slash");
    })
    .on("mouseup mouseleave touchend", function () {
      $("#password").attr("type", "password");
      $(this).removeClass("fa-eye-slash").addClass("fa-eye");
    });
});
