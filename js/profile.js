// ===== PROFILE PAGE FUNCTIONALITY =====

class ProfileManager {
    constructor() {
        this.photoAction = null;
        this.selectedFile = null;
        this.isEditing = false;
        this.init();
    }

    // ===== INITIALIZATION =====
    init() {
        this.loadProfile();
        this.bindEvents();
    }

    // ===== EVENT BINDINGS =====
    bindEvents() {
        // Logout functionality
        $("#logoutBtn, #logoutDropdownBtn").on('click', () => {
            window.location.href = "login.html";
        });

        // Profile navigation
        $("#myProfileBtn").on('click', (e) => {
            e.preventDefault();
            window.scrollTo(0, 0);
        });

        // Dropdown functionality
        this.bindDropdownEvents();
        
        // Photo functionality
        this.bindPhotoEvents();
        
        // Edit functionality
        this.bindEditEvents();
    }

    // ===== DROPDOWN EVENTS =====
    bindDropdownEvents() {
        $("#headerAvatar").on('click', (e) => {
            e.stopPropagation();
            $(".dropdown-menu").toggleClass("show");
        });

        $(document).on('click', () => {
            $(".dropdown-menu").removeClass("show");
        });
    }

    // ===== PHOTO EVENTS =====
    bindPhotoEvents() {
        // Photo upload trigger
        $("#addPhotoBtn").on('click', () => {
            $("#photoInput").click();
        });

        // Photo selection
        $("#photoInput").on('change', (e) => {
            this.handlePhotoSelection(e.target.files[0]);
        });

        // Photo hover for remove
        $("#profileAvatar").hover(
            () => {
                if (this.isEditing && $("#profileAvatar").find('img').length > 0) {
                    $("#removePhotoBtn").show();
                }
            },
            () => {
                $("#removePhotoBtn").hide();
            }
        );

        // Photo removal
        $("#removePhotoBtn").on('click', () => {
            this.handlePhotoRemoval();
        });
    }

    // ===== EDIT EVENTS =====
    bindEditEvents() {
        $("#editBtn").on('click', () => {
            if (!this.isEditing) {
                this.enableEditMode();
            } else {
                this.saveProfile();
            }
        });
    }

    // ===== PROFILE LOADING =====
    loadProfile() {
        $.ajax({
            url: "php/db_profile.php",
            method: "GET",
            dataType: "json",
            success: (response) => {
                try {
                    if (response.status === "success") {
                        this.displayProfile(response.user);
                    } else {
                        console.log('Profile load failed:', response.message);
                        window.location.href = "login.html";
                    }
                } catch (e) {
                    console.error('Profile display error:', e);
                }
            },
            error: (xhr, status, error) => {
                console.error('AJAX error:', error, xhr.responseText);
                window.location.href = "login.html";
            }
        });
    }

    // ===== PROFILE DISPLAY =====
    displayProfile(user) {
        // Update header
        $("#welcomeText").text(`Welcome, ${user.username}`);
        $("#profileName").text(`${user.first_name} ${user.last_name}`.trim() || user.username);
        $("#profileEmail").text(user.email);
        $("#emailAddress").text(user.email);
        
        // Populate form fields
        this.populateFormFields(user);
        
        // Update avatars
        this.updateAvatars(user);
    }

    // ===== FORM POPULATION =====
    populateFormFields(user) {
        const fields = [
            'userId', 'username', 'firstName', 'lastName', 'email',
            'age', 'dob', 'contact', 'gender', 'occupation',
            'address', 'city', 'state', 'zipCode'
        ];

        fields.forEach(field => {
            const value = user[field === 'userId' ? 'id' : 
                              field === 'firstName' ? 'first_name' :
                              field === 'lastName' ? 'last_name' :
                              field === 'zipCode' ? 'zip_code' : field] || '';
            $(`#${field}`).val(value);
        });
    }

    // ===== AVATAR UPDATES =====
    updateAvatars(user) {
        if (user.photo && user.photo.trim() !== '') {
            this.loadPhotoWithFallback(user.photo, user.username);
        } else {
            this.showInitialAvatars(user.username);
        }
    }

