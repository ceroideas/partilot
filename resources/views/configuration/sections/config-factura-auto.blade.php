<div class="form-card bs" style="min-height: 658px;">
    <h4 class="mb-0 mt-1">
        Datos legales de la imprenta
    </h4>
    <small><i>Todos los campos son obligatorios</i></small>

    <div class="form-group mt-2 mb-3">
        <div class="row">
            <div class="col-6">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Empresa</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/1.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="empresa" id="empresa" placeholder="Empresa" value="El Búho Lotero" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">NIF/CIF</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="nif_cif_imprenta" id="nif-cif-imprenta" placeholder="NIF/CIF" value="16600600A" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Dirección</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/8.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="direccion_imprenta" id="direccion-imprenta" placeholder="Dirección" value="Avd. Club Deportivo 28" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Código Postal</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/7.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="codigo_postal_imprenta" id="codigo-postal-imprenta" placeholder="C.P." value="26007" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Provincia</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/5.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="provincia_imprenta" id="provincia-imprenta" placeholder="Provincia" value="La Rioja" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Localidad</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/6.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="localidad_imprenta" id="localidad-imprenta" placeholder="Localidad" value="Logroño" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Teléfono</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/10.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="telefono_imprenta" id="telefono-imprenta" placeholder="941 900 900" value="941 900 900" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Email</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/9.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="email" name="email_imprenta" id="email-imprenta" placeholder="ejemplo@cuentaemail.com" value="administracion@ejemplo.es" readonly required style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-0 mt-1">
        Precio Gestión
    </h4>
    <small><i>Todos los campos son obligatorios. Importe por participación.</i></small>

    <div class="form-group mt-2 mb-3" style="background-color: #f8f9fa; padding: 1rem; border-radius: 0.375rem;">
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Gestión Particip. 1000Un</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="gestion_1000" id="gestion-1000" placeholder="00,00€" value="00,05€" style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Gestión Part. Administración</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="gestion_administracion" id="gestion-administracion" placeholder="00,00€" value="00,03€" style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Gestión Particip. 5000Un</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="gestion_5000" id="gestion-5000" placeholder="00,00€" value="00,04€" style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Comisión Gestión Pago</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="comision_gestion_pago" id="comision-gestion-pago" placeholder="00,00€" value="00,03€" style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Gestión Particip. 10000Un</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="gestion_10000" id="gestion-10000" placeholder="00,00€" value="00,03€" style="border-radius: 0 30px 30px 0;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-0 mt-1">
        Datos Pago
    </h4>
    <small><i>Introduzca los detalles de su cuenta bancaria para procesar los pagos. Asegúrese de que la información es correcta y está actualizada.</i></small>

    <div class="form-group mt-2 mb-3">
        <div class="row">
            <div class="col-12">
                <div class="form-group mt-2 mb-3">
                    <label class="label-control">Cuenta bancaria (IBAN)</label>
                    <div class="input-group input-group-merge group-form">
                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                            <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                        </div>
                        <input class="form-control" type="text" name="iban" id="iban" placeholder="1234 - 1234 - 1234 - 12 - 1234567890" value="1234 - 1234 - 1234 - 12 - 1234567890" style="border-radius: 0 30px 30px 0; letter-spacing: 0.05em;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-end">
            <button type="button" id="btn-guardar-config-factura" style="border-radius: 30px; min-width: 140px; background-color: #e78307; color: #333; padding: 8px 16px; font-weight: bolder;" class="btn btn-md btn-light mt-2">
                <i class="fe-save me-1"></i> Guardar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btn-guardar-config-factura').addEventListener('click', function() {
        // Lógica de guardado pendiente de implementar
        alert('Funcionalidad de guardado pendiente de implementar');
    });
});
</script>
