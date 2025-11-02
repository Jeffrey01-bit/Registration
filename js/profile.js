$(document).ready(function () {
  loadProfile();

  $("#logoutBtn").click(function () {
    localStorage.removeItem('sessionToken');
    localStorage.removeItem('user');
    window.location.href = "index.html";
  });

  $(document).on('submit', '#profileForm', function (e) {
    e.preventDefault();
    
    const token = localStorage.getItem('sessionToken');
    const firstName = $("#firstName").val() || '';
    const lastName = $("#lastName").val() || '';
    const age = $("#age").val() || null;
    const dob = $("#dob").val() || '';
    const gender = $("#gender").val() || '';
    const contact = $("#contact").val() || '';
    const address = $("#address").val() || '';
    const city = $("#city").val() || '';
    const state = $("#state").val() || '';
    const zipCode = $("#zipCode").val() || '';
    const occupation = $("#occupation").val() || '';
    const company = $("#company").val() || '';
    
    console.log('Form data:', {
      firstName, lastName, age, dob, gender, contact, 
      address, city, state, zipCode, occupation, company
    });
    
    $.ajax({
      url: "php/basic_profile.php",
      method: "POST",
      dataType: "json",
      data: { 
        token: token, firstName: firstName, lastName: lastName, age: age, 
        dob: dob, gender: gender, contact: contact, address: address, 
        city: city, state: state, zipCode: zipCode, occupation: occupation, company: company 
      },
      success: function (response) {
        console.log('Profile update response:', response);
        if (response.status === "success") {
          // Upload photo if one was selected
          const photoFile = $("#photo")[0].files[0];
          if (photoFile) {
            const formData = new FormData();
            formData.append('photo', photoFile);
            formData.append('token', localStorage.getItem('sessionToken'));
            
            $.ajax({
              url: 'php/upload_photo.php',
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              success: function(photoResponse) {
                if (photoResponse.status === 'success') {
                  showMessage("Profile and photo updated successfully!", "success");
                } else {
                  showMessage("Profile updated, but photo upload failed", "warning");
                }
                loadProfile();
                $("#updateProfileModal").fadeOut();
              },
              error: function() {
                showMessage("Profile updated, but photo upload failed", "warning");
                loadProfile();
                $("#updateProfileModal").fadeOut();
              }
            });
          } else {
            showMessage("Profile updated successfully!", "success");
            loadProfile();
            $("#updateProfileModal").fadeOut();
          }
        } else {
          showMessage(response.message || "Update failed", "danger");
        }
      },
      error: function () {
        showMessage("Server error. Please try later.", "danger");
      }
    });
  });

  function loadProfile() {
    const token = localStorage.getItem('sessionToken');
    const user = localStorage.getItem('user');
    
    if (!token || !user) {
      window.location.href = "login.html";
      return;
    }
    
    // Use stored user data instead of making AJAX call
    try {
      const userData = JSON.parse(user);
      displayProfile(userData);
    } catch (e) {
      window.location.href = "login.html";
    }
  }

  function displayProfile(user) {
    const safeUsername = $('<div>').text(user.username || '').html();
    const safeEmail = $('<div>').text(user.email || '').html();
    const safeFirstName = $('<div>').text(user.first_name || '').html();
    const safeLastName = $('<div>').text(user.last_name || '').html();
    const safeContact = $('<div>').text(user.contact || '').html();
    const safeCity = $('<div>').text(user.city || '').html();
    const safeGender = $('<div>').text(user.gender || '').html();
    const safeDob = $('<div>').text(user.dob || '').html();
    const safeOccupation = $('<div>').text(user.occupation || '').html();
    const safeCompany = $('<div>').text(user.company || '').html();
    const safePhoto = $('<div>').text(user.photo || '').html();
    
    const profileImage = user.photo ? 
      `<img src="uploads/${safePhoto}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">` :
      `<div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px; font-size: 3rem;">${safeUsername.charAt(0).toUpperCase()}</div>`;
    
    // Left sidebar with image
    $("#profileImageSection").html(`
      ${profileImage}
      <h5 class="mt-3 mb-1">${(safeFirstName + ' ' + safeLastName).trim() || safeUsername}</h5>
      <p class="text-muted small mb-0">${safeOccupation || 'User'}</p>
      <p class="text-muted small">${safeCity || 'Location not set'}</p>
    `);
    
    // Right section with details
    $("#userInfo").html(`
      <div class="mb-4">
        <h6 class="text-success mb-3">Contact Information</h6>
        <div class="row mb-2">
          <div class="col-3"><strong>Phone:</strong></div>
          <div class="col-9">${safeContact || 'Not provided'}</div>
        </div>
        <div class="row mb-2">
          <div class="col-3"><strong>Email:</strong></div>
          <div class="col-9">${safeEmail}</div>
        </div>
        <div class="row mb-2">
          <div class="col-3"><strong>Address:</strong></div>
          <div class="col-9">${user.address || 'Not provided'}</div>
        </div>
      </div>
      
      <div class="mb-4">
        <h6 class="text-success mb-3">Basic Information</h6>
        <div class="row mb-2">
          <div class="col-3"><strong>Birthday:</strong></div>
          <div class="col-9">${safeDob || 'Not provided'}</div>
        </div>
        <div class="row mb-2">
          <div class="col-3"><strong>Gender:</strong></div>
          <div class="col-9">${safeGender || 'Not provided'}</div>
        </div>
        <div class="row mb-2">
          <div class="col-3"><strong>Age:</strong></div>
          <div class="col-9">${user.age || 'Not provided'}</div>
        </div>
      </div>
      
      <div class="mb-4">
        <h6 class="text-success mb-3">Professional</h6>
        <div class="row mb-2">
          <div class="col-3"><strong>Occupation:</strong></div>
          <div class="col-9">${safeOccupation || 'Not provided'}</div>
        </div>
        <div class="row mb-2">
          <div class="col-3"><strong>Company:</strong></div>
          <div class="col-9">${safeCompany || 'Not provided'}</div>
        </div>
        <div class="text-start mt-5">
          <button id="showUpdateForm" class="btn btn-success">Update Profile</button>
        </div>
      </div>
    `);
    
    if (user.first_name) $("#firstName").val(user.first_name);
    if (user.last_name) $("#lastName").val(user.last_name);
    if (user.age) $("#age").val(user.age);
    if (user.dob) $("#dob").val(user.dob);
    if (user.gender) $("#gender").val(user.gender);
    if (user.contact) $("#contact").val(user.contact);
    if (user.address) $("#address").val(user.address);
    if (user.city) $("#city").val(user.city);
    if (user.state) $("#state").val(user.state);
    if (user.zip_code) $("#zipCode").val(user.zip_code);
    if (user.occupation) $("#occupation").val(user.occupation);
    if (user.company) $("#company").val(user.company);
    
    if (user.photo) {
      $("#photoPreview").html(`<img src="uploads/${user.photo}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`);
    }
  }
  
  // Photo upload preview
  $("#photo").change(function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        $("#photoPreview").html(`<img src="${e.target.result}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`);
      };
      reader.readAsDataURL(file);
      
      // Just show preview, don't upload yet
      // Photo will be uploaded when form is submitted
    }
  });

  // Show/hide modal (using event delegation for dynamically created button)
  $(document).on('click', '#showUpdateForm', function() {
    $("#updateProfileModal").fadeIn();
  });

  $("#discardBtn").click(function() {
    $("#updateProfileModal").fadeOut();
    $("#message").empty();
  });

  // Close modal when clicking outside
  $("#updateProfileModal").click(function(e) {
    if (e.target === this) {
      $(this).fadeOut();
      $("#message").empty();
    }
  });

  function showMessage(message, type) {
    $("#message").html(`<div class="alert alert-${type}">${message}</div>`);
  }
});