    loadPhotoWithFallback(photoPath, username) {
        const img = new Image();
        img.onload = () => {
            $("#headerAvatar").html(`<img src="${photoPath}" alt="Profile" class="avatar-img">`);
            $("#profileAvatar").html(`<img src="${photoPath}" alt="Profile" class="avatar-img">`);
        };
        img.onerror = () => {
            this.showInitialAvatars(username);
        };
        img.src = photoPath;
    }

    showInitialAvatars(username) {
        const initial = username.charAt(0).toUpperCase();
        $("#headerAvatar").html(`<div style="width: 40px; height: 40px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">${initial}</div>`);
        $("#profileAvatar").html(`<div style="width: 80px; height: 80px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px;">${initial}</div>`);
    }

    // ===== PHOTO HANDLING =====
    handlePhotoSelection(file) {
        if (file) {
            this.selectedFile = file;
            this.photoAction = 'upload';
            
            const reader = new FileReader();
            reader.onload = (e) => {
                $("#profileAvatar").html(`<img src="${e.target.result}" alt="Profile" class="avatar-img">`);
            };
            reader.readAsDataURL(file);
        }
    }

    handlePhotoRemoval() {
        const initial = $("#username").val().charAt(0).toUpperCase();
        $("#profileAvatar").html(`<div style="width: 80px; height: 80px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px;">${initial}</div>`);
        $("#removePhotoBtn").hide();
        
        if (!this.selectedFile) {
            this.photoAction = 'remove';
        }
    }

    // ===== EDIT MODE =====
    enableEditMode() {
        $(".form-input, .form-select").addClass("editable");
        $(".plus-icon").show();
        $("#editBtn").text("Save");
        this.isEditing = true;
        this.photoAction = null;
        this.selectedFile = null;
    }

    // ===== SAVE PROFILE =====
    saveProfile() {
        const formData = this.collectFormData();
        
        if (this.photoAction === 'upload' && this.selectedFile) {
            this.uploadPhoto(() => this.updateProfile(formData));
        } else if (this.photoAction === 'remove') {
            this.removePhoto(() => this.updateProfile(formData));
        } else {
            this.updateProfile(formData);
        }
    }

    collectFormData() {
        return {
            username: $("#username").val(),
            firstName: $("#firstName").val(),
            lastName: $("#lastName").val(),
            age: $("#age").val(),
            dob: $("#dob").val(),
            contact: $("#contact").val(),
            gender: $("#gender").val(),
            occupation: $("#occupation").val(),
            address: $("#address").val(),
            city: $("#city").val(),
            state: $("#state").val(),
            zipCode: $("#zipCode").val()
        };
    }

    // ===== PHOTO OPERATIONS =====
    uploadPhoto(callback) {
        const photoFormData = new FormData();
        photoFormData.append('photo', this.selectedFile);
        
        $.ajax({
            url: 'php/upload_photo.php',
            method: 'POST',
            data: photoFormData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: (response) => {
                if (response.status === 'success') {
                    $("#headerAvatar").html(`<img src="${response.photo_path}" alt="Profile" class="avatar-img">`);
                }
                callback();
            },
            error: callback
        });
    }

    removePhoto(callback) {
        $.ajax({
            url: "php/remove_photo.php",
            method: "POST",
            dataType: "json",
            success: (response) => {
                if (response.status === 'success') {
                    const initial = $("#username").val().charAt(0).toUpperCase();
                    $("#headerAvatar").html(`<div style="width: 40px; height: 40px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">${initial}</div>`);
                }
                callback();
            },
            error: callback
        });
    }

    // ===== PROFILE UPDATE =====
    updateProfile(formData) {
        $.ajax({
            url: "php/update_profile.php",
            method: "POST",
            data: formData,
            dataType: "json",
            success: (response) => {
                if (response.status === 'success') {
                    this.exitEditMode();
                    this.updateDisplayName();
                }
            },
            error: () => {
                this.exitEditMode();
            }
        });
    }

    exitEditMode() {
        $(".form-input, .form-select").removeClass("editable");
        $(".plus-icon").hide();
        $("#editBtn").text("Edit");
        this.isEditing = false;
        this.photoAction = null;
        this.selectedFile = null;
    }

    updateDisplayName() {
        const fullName = `${$("#firstName").val()} ${$("#lastName").val()}`.trim();
        $("#profileName").text(fullName || $("#username").val());
    }
}

// ===== INITIALIZATION =====
$(document).ready(() => {
    new ProfileManager();
});