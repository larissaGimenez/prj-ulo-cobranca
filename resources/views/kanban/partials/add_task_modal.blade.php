<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-translucent shadow-lg">
            <div class="modal-header px-4 border-0 pt-4 pb-0">
                <h4 class="modal-title fs-8">Nova Cobrança / Tarefa</h4>
                <button class="btn-close ms-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form id="add-task-form">
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-uppercase fs-11 text-body-tertiary">Título</label>
                        <input class="form-control rounded-2 py-1 fs-10" type="text" placeholder="Ex: Cobrança Cliente X" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-uppercase fs-11 text-body-tertiary">Descrição</label>
                        <textarea class="form-control rounded-2 py-1 fs-10" rows="3" placeholder="Detalhes..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-uppercase fs-11 text-body-tertiary">Prioridade</label>
                            <select class="form-select rounded-2 py-1 fs-10">
                                <option value="info">Baixa</option>
                                <option value="warning">Média</option>
                                <option value="danger">Alta</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-uppercase fs-11 text-body-tertiary">Vencimento</label>
                            <input class="form-control rounded-2 py-1 fs-10" type="date">
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button class="btn btn-link text-body-tertiary fs-10 px-3 decoration-none" type="button" data-bs-toggle="modal">Cancelar</button>
                        <button class="btn btn-primary px-4 rounded-pill fs-10" type="submit">Criar Tarefa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
