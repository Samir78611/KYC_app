<!-- resources/views/emails/admin_invitation.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Admin Invitation</title>
</head>
<body>
    <h1>Hello {{ $name }},</h1>
    <p>You have been invited to join as an Admin.</p>
    <p>Please click the link below to set up your account:</p>
    <a href="{{ $invitationLink }}">Set Up Your Account</a>
    <p>Thank you!</p>
</body>
</html>
