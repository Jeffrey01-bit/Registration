$(document).ready(function() {
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
        
        const email = $('#email').val();
        const password = $('#password').val();
        
        if (!email || !password) {
            alert('Please fill in all fields');
            return false;
        }
        
        $.ajax({
            url: 'php/simple_login.php',
            type: 'POST',
            data: {
                username: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    localStorage.setItem('session_token', response.token);
                    localStorage.setItem('user', JSON.stringify(response.user));
                    window.location.href = 'profile.html';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error details:', xhr.responseText);
                alert('Connection error. Please check if XAMPP is running.');
            }
        });
        
        return false;
    });
});