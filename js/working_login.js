$(document).ready(function () {
  $("#loginForm").submit(function (e) {
    e.preventDefault();
    
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();
    let $btn = $("#loginBtn");

    $("#message").empty();

    if (!email || !password) {
      showMessage("All fields are required.", "danger");
      return;
    }

    $btn.prop('disabled', true).html('Signing In...');

    $.ajax({
      url: "php/simple_file_login.php",
      method: "POST",
      dataType: "json",
      data: { email: email, password: password },
      success: function (data) {
        if (data.status === "success") {
          showMessage("Login successful! Redirecting...", "success");
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1000);
        } else {
          showMessage(data.message, "danger");
        }
      },
      error: function () {
        showMessage("Connection error. Please try again.", "danger");
      },
      complete: function() {
        $btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt me-2"></i>Sign In');
      }
    });
  });

  function showMessage(message, type) {
    $("#message").html(`<div class="alert alert-${type}">${message}</div>`);
  }
});