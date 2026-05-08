<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$results = DB::select("SELECT status, COUNT(*) as count, SUM(valor) as total FROM titulos_conta_receber GROUP BY status");

foreach($results as $r) {
    echo "Status: " . ($r->status ?? 'NULL') . " | Count: " . $r->count . " | Total: " . $r->total . "\n";
}
