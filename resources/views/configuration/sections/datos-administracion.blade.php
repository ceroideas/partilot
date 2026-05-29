@php
    $administration = $settingsAdministration;
    $panelUser = $settingsPanelUser ?? auth()->user();
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

    <form method="POST" action="{{ route('configuration.administration-settings.update') }}">
        @csrf
        <h4 class="mb-0 mt-1">Datos legales de la administración</h4>
        <small><i>Información de contacto y facturación de su administración</i></small>

        <div class="row mt-3">
            <div class="col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Nombre comercial</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{ url('assets/form-groups/admin/1.svg') }}" alt="">
                        </div>
                        <input class="form-control" type="text" name="name" value="{{ old('name', $administration->name) }}" required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Nº Receptor</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{ url('assets/form-groups/admin/2.svg') }}" alt="">
                        </div>
                        <input class="form-control" type="text" value="{{ $administration->receiving }}" readonly style="border-radius: 0 30px 30px 0; background:#f8f9fa;">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Nº Administración</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{ url('assets/form-groups/admin/2.svg') }}" alt="">
                        </div>
                        <input class="form-control" type="text" value="{{ $administration->admin_number ?? '—' }}" readonly style="border-radius: 0 30px 30px 0; background:#f8f9fa;">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Nombre Autónomo / Sociedad</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{ url('assets/form-groups/admin/3.svg') }}" alt="">
                        </div>
                        <input class="form-control" type="text" name="society" value="{{ old('society', $administration->society) }}" required style="border-radius: 0 30px 30px 0;">
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
                        <input class="form-control" type="text" name="nif_cif" value="{{ old('nif_cif', $administration->nif_cif) }}" required style="border-radius: 0 30px 30px 0;">
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
                        <input class="form-control" type="text" name="phone" value="{{ old('phone', $administration->phone) }}" required style="border-radius: 0 30px 30px 0;">
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
                        <select class="form-control" name="province" id="admin-settings-province" required style="border-radius: 0 30px 30px 0;">
                            <option value="">Seleccionar</option>
                            @foreach(($provinces ?? []) as $province)
                                <option value="{{ $province }}" {{ old('province', $administration->province) === $province ? 'selected' : '' }}>{{ $province }}</option>
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
                        <select class="form-control" name="city" id="admin-settings-city" required style="border-radius: 0 30px 30px 0;">
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
                        <input class="form-control" type="text" name="postal_code" value="{{ old('postal_code', $administration->postal_code) }}" required style="border-radius: 0 30px 30px 0;">
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
                        <input class="form-control" type="email" name="email" value="{{ old('email', $administration->email) }}" required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Web</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{ url('assets/form-groups/admin/0.svg') }}" alt="">
                        </div>
                        <input class="form-control" type="text" name="web" value="{{ old('web', $administration->web) }}" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
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
                        <input class="form-control" type="text" name="address" value="{{ old('address', $administration->address) }}" required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h4 class="mb-0 mt-1">Datos de acceso</h4>
        <small><i>El usuario de acceso es fijo. El correo de la administración se usa para notificaciones. Puede cambiar su contraseña del panel.</i></small>

        <div class="row mt-2">
            <div class="col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Usuario de acceso</label>
                    <input class="form-control" type="text" value="{{ $panelUser->panel_login_username ?? '—' }}" readonly style="border-radius: 30px; background: #f8f9fa;">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Contraseña actual</label>
                    <input class="form-control" type="password" name="current_password" autocomplete="current-password" style="border-radius: 30px;">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Nueva contraseña</label>
                    <input class="form-control" type="password" name="panel_password" autocomplete="new-password" style="border-radius: 30px;">
                </div>
            </div>
            <div class="col-md-4">
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

@if(!empty($provinceCityMap))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var map = @json($provinceCityMap ?? []);
    var provinceSelect = document.getElementById('admin-settings-province');
    var citySelect = document.getElementById('admin-settings-city');
    var currentCity = @json(old('city', $administration->city));

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
