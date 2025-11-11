$(document).ready(function() {
    // Set current date
    const today = new Date();
    const options = { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' };
    $('#currentDate').text(today.toLocaleDateString('en-US', options));
    
    // Load profile data
    const token = localStorage.getItem('session_token');
    console.log('Session token:', token);
    if (!token) {
        console.log('No session token found');
        window.location.href = 'login.html';
        return;
    }
    
    $.get('php/profile.php', { token: token })
    .done(function(data) {
        console.log('Profile data received:', data);
        if (data.status === 'success') {
            const user = data.user;
            console.log('User data:', user);
            
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
            
            // Load profile picture
            loadUserPhoto(user.username);
            
            function loadUserPhoto(username) {
                $.get('php/photo.php', { token: token })
                .done(function(response) {
                    if (response.status === 'success' && response.photo) {
                        const photoUrl = response.photo;
                        $('#headerAvatar').html(`<img src="${photoUrl}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">`);
                        $('#profileAvatar').html(`<img src="${photoUrl}" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">`);
                    } else {
                        showInitials(username);
                    }
                })
                .fail(function() {
                    showInitials(username);
                });
            }
            
            function showInitials(username) {
                const initial = username.charAt(0).toUpperCase();
                $('#headerAvatar').html(`<div style="width: 40px; height: 40px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">${initial}</div>`);
                $('#profileAvatar').html(`<div style="width: 80px; height: 80px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px;">${initial}</div>`);
            }
        } else {
            console.error('Profile load failed:', data.message);
            if (data.message === 'Invalid session') {
                localStorage.removeItem('session_token');
                localStorage.removeItem('user');
                window.location.href = 'login.html';
            }
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Profile request failed:', error, xhr.responseText);
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
            
            // Handle photo upload
            if (photoAction === 'upload' && selectedFile) {
                const photoFormData = new FormData();
                photoFormData.append('photo', selectedFile);
                photoFormData.append('token', localStorage.getItem('session_token'));
                
                $.ajax({
                    url: 'php/photo.php',
                    method: 'POST',
                    data: photoFormData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Photo upload response:', response);
                        if (response.status === 'success') {
                            const photoPath = response.photo_path;
                            $('#headerAvatar').html(`<img src="${photoPath}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">`);
                            $('#profileAvatar').html(`<img src="${photoPath}" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">`);
                            
                            if (response.mongo_saved) {
                                console.log('✅ Photo saved to MongoDB');
                            } else {
                                console.log('⚠️ Photo saved to file only');
                            }
                        }
                        updateProfile();
                    },
                    error: function() {
                        updateProfile();
                    }
                });
            } else if (photoAction === 'remove') {
                // Remove photo from database
                $.post('php/photo.php', { 
                    token: localStorage.getItem('session_token'),
                    remove: true
                })
                .done(function(response) {
                    console.log('Photo remove response:', response);
                    if (response.mongo_removed) {
                        console.log('✅ Photo removed from MongoDB');
                    }
                })
                .always(function() {
                    const initial = $('#username').val().charAt(0).toUpperCase();
                    $('#headerAvatar').html(`<div style="width: 40px; height: 40px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">${initial}</div>`);
                    $('#profileAvatar').html(`<div style="width: 80px; height: 80px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px;">${initial}</div>`);
                    updateProfile();
                });
            } else {
                updateProfile();
            }
            
            function updateProfile() {
                const token = localStorage.getItem('session_token');
                const updateData = Object.assign({}, formData, { token: token });
                $.post('php/profile.php', updateData)
                .done(function(response) {
                    if (response.status === 'success') {
                        // Update display names only if both first and last names are provided
                        const fullName = (formData.firstName && formData.lastName) ? `${formData.firstName} ${formData.lastName}` : '';
                        
                        const escapedFullName = $('<div>').text(fullName).html();
                        const escapedUsername = $('<div>').text(formData.username).html();
                        $('#welcomeText').text(fullName ? `Welcome, ${escapedFullName}` : `Welcome, ${escapedUsername}`);
                        $('#profileName').text(escapedFullName || escapedUsername);
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
        $.post('php/delete_account.php', {
            token: localStorage.getItem('session_token')
        })
        .done(function(response) {
            console.log('Delete response:', response);
            if (response.status === 'success') {
                localStorage.removeItem('session_token');
                localStorage.removeItem('user');
                $('#deleteModal').hide();
                $('body').css('overflow', 'auto');
                window.location.href = 'login.html';
            }
        })
        .always(function() {
            localStorage.removeItem('session_token');
            localStorage.removeItem('user');
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