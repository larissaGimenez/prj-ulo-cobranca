<div class="card mb-2 hover-actions-trigger shadow-sm cursor-pointer kanban-item border-translucent" data-id="{{ $task['id'] ?? 0 }}">
    <div class="card-body p-2"> {{-- Reduzi padding de p-3 para p-2 --}}
        <div class="justify-content-between d-flex align-items-start mb-1">
            <span class="badge badge-phoenix fs-11 badge-phoenix-{{ $task['priority_color'] ?? 'info' }} px-1">
                {{ $task['priority_label'] ?? 'Normal' }}
            </span>
            <div class="dropdown">
                <button class="btn btn-sm btn-link p-0 text-body-tertiary" type="button" data-bs-toggle="dropdown">
                    <span class="fas fa-ellipsis-h fs-11"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item py-1 fs-10" href="#"><span class="fas fa-edit me-2"></span>Editar</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item text-danger py-1 fs-10" href="#"><span class="fas fa-trash me-2"></span>Excluir</a></li>
                </ul>
            </div>
        </div>
        
        <h6 class="mb-1 fw-bold text-body-highlight fs-10">{{ $task['title'] ?? 'Sem Título' }}</h6>
        <p class="fs-11 text-body-tertiary mb-2 lh-sm line-clamp-2">{{ $task['description'] ?? 'Sem descrição.' }}</p>
        
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center text-body-tertiary fs-11 fw-semibold">
                <span class="fas fa-calendar-alt me-1"></span>
                <span>{{ $task['due_date'] ?? date('d/m') }}</span>
            </div>
            
            <div class="avatar-group">
                @if(isset($task['user_avatar']))
                    <div class="avatar avatar-xs rounded-circle border border-white">
                        <img class="rounded-circle" src="{{ asset($task['user_avatar']) }}" alt="" />
                    </div>
                @else
                    <div class="avatar avatar-xs rounded-circle border border-white">
                        <div class="avatar-name rounded-circle fs-11"><span>U</span></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
