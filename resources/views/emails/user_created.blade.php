<!DOCTYPE html>
<html>
<head>
    <title>User Created</title>
</head>
<body>
    <h1>Welcome, {{ $user->first_name }} {{ $user->last_name }}!</h1>
    <p>Thank you for registering. Your account has been created successfully.</p>
    <p>Here are your details:</p>
    <ul>
        <li>Email: {{ $user->email }}</li>
     
    </ul>
    <p>Regards,<br>Team</p>
</body>
</html>
