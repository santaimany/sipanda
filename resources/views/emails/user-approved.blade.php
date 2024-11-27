<!DOCTYPE html>
<html>
<head>
    <title>Account Approved</title>
</head>
<body>
    <h1>Hi, {{ $userName }}</h1>
    <p>Your account has been approved!</p>
    <p>Below is your license key QR Code:</p>
    <img src="{{ $qrCodePath }}" alt="QR Code" />
    <p>Please keep it safe.</p>
    <p>Thank you!</p>
</body>
</html>
