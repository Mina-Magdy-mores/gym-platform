<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$memberRole = Role::firstOrCreate(['name' => 'member']);
$trainerRole = Role::firstOrCreate(['name' => 'trainer']);
$adminRole = Role::firstOrCreate(['name' => 'admin']);

$users = User::all();
foreach ($users as $user) {
    if (!$user->hasAnyRole(['admin', 'trainer', 'member'])) {
        $user->assignRole($memberRole);
        echo "Assigned 'member' role to user #{$user->id} ({$user->name})\n";
    }
}

echo "Role sync completed.\n";
