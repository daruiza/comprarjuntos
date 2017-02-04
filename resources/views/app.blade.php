<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		
		<title>{!! Session::get('app') !!}</title>
		<meta name="description" content="Plaza de mercado para la Economia del Bien Común" />
		<meta name="keywords" content="bien común, economia solidaria, circulos solidarios, grupos solidarios, fomentamos, comprar, vender, tienda, cart, plaza mercado, comprar juntos, grupo consumo" />


		<link rel="shortcut icon" href="{{ url('images/icons/icon.png') }}">
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		
		@php ($style = "default")
		@if (Session::has('style'))
			@php ($style=Session::get('style'))
		@endif
		
			
		<link  rel="stylesheet" href="{{ url('css/'.$style.'/app.css') }}" type="text/css" />		
		<link  rel="stylesheet" href="{{ url('css/jquery-ui.css') }}" type="text/css" />
		<link  rel="stylesheet" href="{{ url('css/bootstrap-submenu.min.css') }}" type="text/css" />
		<link  rel="stylesheet" href="{{ url('css/bootstrap-datepicker.min.css') }}" type="text/css" />	
		<link  rel="stylesheet" href="{{ url('css/datatables.min.css') }}" type="text/css" />	
		<link  rel="stylesheet" href="{{ url('css/datatables-responsive.min.css') }}" type="text/css" />		
				
	</head>
	
	<body>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
			
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle Navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="{{ url('/') }}"><b>{{ Session::get('app') }}</b></a>
				</div>
				
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				@if (Auth::guest())
					<!-- Usuario sin Loguear -->
					<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" data-submenu="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" tabindex="0">Como Iniciar<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">							
							<li><a href="{{ url('/auth/logout') }}">Ser Un Tendero</a></li>							
							<li><a href="{{ url('/auth/logout') }}">Atender una Venta</a></li>
							<li><a href="{{ url('/auth/logout') }}">Crear un Grupo</a></li>											
						</ul>
					</li>				
					</ul>
					
					<ul class="nav navbar-nav navbar-right">				
						<li><a href="#" data-toggle="modal" data-target="#registry_modal" >Registrate</a></li>
						<li><a href="#" data-toggle="modal" data-target="#login_modal" >Ingresa</a></li>
						<li>
							<a href="#">
								<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true" style = "font-size: 20px;"></span>
								<span style = "font-size: 14px;" >Carro</span>
							</a>
							
						</li>												
					</ul>
				@else
					<!-- Usuario Logueado -->
					<ul class="nav navbar-nav">
					<!-- Para pintar los modulos de las aplicaciones -->
					<!-- Para importar los js de los modulos -->
					@foreach (Session::get('comjunplus.usuario.permisos') as $llave_permiso => $permiso)
						
						<li>
							<a href="{{ url(json_decode($permiso['preferencias'])->js.'/listar/')}}">
								<span class="{{json_decode($permiso['preferencias'])->icono}}" aria-hidden="true" style = "font-size: 15px;"></span>
								<span>{{$permiso['aplicacion']}}</span>
							</a>
						
						<!-- Por cada categoria -->
						@foreach ($permiso['modulos'] as $llave_categoria => $categoria)
							<!-- Por cada modulo dentro de la categoria -->
							@foreach ($categoria as $llave_modulo => $modulo)
								<!-- Por cada opcion dentro del modulo -->
								<!-- Listamos opciones Si el modulo esta en esta ruta -->
								@if(Session::get('controlador') == json_decode($modulo['preferencias'])->controlador )
									@foreach ($modulo['opciones'] as $llave_opcion => $opcion)
									<!-- Se lista la opcion deacuerdo al modulo -->
										<li>											
											<a href="#">
												<div class="" id="btn_nueva_tienda" data-toggle="modal" data-target="#{{$opcion['accion']}}_modal">
												<span class="{{$opcion['icono']}}" aria-hidden="true" style = "font-size: 12px;"></span>
											<span>{{$opcion[$llave_opcion]}}</span>
											</div>
											</a>
										</li>
									
									@endforeach	
								@endif															
							
								<!-- Cargamos los js que hacer referencia alos modulos para el cliente -->
								{{ Html::script('js/'.json_decode($permiso['preferencias'])->js.'/'.json_decode($modulo['preferencias'])->js.'.js') }}
							@endforeach
						@endforeach
						</li>
					@endforeach				
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" data-submenu="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" tabindex="0">{{Session::get('comjunplus.usuario.name')}}<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">							
								<li>
									<a href="{{ url('/buzon/'.Session::get('comjunplus.usuario.id')) }}">
										<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>										
										<span class="" aria-hidden="true" style = "font-size: 10px;" >{{Session::get('comjunplus.usuario.messages')}}</span>
										Buzón de Mensajes
										<span class="badge">5</span>
									</a>
								</li>
								<li>
									<a href="{{ url('/perfil/'.Session::get('comjunplus.usuario.id')) }}">
										<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
										Perfil de Usuario
									</a>
								</li>							
								<li>
									<a href="{{ url('/salida/'.Session::get('comjunplus.usuario.id')) }}">
										<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
										Salida Segura
									</a>
								</li>																		
							</ul>
						</li>
						<li>
							<a href="#">
								<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true" style = "font-size: 20px;"></span>
								<span style = "font-size: 14px;" >Carro</span>
							</a>							
						</li>				
					</ul>	
				@endif				
				</div>
				
				
			</div>
		</nav>
		<div class="container-fluid">
			@yield('content')
		</div>
		

		<!-- Scripts -->
		
		<script type="text/javascript" src="{{ url('js/jquery.min.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/jquery-ui.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/bootstrap.min.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/bootstrap.submenu.min.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/bootstrap-datepicker.min.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/locales/bootstrap-datepicker.es.min.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/datatables.min.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/datatables-responsive.min.js') }}"></script>

		<script type="text/javascript" src="{{ url('js/seguridad/seg_user.js') }}"></script>
		<script type="text/javascript" src="{{ url('js/seguridad/seg_ajaxobject.js') }}"></script>
		
		<script type="text/javascript">	$('[data-submenu]').submenupicker();</script>
		@yield('modal')
		@yield('script')
		
	</body>
 </html>