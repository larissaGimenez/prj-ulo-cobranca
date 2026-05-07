<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \Illuminate\Support\Facades\DB::table('titulos_conta_receber')
    ->whereRaw("dados_titulo->>'valor_documento' IS NULL")
    ->count();

echo "Missing: " . $count . "\n";
