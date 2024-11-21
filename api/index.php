<?php
 
// Load the Laravel application
<<<<<<< HEAD
require __DIR__ . '/../public/index.php';
=======
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
 
// Run the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
>>>>>>> cccfcb1 (register FIX BANGET, penambahan config vercel, dan mengganti env menjadi database server)
