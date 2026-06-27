<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$admin = User::where('email', 'admin@kampusresik.id')->first();
if ($admin) {
    $admin->password = Hash::make('admin123');
    $admin->save();
    echo "Admin password reset successfully.\n";
} else {
    echo "Admin user not found.\n";
}

$petugas = User::where('email', 'ahmad@kampusresik.id')->first();
if ($petugas) {
    $petugas->password = Hash::make('petugas123');
    $petugas->save();
    echo "Petugas password reset successfully.\n";
} else {
    echo "Petugas user not found.\n";
}
