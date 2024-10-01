<!DOCTYPE html>
<html>
<head>
    <title>Account Setup</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>

    <p>Your submission has been accepted, and we have created an account for you.</p>
    <p>Please click the link below to set up your password:</p>

    <a href="{{ $setupLink }}">Set Up Your Password</a>

    <p>If you did not request this, please ignore this email.</p>

    <p>Thank you,</p>
    <p>Your Team</p>
</body>
</html>
