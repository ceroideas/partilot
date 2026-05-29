@php
    $entity = $settingsEntity;
    $panelUser = $settingsPanelUser ?? auth()->user();
    $activeTab = request('tab', 'legales');
    $formatIban = function (?string $iban): string {
        $digits = preg_replace('/\s+/', '', strtoupper((string) $iban));
        if ($digits === '') {
            return '';
        }
        return trim(chunk_split($digits, 4, ' '));
    };
@endphp

<div class="form-card bs" style="min-height: 658px;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="form-card bs mb-3">
                <div class="form-wizard-element {{ $activeTab === 'legales' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#entidad-tab-legales" role="button">
                    <span>&nbsp;&nbsp;</span>
                    <img src="{{ url('assets/entidad.svg') }}" alt="">
                    <label>Datos legales</label>
                </div>
                <div class="form-wizard-element {{ $activeTab === 'gestores' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#entidad-tab-gestores" role="button">
                    <span>&nbsp;&nbsp;</span>
                    <img src="{{ url('assets/gestor.svg') }}" alt="">
                    <label>Datos gestores</label>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane fade {{ $activeTab === 'legales' ? 'show active' : '' }}" id="entidad-tab-legales">
                    <form method="POST" action="{{ route('configuration.entity-settings.update') }}">
                        @csrf
                        <h4 class="mb-0 mt-1">Datos legales de la entidad</h4>
                        <small><i>Información de contacto y facturación de su entidad</i></small>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Nombre comercial</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/1.svg') }}" alt="">
                                        </div>
                                        <input class="form-control" type="text" name="name" value="{{ old('name', $entity->name) }}" required style="border-radius: 0 30px 30px 0;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">NIF/CIF</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                        </div>
                                        <input class="form-control" type="text" name="nif_cif" value="{{ old('nif_cif', $entity->nif_cif) }}" style="border-radius: 0 30px 30px 0;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Teléfono</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/10.svg') }}" alt="">
                                        </div>
                                        <input class="form-control" type="text" name="phone" value="{{ old('phone', $entity->phone) }}" style="border-radius: 0 30px 30px 0;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Provincia</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/5.svg') }}" alt="">
                                        </div>
                                        <select class="form-control" name="province" id="entity-settings-province" style="border-radius: 0 30px 30px 0;">
                                            <option value="">Seleccionar</option>
                                            @foreach(($provinces ?? []) as $province)
                                                <option value="{{ $province }}" {{ old('province', $entity->province) === $province ? 'selected' : '' }}>{{ $province }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Localidad</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/6.svg') }}" alt="">
                                        </div>
                                        <select class="form-control" name="city" id="entity-settings-city" style="border-radius: 0 30px 30px 0;">
                                            <option value="">Seleccionar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Código Postal</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/7.svg') }}" alt="">
                                        </div>
                                        <input class="form-control" type="text" name="postal_code" value="{{ old('postal_code', $entity->postal_code) }}" style="border-radius: 0 30px 30px 0;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Email</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/9.svg') }}" alt="">
                                        </div>
                                        <input class="form-control" type="email" name="email" value="{{ old('email', $entity->email) }}" required style="border-radius: 0 30px 30px 0;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Dirección</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/8.svg') }}" alt="">
                                        </div>
                                        <input class="form-control" type="text" name="address" value="{{ old('address', $entity->address) }}" style="border-radius: 0 30px 30px 0;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h4 class="mb-0 mt-1">Datos de acceso</h4>
                        <small><i>El email de acceso coincide con el email de la entidad. Puede cambiar su contraseña del panel.</i></small>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Email de acceso</label>
                                    <div class="input-group input-group-merge group-form">
                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                            <img src="{{ url('assets/form-groups/admin/9.svg') }}" alt="">
                                        </div>
                                        <input class="form-control" type="email" value="{{ $panelUser->email ?? $entity->email }}" readonly style="border-radius: 0 30px 30px 0; background: #f8f9fa;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Contraseña actual</label>
                                    <input class="form-control" type="password" name="current_password" autocomplete="current-password" style="border-radius: 30px;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Nueva contraseña</label>
                                    <input class="form-control" type="password" name="panel_password" autocomplete="new-password" style="border-radius: 30px;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2 mb-3">
                                    <label class="label-control">Repetir contraseña</label>
                                    <input class="form-control" type="password" name="panel_password_confirmation" autocomplete="new-password" style="border-radius: 30px;">
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-md btn-light" style="border-radius: 30px; min-width: 180px; background-color: #e78307; color: #333; font-weight: bolder;">
                                Guardar <i class="ri-arrow-right-circle-line ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade {{ $activeTab === 'gestores' ? 'show active' : '' }}" id="entidad-tab-gestores">
                    <h4 class="mb-0 mt-1">Gestores de la entidad</h4>
                    <small><i>Puede consultar y modificar los datos de los gestores secundarios. La cuenta de acceso al panel no aparece en esta lista.</i></small>

                    @forelse($settingsManagers as $manager)
                        @php($managerUser = $manager->user)
                        <div class="border rounded p-3 mt-3">
                            <form method="POST" action="{{ route('configuration.entity-manager.update', $manager->id) }}">
                                @csrf
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>{{ trim(($managerUser->name ?? '').' '.($managerUser->last_name ?? '')) ?: 'Gestor #'.$manager->id }}</strong>
                                    @if($manager->is_primary)
                                        <span class="badge bg-secondary">Principal</span>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Nombre</label>
                                        <input class="form-control form-control-sm" name="name" value="{{ old('name', $managerUser->name ?? '') }}" required style="border-radius: 20px;">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Primer apellido</label>
                                        <input class="form-control form-control-sm" name="last_name" value="{{ old('last_name', $managerUser->last_name ?? '') }}" required style="border-radius: 20px;">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Segundo apellido</label>
                                        <input class="form-control form-control-sm" name="last_name2" value="{{ old('last_name2', $managerUser->last_name2 ?? '') }}" style="border-radius: 20px;">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">NIF/CIF</label>
                                        <input class="form-control form-control-sm" name="nif_cif" value="{{ old('nif_cif', $managerUser->nif_cif ?? '') }}" style="border-radius: 20px;">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Email</label>
                                        <input class="form-control form-control-sm" type="email" name="email" value="{{ old('email', $managerUser->email ?? '') }}" required style="border-radius: 20px;">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Teléfono</label>
                                        <input class="form-control form-control-sm" name="phone" value="{{ old('phone', $managerUser->phone ?? '') }}" style="border-radius: 20px;">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Fecha nacimiento</label>
                                        <input class="form-control form-control-sm" type="date" name="birthday" value="{{ old('birthday', optional($managerUser->birthday)->format('Y-m-d')) }}" style="border-radius: 20px;">
                                    </div>
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label small">Comentarios</label>
                                        <input class="form-control form-control-sm" name="comment" value="{{ old('comment', $managerUser->comment ?? '') }}" style="border-radius: 20px;">
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-sm btn-dark" style="border-radius: 20px;">Guardar gestor</button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="alert alert-info mt-3 mb-0">
                            No hay gestores secundarios registrados. Si necesita añadir gestores, contacte con su administración.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($provinceCityMap))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var map = @json($provinceCityMap ?? []);
    var provinceSelect = document.getElementById('entity-settings-province');
    var citySelect = document.getElementById('entity-settings-city');
    var currentCity = @json(old('city', $entity->city));

    function fillCities(province) {
        if (!citySelect) return;
        citySelect.innerHTML = '<option value="">Seleccionar</option>';
        (map[province] || []).forEach(function (city) {
            var opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            if (currentCity && currentCity === city) opt.selected = true;
            citySelect.appendChild(opt);
        });
        if (currentCity && province && !(map[province] || []).includes(currentCity)) {
            var extra = document.createElement('option');
            extra.value = currentCity;
            extra.textContent = currentCity;
            extra.selected = true;
            citySelect.appendChild(extra);
        }
    }

    if (provinceSelect) {
        provinceSelect.addEventListener('change', function () {
            currentCity = '';
            fillCities(this.value);
        });
        fillCities(provinceSelect.value);
    }
});
</script>
@endif
