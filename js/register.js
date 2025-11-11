$(document).ready(function () {
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
