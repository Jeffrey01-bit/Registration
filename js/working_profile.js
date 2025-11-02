$(document).ready(function () {
  loadProfile();

  $("#logoutBtn").click(function () {
    window.location.href = "php/logout.php";
  });

  function loadProfile() {
    $.ajax({
      url: "php/working_profile.php",
      method: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          displayProfile(response.user);
        } else {
          window.location.href = "login.html";
        }
      },
      error: function () {
        window.location.href = "login.html";
      }
    });
  }

  function displayProfile(user) {
    const profileImage = `<div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px; font-size: 3rem;">${user.username.charAt(0).toUpperCase()}</div>`;
    
    $("#profileImageSection").html(`
      ${profileImage}
      <h5 class="mt-3 mb-1">${user.first_name} ${user.last_name}</h5>
      <p class="text-muted small mb-0">${user.occupation}</p>
      <p class="text-muted small">${user.city}</p>
    `);
    
    $("#userInfo").html(`
      <div class="mb-4">
        <h6 class="text-success mb-3">Contact Information</h6>
        <div class="row mb-2">
          <div class="col-3"><strong>Phone:</strong></div>
          <div class="col-9">${user.contact}</div>
        </div>
        <div class="row mb-2">
          <div class="col-3"><strong>Email:</strong></div>
          <div class="col-9">${user.email}</div>
        </div>
      </div>
      
      <div class="mb-4">
        <h6 class="text-success mb-3">Basic Information</h6>
        <div class="row mb-2">
          <div class="col-3"><strong>Age:</strong></div>
          <div class="col-9">${user.age}</div>
        </div>
        <div class="row mb-2">
          <div class="col-3"><strong>Username:</strong></div>
          <div class="col-9">${user.username}</div>
        </div>
      </div>
    `);
  }
});