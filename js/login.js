$(document).ready(function() {
    // Generate CSRF token
    const csrfToken = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    
    // Triple prevention for form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    });
    
    // Handle button click
    $('#loginBtn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const email = $('#email').val().trim();
        const password = $('#password').val();
        
        if (!email || !password) {
            showMessage('Please fill in all fields', 'danger');
            return false;
        }
        
        // Basic email validation
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showMessage('Please enter a valid email address', 'danger');
            return false;
        }
        
        $.ajax({
            url: 'php/login.php',
            type: 'POST',
            data: {
                username: email,
                password: password,
                csrf_token: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    localStorage.setItem('session_token', response.token);
                    localStorage.setItem('user', JSON.stringify(response.user));
                    window.location.href = 'profile.html';
                } else {
                    showMessage(response.message || 'Login failed', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Login error:', error);
                showMessage('Connection error. Please check if the server is running.', 'danger');
            }
        });
        
        return false;
    });
    
    function showMessage(message, type) {
        const escapedMessage = $('<div>').text(message).html();
        $('#message').html(`<div class="alert alert-${type}">${escapedMessage}</div>`);
    }
});