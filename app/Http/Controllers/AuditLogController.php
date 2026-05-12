<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Lista todos os logs de auditoria
     */
    public function index()
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.audit_logs.index', compact('logs'));
    }

    /**
     * Exemplo de busca granulada no JSONB
     */
    public function searchByAttribute(Request $request)
    {
        $attribute = $request->attribute; // ex: 'status'
        
        // Sintaxe específica do PostgreSQL para buscar chaves dentro de um JSONB
        $logs = AuditLog::whereRaw("payload->'new' ?? ?", [$attribute])
            ->orWhereRaw("payload->'old' ?? ?", [$attribute])
            ->paginate(10);

        return view('admin.audit_logs.index', compact('logs'));
    }

    /**
     * Inicia o processamento da fila de logs em segundo plano.
     * Não bloqueia a request HTTP (corrige CRITICAL-04 do Audit).
     */
    public function sync()
    {
        $phpBinary = PHP_BINARY ?: 'php';
        $artisanPath = base_path('artisan');
        $command = "\"{$phpBinary}\" \"{$artisanPath}\" queue:work --stop-when-empty --max-jobs=100";

        // Spawn não-bloqueante: funciona em Windows e Linux
        if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
            pclose(popen("start /B {$command} > NUL 2>&1", 'r'));
        } else {
            exec("{$command} > /dev/null 2>&1 &");
        }

        return redirect()->back()->with('success', 'Processamento da fila iniciado em segundo plano! Os logs serão registrados em instantes.');
    }
}
