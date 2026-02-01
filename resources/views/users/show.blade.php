@extends('layouts.layout')

@section('title','Usuarios')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Usuarios</a></li>
                        <li class="breadcrumb-item active">Datos</li>
                    </ol>
                </div>
                <h4 class="page-title">Usuarios</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<div class="d-flex justify-content-between align-items-center">
            			<h4 class="header-title">
            				Datos Usuario
            			</h4>
            			<div class="d-flex align-items-center gap-2">
            				<span class="badge bg-{{ $user->status ? 'success' : 'danger' }} fs-6" id="user-status-badge">{{ $user->status ? 'Activo' : 'Bloqueado' }}</span>
            				<button type="button" class="btn btn-sm btn-outline-secondary" id="user-toggle-status" title="Cambiar estado">Cambiar estado</button>
            			</div>
            		</div>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">
                    		<div class="form-card bs mb-3">

                    			<div class="form-wizard-element active" data-tab="datos_usuario">
                    				
                    				<span>1</span>
                    				<img src="{{url('icons_/usuarios.svg')}}" alt="">
                    				<label>Datos Usuario</label>
                    			</div>

                    			<div class="form-wizard-element" data-tab="cartera">
                    				<span>2</span>
                    				<img src="{{url('assets/form-groups/wallet.svg')}}" alt="">
                    				<label>Cartera</label>
                    			</div>

                    			<div class="form-wizard-element" data-tab="historial">
                    				<span>3</span>
                    				<img src="{{url('assets/form-groups/history.svg')}}" alt="">
                    				<label>Historial</label>
                    			</div>
                    			
                    		</div>

                    		<a href="{{ route('users.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">

                    		<div class="tabbable show-content">
                    			
                    			<div class="tab-content p-0">
                    				
                    				<div class="tab-pane fade active show" id="datos_usuario">
                    					<div class="form-card bs" style="min-height: 658px;">
			                    			<h4 class="mb-0 mt-1">
			                    				Datos de Identificación

			                    				<a href="{{ route('users.edit', $user->id) }}" class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
			                    					<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
			                    					Editar</a>
			                    			</h4>
			                    			<small><i>Todos los campos son obligatorios</i></small>
			                    			<div style="clear: both;"></div>

			                    			<div class="form-group mt-2 mb-3">

			                    				<div class="row">
			                    					
			                    					<div class="col-1">
			                    						
			                    						<div class="photo-preview-3" style="background-image: url({{ asset('storage/' . $user->image) }});">
			                    							
			                    							@if($user->image)

			                    							@else
			                    								<i class="ri-account-circle-fill"></i>
			                    							@endif

			                    						</div>
			                    						
			                    						<div style="clear: both;"></div>
			                    					</div>

			                    					<div class="col-4 text-center">

			                    						<h4 class="mt-3 mb-0">{{ $user->name }} {{ $user->last_name }} {{ $user->last_name2 }}</h4>

			                    						<small>Usuario</small> <br>
			                    						
			                    					</div>
			                    				</div>

			                    				<div style="clear: both;"></div>
			                    			</div>

			                    			
			                    			<br>

			                    			<div>
			                    				<div class="row">
			                    					
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Nombre</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $user->name ?: 'Sin nombre' }}" class="form-control" type="text" placeholder="Nombre" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Primer Apellido</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $user->last_name ?: 'Sin apellido' }}" class="form-control" type="text" placeholder="Primer Apellido" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Segundo Apellido</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $user->last_name2 ?: 'Sin segundo apellido' }}" class="form-control" type="text" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-3">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">NIF/CIF</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $user->nif_cif ?: 'Sin NIF/CIF' }}" class="form-control" type="text" placeholder="12345678A" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>
			                    					
			                    					<div class="col-3">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">F. Nacimiento</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $user->birthday ? $user->birthday->format('d/m/Y') : 'Sin fecha de nacimiento' }}" class="form-control" type="text" placeholder="1985-03-15" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $user->email ?: 'Sin email' }}" class="form-control" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $user->phone ?: 'Sin teléfono' }}" class="form-control" type="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-3">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Contraseña</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="************" class="form-control" type="text" placeholder="Contraseña" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    				</div>
			                    			</div>

                    					</div>
                    				</div>
                    				
                    			</div>
                    		</div>

                    	</div>

                    </div>

                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection

@section('scripts')

<script>
(function() {
    var userId = {{ $user->id }};
    var csrf = '{{ csrf_token() }}';

    // Activar pestaña desde ?tab= (cartera, historial, datos_usuario)
    var tabParam = '{{ $tab ?? "" }}';
    if (tabParam) {
        document.querySelectorAll('.form-wizard-element').forEach(function(el) {
            el.classList.toggle('active', el.getAttribute('data-tab') === tabParam);
        });
    }

    // Navegación interna por wizard
    document.querySelectorAll('.form-wizard-element').forEach(function(item) {
        item.addEventListener('click', function() {
            document.querySelectorAll('.form-wizard-element').forEach(function(nav) { nav.classList.remove('active'); });
            this.classList.add('active');
        });
    });

    // Toggle estado usuario (AJAX)
    document.getElementById('user-toggle-status') && document.getElementById('user-toggle-status').addEventListener('click', function() {
        var btn = this;
        btn.disabled = true;
        fetch('{{ route("users.toggle-status", $user->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var badge = document.getElementById('user-status-badge');
                badge.textContent = data.status_text;
                badge.className = 'badge bg-' + data.status_class + ' fs-6';
            }
        })
        .catch(function() { alert('Error al cambiar el estado'); })
        .finally(function() { btn.disabled = false; });
    });
})();
</script>

@endsection