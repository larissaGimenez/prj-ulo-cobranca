<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenantRequest;
use App\Models\Tenant;
use App\Services\Api\Omie\OmieService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantController extends Controller
{
    protected $omieService;

    public function __construct(OmieService $omieService)
    {
        $this->omieService = $omieService;
    }

    public function testConnection(Tenant $tenant): RedirectResponse
    {
        $result = $this->omieService->checkConnection($tenant);

        if (isset($result['error'])) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function index(): View
    {
        $tenants = Tenant::latest()->paginate(10);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant): View
    {
        return view('admin.tenants.show', compact('tenant'));
    }

    public function create(): View
    {
        return view('admin.tenants.create', [
            'tenant' => new Tenant()
        ]);
    }

    public function store(TenantRequest $request): RedirectResponse
    {
        Tenant::create($request->validated());

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Aplicativo cadastrado com sucesso!');
    }

    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['app_secret'])) {
            unset($data['app_secret']);
        }

        $tenant->update($data);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Aplicativo atualizado com sucesso!');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Aplicativo removido (arquivado) com sucesso.');
    }
}