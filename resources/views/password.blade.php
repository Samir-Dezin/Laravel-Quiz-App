<!DOCTYPE html>
<html>
<head>
    <title>Password Setup</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>Click the link below to set up your password:</p>
    
    <a href="{{ url('password/setup?token=' . $token . '&email=' . $user->email) }}">Set up your password</a>
</body>
</html>
