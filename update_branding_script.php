<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use App\Models\LandingPageSetting;

// Update Logo
LandingPageSetting::set('logo', 'IMG/logo_new.png');
echo "Logo updated to IMG/logo_new.png\n";

// Update Colors (Teal theme based on logo)
Setting::set('theme_primary_color', '#139bb7');
Setting::set('theme_secondary_color', '#0f7685');
Setting::set('theme_accent_color', '#1ac8db');
echo "Theme colors updated to Teal palette (#139bb7)\n";

// Clear Cache to ensure changes take effect immediately
\Illuminate\Support\Facades\Cache::flush();
echo "Cache cleared.\n";
