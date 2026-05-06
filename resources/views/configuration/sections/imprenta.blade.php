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

    <form action="{{ route('configuration.imprenta.update') }}" method="POST">
        @csrf

        <h4 class="mb-0 mt-1">Datos Partilot</h4>
        <small><i>Imprenta, costes dinámicos y datos de pago</i></small>

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="form-group mt-2 mb-3">
                    <h5 class="mb-3">Datos legales de la imprenta</h5>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Empresa</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/1.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="text" name="company_name"
                                        placeholder="Empresa"
                                        value="{{ old('company_name', $printConfiguration->company_name ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
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
                                    <input class="form-control" type="text" name="nif_cif"
                                        placeholder="NIF/CIF"
                                        value="{{ old('nif_cif', $printConfiguration->nif_cif ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
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
                                    <input class="form-control" type="text" name="postal_code"
                                        placeholder="C.P."
                                        value="{{ old('postal_code', $printConfiguration->postal_code ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Dirección</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/8.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="text" name="address"
                                        placeholder="Dirección"
                                        value="{{ old('address', $printConfiguration->address ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
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
                                    <input class="form-control" type="text" name="province"
                                        placeholder="Provincia"
                                        value="{{ old('province', $printConfiguration->province ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
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
                                    <input class="form-control" type="text" name="city"
                                        placeholder="Localidad"
                                        value="{{ old('city', $printConfiguration->city ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
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
                                    <input class="form-control" type="text" name="phone"
                                        placeholder="Teléfono"
                                        value="{{ old('phone', $printConfiguration->phone ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
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
                                    <input class="form-control" type="email" name="email"
                                        placeholder="ejemplo@cuentaemail.com"
                                        value="{{ old('email', $printConfiguration->email ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3 mb-1 g-3">
                    <div class="col-lg-7">
                    <div class="form-group mt-2 mb-0 h-100" style="background-color: #f8f9fa; padding: 1rem; border-radius: 0.75rem; border: 1px solid #ececec;">
                    <h4 class="mb-0 mt-1">Datos de impresión</h4>
                    <small><i>Importes por participación / trasera y tacos</i></small>

                    <div class="row mt-3 g-3">
                        <div class="col-6 col-md-4">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Precio Diseño</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="number" step="0.0001" min="0"
                                        name="price_design"
                                        value="{{ old('price_design', $printConfiguration->price_design ?? 0) }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Precio Participación</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="number" step="0.0001" min="0"
                                        name="price_participation"
                                        value="{{ old('price_participation', $printConfiguration->price_participation ?? 0) }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Precio Trasera (B/N)</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="number" step="0.0001" min="0"
                                        name="price_back_bw"
                                        value="{{ old('price_back_bw', $printConfiguration->price_back_bw ?? 0) }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Precio Trasera (Color)</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="number" step="0.0001" min="0"
                                        name="price_back_color"
                                        value="{{ old('price_back_color', $printConfiguration->price_back_color ?? 0) }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Precio Taco 25</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="number" step="0.0001" min="0"
                                        name="price_taco_25"
                                        value="{{ old('price_taco_25', $printConfiguration->price_taco_25 ?? 0) }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Precio Taco 50</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="number" step="0.0001" min="0"
                                        name="price_taco_50"
                                        value="{{ old('price_taco_50', $printConfiguration->price_taco_50 ?? 0) }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Precio Taco 100</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="number" step="0.0001" min="0"
                                        name="price_taco_100"
                                        value="{{ old('price_taco_100', $printConfiguration->price_taco_100 ?? 0) }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group mt-2 mb-0 h-100 d-flex flex-column justify-content-between">
                            <div>
                            <h5 class="mb-3">Datos de pago (Stripe)</h5>
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Stripe Publishable Key</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="text" name="stripe_publishable_key"
                                        placeholder="pk_test_..."
                                        value="{{ old('stripe_publishable_key', $printConfiguration->stripe_publishable_key ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Stripe Secret Key</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="password" name="stripe_secret_key"
                                        placeholder="sk_test_..."
                                        value="{{ old('stripe_secret_key', $printConfiguration->stripe_secret_key ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                            </div>
                            <div class="form-group mt-2 mb-3">
                                <label class="label-control">Stripe Webhook Secret</label>
                                <div class="input-group input-group-merge group-form">
                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                    </div>
                                    <input class="form-control" type="password" name="stripe_webhook_secret"
                                        placeholder="whsec_..."
                                        value="{{ old('stripe_webhook_secret', $printConfiguration->stripe_webhook_secret ?? '') }}"
                                        style="border-radius: 0 30px 30px 0;">
                                </div>
                                <div class="form-text text-muted mt-1">
                                    Estas claves se usarán para crear pagos y validar webhooks de Stripe.
                                </div>
                            </div>
                            </div>
                            <div class="text-end mt-2">
                                <button type="submit"
                                    style="border-radius: 30px; min-width: 140px; background-color: #e78307; color: #333; padding: 8px 16px; font-weight: bolder;"
                                    class="btn btn-md btn-light">
                                    <i class="ri-save-line me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
