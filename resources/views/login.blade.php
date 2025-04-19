<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- jQuery CDN for easy AJAX handling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Basic styles for body and page layout */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        /* Login form container styles */
        .login-container {
            background-color: #fff;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }

        /* Title and input field styles */
        h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #4e73df;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #4e73df;
            outline: none;
        }

        /* Button and hover effects */
        button {
            width: 100%;
            padding: 12px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2e59d9;
        }

        /* Social media login button styles */
        .social-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            text-decoration: none;
            color: white;
            text-transform: uppercase;
            transition: opacity 0.3s;
        }

        .google-btn { background-color: #db4437; }
        .facebook-btn { background-color: #3b5998; }
        .social-btn:hover { opacity: 0.85; }

        /* Message styling for error and success messages */
        .error-message {
            margin-bottom: 10px;
            font-size: 14px;
            display: block;
            color: red;
        }

        .success-message {
            margin-bottom: 10px;
            font-size: 14px;
            display: block;
            color: green;
        }

        /* Register link styling */
        .register-link {
            margin-top: 10px;
            font-size: 14px;
        }

        /* Responsive adjustments for smaller screen sizes */
        @media (max-width: 500px) {
            .login-container { padding: 20px; }
            h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Logo of the site -->
        <img src="{{ asset('a.png') }}" alt="Logo" class="logo">

        <!-- Login Heading -->
        <h2>Login</h2>

        <!-- Error/Sucess message area -->
        <p id="responseMessage" class="error-message"></p>

        <!-- Login Form -->
        <form id="loginForm" autocomplete="off">
            <input type="email" id="email" name="email" placeholder="Email" required autocomplete="off">
            <input type="password" id="password" name="password" placeholder="Password" required autocomplete="new-password">
            <button type="submit">Login</button>
        </form>

        <div style="margin-top: 15px;">
            <p>OR</p>
        </div>

        <!-- Google Login button -->
        <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="social-btn google-btn">Login with Google</a>

        <!-- Facebook Login button -->
        <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="social-btn facebook-btn">Login with Facebook</a>

        <!-- Register link for users who don't have an account -->
        <p class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </p>
    </div>

  <!-- DITO LANG MAY CHANGES SA ACTIVITY 5 -->
    
    <script>
    $(document).ready(function() {
        // AJAX login form submission
        $('#loginForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            // Get values from the input fields
            const email = $('#email').val();
            const password = $('#password').val();

            // AJAX request to send the form data
            $.ajax({
                url: "{{ route('ajax.login') }}", // Server-side route for login
                type: "POST", // Request type
                data: {
                    email: email,
                    password: password,
                    _token: "{{ csrf_token() }}" // CSRF token to protect the form submission
                },
                success: function(response) {
                    // On successful login
                    $('#responseMessage').text(response.message)  // Display success message
                        .removeClass('error-message')  // Remove error class
                        .addClass('success-message'); // Add success class

                    // Redirect user to a different page if provided
                    if (response.redirect) {
                        setTimeout(() => { window.location.href = response.redirect; }, 1000); // Redirect after 1 second
                    }
                },
                error: function(xhr)
                 {  //ADDED
                    console.log(xhr.responseText); // Log the response text for debugging 

                    let errorMessage = "Something went wrong!"; // Default error message

                    // Check if the server response contains a message
                    if (xhr.responseJSON) // ITO NA CHANGE
                     {
                         //ADDED
                        errorMessage = xhr.responseJSON.message || xhr.responseJSON.error || errorMessage;
                    }
                     //ADDED

                    // Handle 429 (rate limit) error
                    if (xhr.status === 429 && xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message; // Use the message from the server for rate limit
                    }

                    // Display the error message
                    $('#responseMessage').text(errorMessage)
                        .removeClass('success-message')  // Remove success class
                        .addClass('error-message'); // Add error class
                }
            });
        });
    });
</script>
