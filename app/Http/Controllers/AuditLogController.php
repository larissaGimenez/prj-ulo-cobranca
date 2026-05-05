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
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->paginate(20);
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
            ->paginate(20);

        return view('admin.audit_logs.index', compact('logs'));
    }

    /**
     * Força o processamento da fila de logs pendentes no banco.
     */
    public function sync()
    {
        // Roda o worker apenas até a fila esvaziar
        \Illuminate\Support\Facades\Artisan::call('queue:work', [
            '--stop-when-empty' => true,
        ]);

        return redirect()->back()->with('success', 'Fila processada! Todos os logs pendentes foram registrados.');
    }
}
