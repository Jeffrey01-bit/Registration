$(document).ready(function() {
    // Load profile data
    $.get('php/db_profile.php')
    .done(function(data) {
        if (data.status === 'success') {
            const user = data.user;
            
            // Display full name only if both first and last names exist
            const fullName = (user.first_name && user.last_name) ? `${user.first_name} ${user.last_name}` : '';
            
            $('#welcomeText').text(fullName ? `Welcome, ${fullName}` : `Welcome, ${user.username}`);
            $('#profileName').text(fullName || user.username);
            $('#profileEmail, #emailAddress').text(user.email);
            
            // Fill form fields
            $('#userId').val(user.id);
            $('#username').val(user.username);
            $('#firstName').val(user.first_name || '');
            $('#lastName').val(user.last_name || '');
            $('#email').val(user.email);
            $('#age').val(user.age || '');
            $('#dob').val(user.dob || '');
            $('#contact').val(user.contact || '');
            $('#gender').val(user.gender || '');
            $('#occupation').val(user.occupation || '');
            $('#address').val(user.address || '');
            $('#city').val(user.city || '');
            $('#state').val(user.state || '');
            $('#zipCode').val(user.zip_code || '');
            
            // Set initial readonly state
            $('.form-input, .form-select').prop('readonly', true).prop('disabled', true);
            $('.plus-icon').hide();
            
            // Load profile picture or show initials
            if (user.photo && user.photo.trim() !== '') {
                const img = new Image();
                img.onload = function() {
                    $('#headerAvatar').html(`<img src="${user.photo}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">`);
                    $('#profileAvatar').html(`<img src="${user.photo}" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">`);
                };
                img.onerror = function() {
                    showInitials(user.username);
                };
                img.src = user.photo;
            } else {
                showInitials(user.username);
            }
            
            function showInitials(username) {
                const initial = username.charAt(0).toUpperCase();
                $('#headerAvatar').html(`<div style="width: 40px; height: 40px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">${initial}</div>`);
                $('#profileAvatar').html(`<div style="width: 80px; height: 80px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px;">${initial}</div>`);
            }
        }
    })
    .fail(function() {
        window.location.href = 'login.html';
    });
    
    // Logout functionality
    $('#logoutBtn').click(function() {
        window.location.href = 'login.html';
    });
    
    // Edit/Save functionality
    let isEditing = false;
    $('#editBtn').click(function() {
        if (!isEditing) {
            $('.form-input, .form-select').prop('readonly', false).prop('disabled', false).addClass('editable');
            $('#userId').prop('readonly', true).removeClass('editable');
            $('.plus-icon').show();
            $(this).text('Save');
            isEditing = true;
        } else {
            const formData = {
                username: $('#username').val(),
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                age: $('#age').val(),
                dob: $('#dob').val(),
                contact: $('#contact').val(),
                gender: $('#gender').val(),
                occupation: $('#occupation').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                state: $('#state').val(),
                zipCode: $('#zipCode').val()
            };
            
            // Handle photo operations first
            if (photoAction === 'upload' && selectedFile) {
                const photoFormData = new FormData();
                photoFormData.append('photo', selectedFile);
                
                $.ajax({
                    url: 'php/test_upload.php',
                    method: 'POST',
                    data: photoFormData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Upload response:', response);
                        if (response.status === 'success') {
                            $('#headerAvatar').html(`<img src="${response.photo_path}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">`);
                        } else {
                            console.error('Upload failed:', response.message);
                        }
                        updateProfile();
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', error, xhr.responseText);
                        updateProfile();
                    }
                });
            } else if (photoAction === 'remove') {
                $.post('php/remove_photo.php')
                .always(function() {
                    const initial = $('#username').val().charAt(0).toUpperCase();
                    $('#headerAvatar').html(`<div style="width: 40px; height: 40px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">${initial}</div>`);
                    updateProfile();
                });
            } else {
                updateProfile();
            }
            
            function updateProfile() {
                $.post('php/update_profile.php', formData)
                .done(function(response) {
                    if (response.status === 'success') {
                        // Update display names only if both first and last names are provided
                        const fullName = (formData.firstName && formData.lastName) ? `${formData.firstName} ${formData.lastName}` : '';
                        
                        $('#welcomeText').text(fullName ? `Welcome, ${fullName}` : `Welcome, ${formData.username}`);
                        $('#profileName').text(fullName || formData.username);
                    }
                })
                .always(function() {
                    $('.form-input, .form-select').prop('readonly', true).prop('disabled', true).removeClass('editable');
                    $('.plus-icon').hide();
                    $('#editBtn').text('Edit');
                    isEditing = false;
                    selectedFile = null;
                    photoAction = null;
                });
            }
        }
    });
    
    // Photo upload functionality
    let selectedFile = null;
    let photoAction = null;
    
    $('#addPhotoBtn').click(function() {
        if (isEditing) {
            $('#photoInput').click();
        }
    });
    
    $('#photoInput').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            selectedFile = file;
            photoAction = 'upload';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profileAvatar').html(`<img src="${e.target.result}" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">`);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Photo hover for remove
    $('#profileAvatar').hover(
        function() {
            if (isEditing && $(this).find('img').length > 0) {
                $('#removePhotoBtn').show();
            }
        },
        function() {
            $('#removePhotoBtn').hide();
        }
    );
    
    // Remove photo
    $('#removePhotoBtn').click(function() {
        if (isEditing) {
            const initial = $('#username').val().charAt(0).toUpperCase();
            $('#profileAvatar').html(`<div style="width: 80px; height: 80px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px;">${initial}</div>`);
            $(this).hide();
            selectedFile = null;
            photoAction = 'remove';
        }
    });
    
    // Delete account functionality
    $('#deleteAccountBtn').click(function() {
        $('#deleteModal').show();
        $('body').css('overflow', 'hidden');
    });
    
    $('#cancelDelete').click(function() {
        $('#deleteModal').hide();
        $('body').css('overflow', 'auto');
    });
    
    $('#confirmDelete').click(function() {
        $.post('php/delete_account.php')
        .done(function(response) {
            if (response.status === 'success') {
                $('#deleteModal').hide();
                $('body').css('overflow', 'auto');
                window.location.href = 'login.html';
            }
        })
        .always(function() {
            $('#deleteModal').hide();
            $('body').css('overflow', 'auto');
            window.location.href = 'login.html';
        });
    });
    
    // Close modal when clicking outside
    $('#deleteModal').click(function(e) {
        if (e.target === this) {
            $(this).hide();
            $('body').css('overflow', 'auto');
        }
    });
});