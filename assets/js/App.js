$(document).ready(function () {

    
    


    $("#frmClaim").submit(function (e) {
        e.preventDefault();
    
        var formData = new FormData(this);
        formData.append('requestType', 'ClaimPet');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
    
                if (response.status == "success") {
                    alertify.success('Update Saved');
                    setTimeout(function () {
                       location.reload();
                    }, 1000);
                } else {
                    alertify.error('Sending failed, please try again.');
                }
            }
        });
    });





    


    $("#frmAddImpoundPets").submit(function (e) {
        e.preventDefault();
    
        $('.spinner').show();
        $('#btnAddImpoundPets').prop('disabled', true);
    
        var formData = new FormData(this);
        formData.append('requestType', 'AddImpoundPets');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#btnAddImpoundPets').prop('disabled', false);
    
                if (response.status == "success") {
                    alertify.success('Impound Pet Successfully');
                    setTimeout(function () {
                        window.location.href = 'index.php?lgupages=impounded';


                    }, 1000);
                } else {
                    alertify.error('Sending failed, please try again.');
                }
            }
        });
    });






    $("#frmUpdatePetStatus button[type=submit]").click(function() {
        // Store the clicked button's value in a variable
        var statusValue = $(this).val();
        $("#frmUpdatePetStatus").data("status", statusValue);
    });
    
    $("#frmUpdatePetStatus").submit(function (e) {
        e.preventDefault();
    
        var formData = new FormData(this);
        formData.append('requestType', 'UpdatePetStatus');
    
        // Get the stored status value (Accept or Decline)
        var status = $("#frmUpdatePetStatus").data("status") || 'accept_by_vet';
        formData.append('status', status);
    
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#send-message').prop('disabled', false);
    
                if (response.status == "success") {
                    if (status === "accept_by_vet") {
                        alertify.success('Accepted Successfully');
                    } else {
                        alertify.success('Declined Successfully');
                    }
    
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
    
                } else {
                    alertify.error('Error');
                }
            }
        });
    });
    

   

    
    $("#frmSentMessagge").submit(function (e) {
        e.preventDefault();

        
        if ($("#reciever_id").val().trim() === "") {
            alertify.error('Select Receiver First');
            return;
        }

        if ($("#message-input").val().trim() === "" && $("#file-upload")[0].files.length === 0) {
            return;
        }
        

        $('.spinner').show();
        $('#send-message').prop('disabled', true);
    
        var formData = new FormData(this);
        formData.append('requestType', 'SentMessagge');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#send-message').prop('disabled', false);
    
                if (response.status == "success") {
                    // alertify.success('Sent Successfully');

                    $("#file-upload").val("");
                    $("#message-input").val("");
                } else {
                    alertify.error('Error');
                }
            }
        });
    });


    
    $("#frmDeletePost").submit(function (e) {
        e.preventDefault();
        $('.spinner').show();
        $('#confirmDeletePost').prop('disabled', true);
    
        var formData = new FormData(this);
        formData.append('requestType', 'DeletePost');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#confirmDeletePost').prop('disabled', false);
    
                if (response.status == "success") {
                    alertify.success('Deleted Successfully');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    alertify.error('Error');
                }
            }
        });
    });


    $("#frmEditPost").submit(function (e) {
        e.preventDefault();
    
        $('.spinner').show();
        $('#btnUpdatePost').prop('disabled', true);
    
        var formData = new FormData(this);
        formData.append('requestType', 'EditPost');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#btnUpdatePost').prop('disabled', false);
    
                if (response.status == "success") {
                    alertify.success('Update Successfully');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    alertify.error('Error');
                }
            }
        });
    });

    
    $("#frmUpdateProfile").submit(function (e) {
        e.preventDefault();
    
        $('.spinner').show();
        $('#btnUpdateProfile').prop('disabled', true);
    
        var formData = new FormData(this);
        formData.append('requestType', 'UpdateProfile');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#btnUpdateProfile').prop('disabled', false);
    
                if (response.status == "success") {
                    alertify.success('Update Successfully');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    alertify.error('Error');
                }
            }
        });
    });


    $("#petRegistrationForm").submit(function (e) {
        e.preventDefault();
    
        $('.spinner').show();
        $('#BtnRegistrationForm').prop('disabled', true);
    
        var formData = new FormData(this);
        formData.append('requestType', 'petRegistration');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#BtnRegistrationForm').prop('disabled', false);
    
                if (response.status == "success") {
                    alertify.success('Pet Registered Successfully');
                    setTimeout(function () {
                        window.location.href = 'index.php?page=MyPets';


                    }, 2000);
                } else {
                    alertify.error('Sending failed, please try again.');
                }
            }
        });
    });
    




    $("#frmPOST_CONTENT").submit(function (e) {
        e.preventDefault();
    
        $('.spinner').show();
        $('#btnPOSTCONTENT').prop('disabled', true);
    
        var formData = new FormData(this);
        formData.append('requestType', 'PostContent');
        
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.spinner').hide();
                $('#btnPOSTCONTENT').prop('disabled', false);
    
                if (response.status == "success") {
                    alertify.success('Posted Successfully');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    alertify.error('Sending failed, please try again.');
                }
            }
        });
    });
    




    









    $("#FrmRegister").submit(function (e) {
        e.preventDefault();
    
        let selectedRole = $("input[name='role']:checked").val();
        let formData = new FormData(this); // Use FormData to handle file uploads
    
        if (selectedRole === "vet") {
            let licenseFile = $("#vet-id-upload")[0]?.files[0]; // Get the file
    
            if (!licenseFile) {
                alertify.error('Please upload your vet ID.');
                return;
            }
    
            formData.append("vet_license_id", licenseFile); // Append file to FormData
        }
    
        let password = $("#password").val();
    
        function validatePassword(password) {
            let messages = [];
    
            if (!/(?=.*[a-z])/.test(password)) {
                messages.push("Password must contain at least one lowercase letter.");
            }
            if (!/(?=.*[A-Z])/.test(password)) {
                messages.push("Password must contain at least one uppercase letter.");
            }
            if (!/(?=.*\d)/.test(password)) {
                messages.push("Password must contain at least one number.");
            }
            if (!/(?=.*[@$!%*?&])/.test(password)) {
                messages.push("Password must contain at least one special character.");
            }
            if (password.length < 8) {
                messages.push("Password must be at least 8 characters long.");
            }
    
            return messages.length ? messages.join(" ") : null;
        }
    
        let passwordValidationResult = validatePassword(password);
        if (passwordValidationResult) {
            alertify.error(passwordValidationResult);
            return;
        }
    
        $('.spinner').show();
        $('#btnRegister').prop('disabled', true);
    
        formData.append('requestType', 'Signup'); // Ensure request type is included
    
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: formData,
            processData: false,  // Prevent jQuery from converting FormData into a query string
            contentType: false,  // Let the browser set the correct multipart/form-data content type
            success: function (response) {
                try {
                    let data = JSON.parse(response);
    
                    if (data.status === "success") {
                        alertify.success('Registration Successful');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        alertify.error(data.message || 'Registration failed, please try again.');
                        $('.spinner').hide();
                        $('#btnRegister').prop('disabled', false);
                    }
                } catch (error) {
                    alertify.error("An error occurred while processing the request.");
                    $('.spinner').hide();
                    $('#btnRegister').prop('disabled', false);
                }
            },
            error: function () {
                alertify.error("Failed to connect to the server.");
                $('.spinner').hide();
                $('#btnRegister').prop('disabled', false);
            }
        });
    });
    
    









    $("#frmLogin").submit(function (e) {
        e.preventDefault();
    
        $('.spinner').show();
        $('#btnLogin').prop('disabled', true);
        
        var formData = $(this).serializeArray(); 
        formData.push({ name: 'requestType', value: 'Login' });
        var serializedData = $.param(formData);
    
        // Perform the AJAX request
        $.ajax({
            type: "POST",
            url: "api/config/end-points/controller.php",
            data: serializedData,  
            success: function (response) {
                console.log(response);
                var data = JSON.parse(response);
    
                if (data.status === "success") {
                    alertify.success('Login Successful');
    
                    
                    setTimeout(function() {
                        if (data.data.Role === "pet_owner") {
                            window.location.href = "index.php?page=home";
                        }else if (data.data.Role === "vet") {
                            window.location.href = "index.php?vetpages=VetHome";
                        }else if (data.data.Role === "lgu") {
                            window.location.href = "index.php?lgupages=LGUHome";
                        }else if (data.data.Role === "admin") {
                            window.location.href = "index.php?page=home";
                        }
                    }, 2000);  
    
                }else if(data.status === "error"){
                  console.log(data)
                  $('.spinner').hide();
                  $('#btnLogin').prop('disabled', false);
                  alertify.error(data.message);
    
                } else {
                    $('.spinner').hide();
                    $('#btnLogin').prop('disabled', false);
                    console.error(response); 
                    alertify.error('Registration failed, please try again.');
                }
            },
            error: function () {
                $('.spinner').hide();
                $('#btnLoginStudent').prop('disabled', false);
                alertify.error('An error occurred. Please try again.');
            }
        });
    });
    
      
      
      
      
});