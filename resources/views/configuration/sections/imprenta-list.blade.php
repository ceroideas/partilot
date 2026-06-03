<div class="form-card bs pb-3" style="min-height: 658px;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-1 mb-3">
        <div>
            <h4 class="mb-0">Imprentas</h4>
            <small class="text-muted"><i>Cada imprenta diseña e imprime con sus propios precios y acceso al panel.</i></small>
        </div>
        @if(auth()->user()?->isSuperAdmin())
            <a href="{{ route('configuration.index', ['section' => 'imprenta', 'print_config_id' => 'new']) }}"
               class="btn btn-warning text-dark fw-semibold" style="border-radius: 30px;">
                <i class="ri-add-line me-1"></i> Añadir imprenta
            </a>
        @endif
    </div>

    @if(($printConfigurations ?? collect())->isEmpty())
        <div class="alert alert-info mb-0">No hay imprentas configuradas.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Empresa</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($printConfigurations as $shop)
                        <tr>
                            <td>{{ $shop->displayName() }}</td>
                            <td>{{ $shop->email ?: '—' }}</td>
                            <td>
                                @if($shop->isActive())
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('configuration.index', ['section' => 'imprenta', 'print_config_id' => $shop->id]) }}"
                                   class="btn btn-sm btn-dark" style="border-radius: 20px;">
                                    <i class="ri-settings-3-line me-1"></i> Configurar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
