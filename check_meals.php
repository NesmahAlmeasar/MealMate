<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Meal;

echo "--- Meal Stats ---\n";
echo 'Total Meals: '.Meal::count()."\n";
echo "Meals by State:\n";
$states = Meal::select('state', \DB::raw('count(*) as total'))->groupBy('state')->get();

foreach ($states as $state) {
    echo ' - '.($state->state ?? 'NULL').': '.$state->total."\n";
}

echo "\n--- Sample Meal (first) ---\n";
$meal = Meal::first();
if ($meal) {
    echo 'ID: '.$meal->meals_id."\n";
    echo 'Name: '.$meal->name."\n";
    echo 'State: '.$meal->state."\n";
    echo 'Restaurant ID: '.$meal->restaurant_id."\n";
} else {
    echo "No meals found.\n";
}
