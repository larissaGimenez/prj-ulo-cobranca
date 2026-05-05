@extends('layouts.app')

@section('content')
<div class="mb-5 d-flex justify-content-between align-items-end">
    <div>
        <h2 class="mb-2">Logs de Auditoria</h2>
        <h5 class="text-body-tertiary fw-semibold">Rastreamento completo de alterações e eventos do sistema.</h5>
    </div>
    
    <form action="{{ route('admin.audit_logs.sync') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary d-flex align-items-center">
            <span class="fas fa-sync-alt me-2"></span>Sincronizar Logs Pendentes
        </button>
    </form>
</div>

<div class="card shadow-none border border-300">
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-sm fs-9 mb-0">
                <thead class="bg-body-emphasis">
                    <tr>
                        <th class="ps-3" style="width: 15%">Data/Hora</th>
                        <th style="width: 10%">Usuário</th>
                        <th style="width: 10%">Evento</th>
                        <th style="width: 20%">Entidade</th>
                        <th style="width: 35%">Mudanças</th>
                        <th class="text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @forelse($logs as $log)
                        <tr class="align-middle">
                            <td class="ps-3 py-3 text-body-tertiary">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <span class="fw-bold">{{ $log->user->name ?? 'Sistema' }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($log->event_type) {
                                        'created' => 'badge-phoenix-success',
                                        'updated' => 'badge-phoenix-warning',
                                        'deleted' => 'badge-phoenix-danger',
                                        default => 'badge-phoenix-info'
                                    };
                                @endphp
                                <span class="badge badge-phoenix fs-10 {{ $badgeClass }}">{{ strtoupper($log->event_type) }}</span>
                            </td>
                            <td class="text-body-highlight fw-semibold">
                                {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                            </td>
                            <td>
                                @if($log->event_type === 'updated')
                                    <small class="text-body-tertiary">
                                        Alterou {{ count($log->payload['new'] ?? []) }} campos.
                                    </small>
                                @else
                                    <small class="text-body-tertiary">Registro completo no payload.</small>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-phoenix-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalLog{{ $log->id }}">
                                    Ver Detalhes
                                </button>
                            </td>
                        </tr>

                        <!-- Modal de Detalhes -->
                        <div class="modal fade" id="modalLog{{ $log->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-bottom-0">
                                        <h5 class="modal-title">Detalhes do Log #{{ $log->id }}</h5>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body bg-body-highlight p-4">
                                        <div class="row g-3 mb-4">
                                            <div class="col-6">
                                                <label class="fs-11 fw-bold text-uppercase text-body-tertiary">IP Address</label>
                                                <p class="mb-0 fw-semibold">{{ $log->ip_address }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="fs-11 fw-bold text-uppercase text-body-tertiary">URL</label>
                                                <p class="mb-0 fw-semibold">{{ $log->url }}</p>
                                            </div>
                                        </div>
                                        <label class="fs-11 fw-bold text-uppercase text-body-tertiary mb-2">Payload (JSON Diff)</label>
                                        <div class="bg-dark p-3 rounded-2">
                                            <pre class="text-success mb-0 fs-10"><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">Nenhum log registrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
