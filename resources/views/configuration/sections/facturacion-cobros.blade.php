@if(!empty($configurationEntityScoped) && !empty($settingsEntity))
    @php
        $entity = $settingsEntity;
        $formatIban = function (?string $iban): string {
            $digits = preg_replace('/\s+/', '', strtoupper((string) $iban));
            if ($digits === '') {
                return '';
            }
            return trim(chunk_split($digits, 4, ' '));
        };
    @endphp
    <div class="form-card bs pb-3" style="min-height: 658px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('configuration.entity-billing.update') }}">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    <h4 class="mb-0 mt-1">Datos legales de facturación</h4>
                    <small><i>Estos datos se usan en las órdenes de pago de su entidad</i></small>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="label-control">Nombre comercial</label>
                            <input class="form-control" type="text" value="{{ $entity->name }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label class="label-control">NIF/CIF</label>
                            <input class="form-control" type="text" value="{{ $entity->nif_cif ?? '—' }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="label-control">Dirección</label>
                            <input class="form-control" type="text" value="{{ $entity->address ?? '—' }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                        <div class="col-md-4 mt-2">
                            <label class="label-control">Provincia</label>
                            <input class="form-control" type="text" value="{{ $entity->province ?? '—' }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                        <div class="col-md-4 mt-2">
                            <label class="label-control">Localidad</label>
                            <input class="form-control" type="text" value="{{ $entity->city ?? '—' }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                        <div class="col-md-4 mt-2">
                            <label class="label-control">Código postal</label>
                            <input class="form-control" type="text" value="{{ $entity->postal_code ?? '—' }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="label-control">Teléfono</label>
                            <input class="form-control" type="text" value="{{ $entity->phone ?? '—' }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="label-control">Email</label>
                            <input class="form-control" type="text" value="{{ $entity->email ?? '—' }}" readonly style="border-radius: 30px; background:#f8f9fa;">
                        </div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Para modificar los datos legales, use la sección <a href="{{ url('/configuration?section=datos-entidad') }}">Mis datos</a>.</p>
                </div>

                <div class="col-lg-5">
                    <h4 class="mb-0 mt-1">Datos pago</h4>
                    <small><i>Cuenta bancaria para recibir cobros de participaciones</i></small>

                    <div class="form-group mt-3 mb-3">
                        <label class="label-control">Cuenta bancaria (IBAN)</label>
                        <div class="input-group input-group-merge group-form">
                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                            </div>
                            <input class="form-control @error('billing_iban') is-invalid @enderror" type="text" name="billing_iban" value="{{ old('billing_iban', $formatIban($entity->billing_iban)) }}" placeholder="ES00 0000 0000 0000 0000 0000" style="border-radius: 0 30px 30px 0; letter-spacing: 0.05em;">
                        </div>
                        @error('billing_iban')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-md btn-light" style="border-radius: 30px; min-width: 140px; background-color: #e78307; color: #333; font-weight: bolder;">
                            <i class="fe-save me-1"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@else
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Facturación y Cobros</h4>
</div>

<div class="alert alert-info">
    <i class="fe-info me-2"></i>
    Esta sección está en desarrollo. Próximamente podrás gestionar la facturación y cobros.
</div>
@endif
