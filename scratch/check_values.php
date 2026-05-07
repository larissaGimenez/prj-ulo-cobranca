<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$results = \Illuminate\Support\Facades\DB::select("SELECT dados_titulo->>'valor_documento' as val FROM titulos_conta_receber LIMIT 10");
foreach($results as $r) {
    echo "Value: " . $r->val . "\n";
}
