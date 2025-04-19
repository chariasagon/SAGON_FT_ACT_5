<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div id="message"></div>

    <form id="registrationForm">
        @csrf
        <label>Username: <input type="text" name="username" required></label><br>
        <label>First Name: <input type="text" name="first_name" required></label><br>
        <label>Last Name: <input type="text" name="last_name" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <label>Retype Password: <input type="password" name="retype_password" required></label><br>

        <button type="submit">Register</button>
    </form>

    <!-- Link to Login page (Visible only if user is not logged in) -->
    <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>

    <script>
        $(document).ready(function() {
            $('#registrationForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("register") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#message').html('<p style="color: green;">' + response.success + '</p>');
                        $('#registrationForm')[0].reset();

                        // Redirect to login page after successful registration
                        window.location.href = "{{ route('login') }}";
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            $('#message').html('<p style="color: red;">' + xhr.responseJSON.error + '</p>');
                        } else {
                            $('#message').html('<p style="color: red;">An error occurred. Please try again.</p>');
                        }
                    }
                });
            });
        });
    </script>

</body>

</html>
