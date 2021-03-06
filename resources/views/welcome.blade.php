@extends('app')
@section('content')
	<style>
		.panel-body {		    
		    padding-bottom: 0px;
		}
		.input_danger{
			color: #a94442;
    		background-color: #f2dede;
    		border-color: #ebccd1;
		}
		.center-block {
		  display: block;
		  margin-left: auto;
		  margin-right: auto;
		  text-align: center;
		}
		.introduccion{
			margin-top: 2%;padding: 1%;
			background:  #B0E1EA ; /* For browsers that do not support gradients */    
		    background: -webkit-linear-gradient(left, #B0E1EA  , white ); /* For Safari 5.1 to 6.0 */
		    background: -o-linear-gradient(right,   #B0E1EA  , white ); /* For Opera 11.1 to 12.0 */
		    background: -moz-linear-gradient(right,   #B0E1EA  , white ); /* For Firefox 3.6 to 15 */
		    background: linear-gradient(to right,   #B0E1EA  , white ); /* Standard syntax (must be last) */
		}
		/*449AA2 - #D8E9EC*/
		.comprarjuntos{
			margin-top: 2%;padding: 1%;
			background:  #fff; /* For browsers that do not support gradients */    
		    background: -webkit-linear-gradient(left, #449AA2 , #B0E1EA  ); /* For Safari 5.1 to 6.0 */
		    background: -o-linear-gradient(right,   #449AA2 , #B0E1EA  ); /* For Opera 11.1 to 12.0 */
		    background: -moz-linear-gradient(right,   #449AA2 , #B0E1EA  ); /* For Firefox 3.6 to 15 */
		    background: linear-gradient(to right,   #449AA2 , #B0E1EA  ); /* Standard syntax (must be last) */
		}
		.option_store{
			text-align: center;
			cursor:pointer;			
		}

		.option_store a{
			text-decoration:none;			
		}
		.option_store_icon{
			font-size: 17px;
		}
		.option_store:hover{			
			color: #000 !important;
		}
		.option_store a:hover{			
			color: #000 !important;
		}
		
		.popover-content ul{
			margin-left: -25px;
		}

		.popover-content ul li{
			cursor:pointer;
		}
		.popover-content ul li:hover{
			background-color: #dddddd;
		}
		.glyphicon-star{
			color: #ffcc00;
		}

		@-moz-document url-prefix() {
		    .img_tendero {
		    	margin-left: -40% !important;
		    } 
		}
		.btn{
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 14px;
			border-color: #449aa2;
		}

	</style>

	<!--Importacion de iconos especiales para la labor (star)-->
	<!--
	<link  rel="stylesheet" href="{{ url('fonts/font-awesome/css/font-awesome.min.css') }}">
	-->

	<div class="row visible-lg" style="margin-top: 5%;"></div>
	<div class="row visible-md" style="margin-top: 7%;"></div>
	<div class="row visible-sm" style="margin-top: 10%;"></div>
	<div class="row visible-xs" style="margin-top: 16%;"></div>
	
	<div class="row">
	<div class="alerts col-md-12 col-md-offset-0">
		<!-- $error llega si la función validator falla en autenticar los datos -->
		@if (count($errors) > 0)
			<div class="alert alert-danger alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<strong>Algo no va bien con el acceso!</strong> Hay problemas con los datos diligenciados.
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul></br>						
				<div data-toggle="modal" data-target="#rpsw_modal" style = "cursor:pointer;" ><strong>Recuperar Contraseña AQUI!!!</strong></div>
			</div>
		@endif
		
		@if(Session::has('message'))
			<div class="alert alert-info alert-dismissible fade in" role="alert" >
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<strong>¡Operación exitosa!</strong>  El proceso se ha ejecutado adecuadamente.<br>
				<ul>
					@foreach (Session::get('message') as $message)
						@if ($message  == 'Perfil1')
							<li>  El perfil de usuario se halla ubicado en la esquina superior derecha al desplegar las opciones del botón que tiene tu nombre de usuario: {{Session::get('comjunplus.usuario.name')}}.  <a  href="{{ url('/perfil/'.Session::get('comjunplus.usuario.id')) }}">Ó dando CLICK AQUI</a>  </li>													
						@elseif ($message  == 'Perfil2' || $message  == 'Perfil3')						
						@else
							<li>{{ $message }}</li>
						@endif
					@endforeach					
				</ul>
			</div>  
		@endif

		@if(Session::has('message_ok'))
			<div class="alert alert-info alert-dismissible fade in" role="alert" >
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>				
				<ul>
					@foreach (Session::get('message_ok') as $message)
						<li>{{ $message }}</li>
					@endforeach					
				</ul>
			</div>  
		@endif
		
		@if(Session::has('alerta'))
			<div class="alert alert-warning alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<strong>¡Algo no va bien!</strong>  El proceso no se ejecuto correctamente.<br>			
				<ul>								
					@foreach (Session::get('alerta') as $alerta)
						<li>{{ $alerta }}</li>
					@endforeach															
				</ul>
			</div>                
		@endif		            
		
	    @if(Session::has('error'))
			<div class="alert alert-danger alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<strong>¡Algo no va bien!</strong>  El proceso no pudo ejecutarce.<br>
				<ul>								
					@foreach (Session::get('error') as $error)
					<li>{{ $error }}</li>
				@endforeach															
				</ul></br>
				<div data-toggle="modal" data-target="#rpsw_modal" style = "cursor:pointer;" ><strong>Recuperar Contraseña AQUI!!!</strong></div>
			</div>                
		@endif
	</div>
	</div>
	
	<!--Categorias-->	
	<div class="title m-b-md center-block visible-lg ">
		<div class="btn-group" role="group">
		@php ($i=1)	
		@foreach ($categorias as $llave_categoria => $categoria)			
			@if($i < 15)
				<button type="button" class="btn btn-default" data-toggle="popover" title="{{$llave_categoria}}" data-placement="bottom" data-content="{{ Html::ul($categoria)}}" data-html="true">{{$llave_categoria}}</button>			
			@endif			
			@php ($i++)	
		@endforeach
		</div>
	</div>
	
	<div class="title m-b-md center-block visible-md">
		<div class="btn-group" role="group">		
		@php ($i=1)		
		@foreach ($categorias as $llave_categoria => $categoria)
			@if($i < 11)
				<button type="button" class="btn btn-default" data-toggle="popover" title="{{$llave_categoria}}" data-placement="bottom" data-content="{{ Html::ul($categoria)}}" data-html="true">{{$llave_categoria}}</button>
			@endif			
			@php ($i++)	
		@endforeach
		</div>
	</div>

	<div class="title m-b-md center-block visible-sm">
		<div class="btn-group" role="group">		
		@php ($i=1)		
		@foreach ($categorias as $llave_categoria => $categoria)
			@if($i < 7)
				<button type="button" class="btn btn-default" data-toggle="popover" title="{{$llave_categoria}}" data-placement="bottom" data-content="{{ Html::ul($categoria)}}" data-html="true">{{$llave_categoria}}</button>
			@endif			
			@php ($i++)	
		@endforeach
		</div>
	</div>	

	<!--Div Introducciòn-->
	<div class="col-md-12 col-md-offset-0 introduccion visible-lg">
		<div class="row col-md-12" >
			<div class="col-md-2  col-md-offset-0">
				{{ Html::image('images/icons/LogoFomentamos.png','Imagen no disponible',array( 'style'=>'width: auto; height: 200px;border-radius: 0%; float: left;' ))}}	
			</div>
			<div class="col-md-7 col-md-offset-0" style="text-align: justify;">
				La economía del bien común no es ni el mejor de los modelos económicos ni el final de una historia, sólo el paso siguiente hacia un futuro más sostenible, justo y democrático. Se trata de un proceso participativo, de desarrollo abierto que busca sinergia en procesos similares como: economía solidaria, economía social, movimiento de bienes comunes, economía del postcrecimiento o democracia económica. Juntando sus esfuerzos, una gran cantidad de personas y actores son capaces de crear algo fundalmente nuevo. La implementación de la visión requiere motivación intrínseca y autorresponsabilidad, incentivos económicos, un orden político-legal coherente, así como concientización. Todas las personas, empresas y comunidades están invitadas a participar en la reconstrucción de la economía hacia el bien común.
			</div>
			@if(count($tendero))
				<div class="col-md-3"  style="text-align: center;">
					{{ Html::image('users/'.$tendero[0]->user_name.'/profile/'.$tendero[0]->avatar,'Imagen no disponible',array( 'style'=>'width: auto; height: 150px;border:2px solid #449aa2;border-radius: 50%;' ))}}
					<div>Yo: {{$tendero[0]->names}} </div>
					<div> Tambièn hago parte de ComprarJuntos</div>
				</div>
			@endif	
		</div>
	</div>	

	<div class="col-md-12 col-md-offset-0 " style="margin-top: 1%;">
		<!--Para resoluciones en celular-->
		<div  class=" row col-md-12 col-md-offset-0 hidden-lg" >
			<!-- Div de tiendas-->
			<div class="col-md-12 col-md-offset-0 " style="margin-top: 1%;">
				<div class=" col-md-12  col-md-offset-0 title m-b-md center-block" style="font-size: 22px;">
					<b>Encuentra Tiendas y CrearRedes de Consumo</b>
				</div>
				<div class="col-md-12 col-md-offset-0" style="margin-top: 1%;" >
				@foreach($tiendas as $tienda)
					<div class="col-md-3 col-mx-offset-1">
						<div class="panel panel-default" style="border-color:{{$tienda->color_two}};">					
							<div class="panel-body">
						    	<div class="row">
						    		<div class="col-md-12" style="text-align: center;">
						    			<a href="{{url('/'.$tienda->name)}}">
						    				{{ Html::image('users/'.$tienda->user_name.'/stores/'.$tienda->image,'Imagen no disponible',array( 'style'=>'width: 100%;height: 150px;border-radius: 0%;' ))}}	    				
						    			</a>					    				    									    			
						    		</div>
						    		<a href="{{url('/'.$tienda->name)}}" class="visible-lg" style="color:{{$tienda->color_two}};font-size: 16px;"> 
				    					{{ Html::image('users/'.$tienda->user_name.'/profile/'.$tienda->avatar,'Imagen no disponible',array('class'=>'img_tendero', 'style'=>'width: 35%;border-radius: 50%;position: absolute; margin-left: 40%;z-index: 99;' ))}}
				    				</a>	
						    		<div class="col-md-12"  style="background-color:{{$tienda->color_one}}; color: {{$tienda->color_two}}; border-color:
						    	{{$tienda->color_two}};padding: 0px;">					    			
						    			<div class="col-md-12" style="padding: 0px;" >
					    					<div style="text-align: center;">
					    						<a href="{{url('/'.$tienda->name)}}" style="color:{{$tienda->color_two}};font-size: 16px;text-decoration:none;	">	
						    						<span class="glyphicon glyphicon-home option_store_icon" aria-hidden="true"></span> {{$tienda->name}}
					    						</a>
							    			</div>			    				
						    			</div>
						    			<div class="col-md-12" >
						    				<div style="font-size: 16px;text-align: center;">
							    				<span class="glyphicon glyphicon-map-marker option_store_icon" aria-hidden="true"></span> {{$tienda->department}} - {{$tienda->city}}
							    			</div>						    			
							    			<div style="font-size: 16px;text-align: center;">
							    				{{$tienda->adress}}
							    			</div>		
						    			</div>  			
						    		</div>
						    	</div>
						    </div>				    
						</div>
					</div>	
				@endforeach
				</div>
			</div>

			<!--Div Presentaciòn de una tienda-->
			<div class="col-md-10 col-md-offset-1 comprarjuntos" style="margin-top: 3%;background-image: url('images/banner/banner_word.jpg');">
				<div class="row col-md-7 col-md-offset-1" style="font-size: 30px;">
					<div class="col-md-12 col-md-offset-0">
						Encuentra lo que buscas
					</div>
					<div class="col-md-12 col-md-offset-0">
						{!! Form::open(array('url' => '/','method'=>'get','class'=>'navbar-form navbar-left','onsubmit'=>'javascript:return seg_user.validateFinder()', 'style'=>'width: 100%;')) !!}
						   <div class="input-group" style="width: 100%;">
								{!! Form::text('finder','', array('class' => 'form-control','placeholder'=>'¿Que Estas Buscando?','style'=>'text-align: center;border: 1px solid #449aa2;','maxlength' => 48)) !!}
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">Buscar!</button>
								</span>
							</div>
					    {!! Form::close() !!}
				    </div>		    
				</div>

				<div class="row col-md-4 col-md-offset-0" style="font-size: 22px;text-align: center;">
					<div>Tiendas</div>
					<div>Productos</div>
					<div>Grupos de Consumo</div>
					<div>Tenderos</div>
				</div>
			</div>

			<!--Div de productos-->
			<div class="col-md-12 col-md-offset-0" style="margin-top: 3%;">
				<div class=" col-md-12  col-md-offset-0 title m-b-md center-block" style="font-size: 22px;">
					<b>Encuentra Productos y Consume Responsablemente</b>
				</div>
				<div class="col-md-12 col-md-offset-0" style="margin-top: 1%;" >
					@foreach($productos as $producto)
						<div class="col-md-3 col-mx-offset-1">
							<div class="panel panel-default">					
								<div class="panel-body">
							    	<div class="row">
							    		<div class="col-md-12">
							    			<a href="{{url('/'.$producto->store_name)}}">
							    				{{ Html::image('users/'.$producto->user_name.'/products/'.$producto->image1,'Imagen no disponible',array( 'style'=>'width: 100%;height: 150px;border-radius: 0%;' ))}}    				
							    			</a>				    			
							    		</div>
							    		{{--
							    		<div class="col-md-12"  style="background-color:{{$producto->color_one}}; color: {{$producto->color_two}}; border-color:{{$producto->color_two}};padding: 0px;">
							    		--}}
							    		<div class="col-md-12"  style="background-color:#fff; color: #777777; border-color:#777777;padding: 0px;">
							    			<div  class="col-md-12" style="padding: 0px;">
						    					<div style="text-align: center;">
						    						<a href="{{url('/'.$producto->name)}}" style="color:{{$producto->color_two}};font-size: 18px;text-decoration:none;	">	
							    						<span class="glyphicon glyphicon-home option_store_icon" aria-hidden="true"></span> {{$producto->name}}
						    						</a>
								    			</div>
								    			<div style="font-size: 14px;text-align: center;border-color:{{$tienda->color_two}};">
								    				<span class="glyphicon glyphicon-map-marker option_store_icon" aria-hidden="true"></span> {{$producto->store_city}} - {{$producto->store_adress}}
								    			</div>						    			
								    			<div style="font-size: 14px;text-align: center;border-color:{{$tienda->color_two}};">
								    				De la Tienda {{$producto->store_name}}
								    			</div>						    			
						    				</div>
						    				{{--
						    				<div class="col-md-3 hidden-xs">
						    					<a href="{{url('/'.$producto->name)}}" style="color:{{$producto->color_two}};font-size: 18px;">
							    					{{ Html::image('users/'.$producto->user_name.'/stores/'.$producto->store_image,'Imagen no disponible',array( 'style'=>'width: 130%;border-radius: 0%;' ))}}
							    				</a>
							    			</div>
							    			--}}		    			
							    		</div>
							    	</div>
							    </div>				    
							</div>
						</div>	
					@endforeach
				</div>
			</div>
		</div>
	</div>

	<!-- Para resoluciones de computador-->
	<div  class=" row col-md-9 col-md-offset-0 visible-lg" >
		<!-- Div de tiendas-->
		<div class="col-md-12 col-md-offset-0 " style="margin-top: 1%;">
			<div class=" col-md-12  col-md-offset-0 title m-b-md center-block" style="font-size: 22px;">
				<b>Encuentra Tiendas y Crea Redes de Consumo</b>
			</div>
			<div class="col-md-12 col-md-offset-0" style="margin-top: 1%;" >
			@foreach($tiendas as $tienda)
				<div class="col-md-3 col-mx-offset-1">
					<div class="panel panel-default" style="border-color:{{$tienda->color_two}};">					
						<div class="panel-body">
					    	<div class="row">
					    		<div class="col-md-12 popoverStore" data-content="<div>{{$tienda->description}}</div><div><b>Tendero:</b> {{$tienda->tnames}} {{$tienda->tsurnames}}</div>" rel="popover" data-placement="bottom" data-original-title="{{$tienda->name}}" data-trigger="hover" data-html="true">
					    			<a href="{{url('/'.$tienda->name)}}">
					    				{{ Html::image('users/'.$tienda->user_name.'/stores/'.$tienda->image,'Imagen no disponible',array( 'style'=>'width: 100%;height: 150px;border-radius: 0%;' ))}}	    				
					    			</a>					    				    									    			
					    		</div>
					    		<!--
					    		<a href="{{url('/'.$tienda->name)}}" style="color:{{$tienda->color_two}};font-size: 16px;"> 
			    					{{ Html::image('users/'.$tienda->user_name.'/profile/'.$tienda->avatar,'Imagen no disponible',array('class'=>'img_tendero', 'style'=>'width: 35%;border-radius: 50%;position: absolute; margin-left: 40%;z-index: 99;' ))}}
			    				</a>
			    				-->
					    		<div class="col-md-12"  style="background-color:{{$tienda->color_one}}; color: {{$tienda->color_two}}; border-color:
					    	{{$tienda->color_two}};padding: 0px;">					    			
					    			<div class="col-md-12" style="padding: 0px;">				    				
				    					<div style="text-align: center;">
				    						<a href="{{url('/'.$tienda->name)}}" style="color:{{$tienda->color_two}};font-size: 16px;text-decoration:none;	">	
					    						<span class="glyphicon glyphicon-home option_store_icon" aria-hidden="true"></span> {{$tienda->name}}
				    						</a>
						    			</div>			    				
					    			</div>
					    			<div class="col-md-12" >
					    				<div style="font-size: 16px;text-align: center;">
						    				<span class="glyphicon glyphicon-map-marker option_store_icon" aria-hidden="true"></span> {{$tienda->department}} - {{$tienda->city}}
						    			</div>						    			
						    			<div style="font-size: 16px;text-align: center;">
						    				{{$tienda->adress}}
						    			</div>		
					    			</div>  			
					    		</div>
					    	</div>
					    </div>				    
					</div>
				</div>	
			@endforeach
			</div>
		</div>

		<!--Div Presentaciòn de una tienda-->
		<div class="col-md-10 col-md-offset-1 comprarjuntos" style="margin-top: 3%;background-image: url('images/banner/banner_word.jpg');">
			<div class="row col-md-7 col-md-offset-1" style="font-size: 30px;">
				<div class="col-md-12 col-md-offset-0">
					Encuentra lo que buscas
				</div>
				<div class="col-md-12 col-md-offset-0">
					{!! Form::open(array('url' => '/','method'=>'get','class'=>'navbar-form navbar-left visible-lg','onsubmit'=>'javascript:return seg_user.validateFinder()', 'style'=>'width: 100%;')) !!}
					   <div class="input-group" style="width: 100%;">
							{!! Form::text('finder','', array('class' => 'form-control','placeholder'=>'¿Que Estas Buscando?','style'=>'text-align: center;border: 1px solid #449aa2;','maxlength' => 48)) !!}
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">Buscar!</button>
							</span>
						</div>
				    {!! Form::close() !!}
			    </div>		    
			</div>

			<div class="row col-md-4 col-md-offset-0" style="font-size: 22px;text-align: center;">
				<div>Tiendas</div>
				<div>Productos</div>
				<!--<div>Grupos de Consumo</div>-->
				<div>Tenderos</div>
			</div>
		</div>

		<!--Div de productos-->
		<div class="col-md-12 col-md-offset-0" style="margin-top: 3%;">
			<div class=" col-md-12  col-md-offset-0 title m-b-md center-block" style="font-size: 22px;">
				<b>Encuentra Productos y Consume Responsablemente</b>
			</div>
			<div class="col-md-12 col-md-offset-0" style="margin-top: 1%;" >
				@php ($p=0)
				@php ($j=1)
				@foreach($productos as $producto)						
					@if($p%4==0)
						<div class="col-md-12 col-md-offset-0">
					@endif
					<div class="col-md-3 col-mx-offset-1">
						<div class="panel panel-default">					
							<div class="panel-body">
						    	<div class="row">
						    		<div class="col-md-12 popoverStore" data-content="<div><b>Precio:</b> ${{$producto->price}}</div>@if($producto->colors)<div> Colores: {{$producto->colors}}</div>@endif @if($producto->sizes)<div> Tamaños: {{$producto->sizes}}</div>@endif @if($producto->flavors)<div> Sabores: {{$producto->flavors}}</div>@endif @if($producto->materials)<div> Materiales: {{$producto->materials}}</div>@endif" rel="popover" data-placement="bottom" data-original-title="{{$producto->name}}" data-trigger="hover" data-html="true">
						    			<a href="{{url('/'.$producto->store_name)}}">
						    				{{ Html::image('users/'.$producto->user_name.'/products/'.$producto->image1,'Imagen no disponible',array( 'style'=>'width: 100%;height: 150px;border-radius: 0%;' ))}}    				
						    			</a>				    			
						    		</div>
						    		{{--
						    		<div class="col-md-12"  style="background-color:{{$producto->color_one}}; color: {{$producto->color_two}}; border-color:{{$producto->color_two}};padding: 0px;">
						    		--}}
						    		<div class="col-md-12"  style="background-color:#fff; color: #777777; border-color:#777777;padding: 0px;">
						    			<div  class="col-md-12" style="padding: 0px;">
					    					<div style="text-align: center;">
					    						<a href="{{url('/'.$producto->name)}}" style="color:{{$producto->color_two}};font-size: 18px;text-decoration:none;	">	
						    						<span class="glyphicon glyphicon-home option_store_icon" aria-hidden="true"></span> {{$producto->name}}
					    						</a>
							    			</div>
							    			<div style="font-size: 14px;text-align: center;border-color:{{$tienda->color_two}};">
							    				<span class="glyphicon glyphicon-map-marker option_store_icon" aria-hidden="true"></span> {{$producto->store_city}} - {{$producto->store_adress}}
							    			</div>						    			
							    			<div style="font-size: 14px;text-align: center;border-color:{{$tienda->color_two}};">
							    				De la Tienda {{$producto->store_name}}
							    			</div>						    			
					    				</div>
					    				{{--
					    				<div class="col-md-3 hidden-xs">
					    					<a href="{{url('/'.$producto->name)}}" style="color:{{$producto->color_two}};font-size: 18px;">
						    					{{ Html::image('users/'.$producto->user_name.'/stores/'.$producto->store_image,'Imagen no disponible',array( 'style'=>'width: 130%;border-radius: 0%;' ))}}
						    				</a>
						    			</div>
						    			--}}		    			
						    		</div>
						    	</div>
						    </div>				    
						</div>
					</div>
					@if($j%4==0)
						</div>							
					@elseif($p == count($productos)-1)
						</div>
					@endif
					@php ($p++)
					@php ($j++)
				@endforeach
			</div>
		</div>
	</div>

	<div class="row col-md-3 col-md-offset-0 visible-lg" >
		<div class="col-md-12 col-md-offset-0 " style="border: 1px solid #449AA2;">
			<div class=" col-md-12  col-md-offset-0 title m-b-md center-block" style="font-size: 22px;">
				<b>¿Que es ComprarJuntos?</b>
			</div>
			<div class="row col-md-12 col-md-offset-0" style="text-align: justify;">
				ComprarJuntos es un proyecto para fomentar el consumo responsable. Es una herramienta para favorecer el cambio y promover la economia solidaria. La idea es que la gente pueda unirse a Grupos de Consumo existentes o puedan crear nuevos Grupos de Consumo con compañeros, vecinos o amigos. Se da la posibilidad a productoras y productores de promocionar sus productos y comunicar tanto con grupos de consumo como con otros productores para intercambiar información. Facilitamos el sistema de pedidos para que todo el proceso sea más sencillo y rápido tanto para los grupos de consumo como para los productores. Es soberanía alimentaria: volvamos a ser dueños de nuestra comida, compremos a través de circuitos locales, compremos productos de comercio justo, ¡Compremos Ecológico, Compremos Juntos!
			</div>
		</div>		
	</div>
	

@endsection

@section('modal')
	
	<!-- Modal para cambiar de pasword -->
	<div class="modal fade" id="cpsw_modal" role="dialog" >
	    <div class="modal-dialog  modal-sm">    
	      <!-- Modal content-->
	      <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Cambiar contraseña</h4>
				</div>
				<div class = "alerts-module"></div>				
				<div class="modal-body">
					<div class="row ">
						<div class="col-md-12 col-md-offset-0 row_init">
							{!! Form::open(array('id'=>'cpsw','url' => '/cambiarcontraseña','method'=>'get','onsubmit'=>'javascript:return seg_user.validatePassword()')) !!}
				        		<div class="form-group">
									{!! Form::hidden('usuario', Session::get('user.name')) !!}
									{!! Form::label('contraseña_uno', 'Contraseña', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::password('contraseña', array('class' => 'form-control','placeholder'=>'Ingresa tu nueva contraseña', 'autofocus'=>'autofocus')) !!}
									</div>
									
									{!! Form::label('contraseña_dos', 'Contraseña Nuevamente', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12" data-toggle="modal" data-target="#rpsw_modal">
										{!! Form::password('contraseña_dos', array('class' => 'form-control','placeholder'=>'Ingresa nuevamente tu contraseña')) !!}
									</div>
								</div>
					        {!! Form::close() !!}
						</div>						
					</div>
		        </div>
		        <div class="modal-footer">
		          <button type="submit" form = "cpsw" class="btn btn-default " >Enviar</button>
		          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>	                  
		        </div>     
	      </div>
      </div>
	</div>

	@if (Auth::guest())
	<!-- Modals para invitados -->

	<!--Modal para login-->
	<div class="modal fade" id="login_modal" role="dialog" >
	    <div class="modal-dialog  modal-sm">	    
	      <!-- Modal content-->
	      <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Ingreso a ComprarJuntos</h4>
				</div>
				<div class = "alerts-module"></div>				
				<div class="modal-body">
					<div class="row ">
						<div class="col-md-12 col-md-offset-0 row_init">
							{!! Form::open(array('url' => '/ingreso', 'method' => 'get','id'=>'login','onsubmit'=>'javascript:return seg_user.validateLogin()')) !!}
								<div class="panel-body">
												
									<div class="form-group">
										{!! Form::label('usuario', 'Usuario', array('class' => 'col-md-12 control-label')) !!}						
										<div class="col-md-12">
											{!! Form::text('usuario', old('usuario'), array('class' => 'form-control','placeholder'=>'Ingresa tu nombre usuario', 'autofocus'=>'autofocus'))!!}
										</div>
									</div>
			
									<div class="form-group">
										{!! Form::label('contraseña', 'Contraseña', array('class' => 'col-md-12 control-label')) !!}
										<div class="col-md-12">
											{!! Form::password('contraseña', array('class' => 'form-control','placeholder'=>'Ingresa tu contraseña')) !!}
										</div>
									</div>

									<div class="col-md-12" data-toggle="modal" data-target="#rpsw_modal" style="margin-top: 10px; font-size: 16px;">
										<a href="#">Recuperar Contraseña</a>
									</div>
								</div>							
			
							{!! Form::close() !!}
						</div>						
					</div>
		        </div>
		        <div class="modal-footer">
		          <button type="submit" form = "login" class="btn btn-default " >Ingresar</button>
		          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>		                  
		        </div>     
	      </div>
      </div>
	</div>
	
	<!-- Para recuperar el pasword por medio del corro electronico -->
	<div class="modal fade" id="rpsw_modal" role="dialog" >
	    <div class="modal-dialog  modal-sm">	    
	      <!-- Modal content-->
	      <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Recuperar contraseña</h4>
				</div>
				<div class = "alerts-module"></div>				
				<div class="modal-body">
					<div class="row ">
						<div class="col-md-12 col-md-offset-0 row_init">
							{!! Form::open(array('id'=>'rpsw','url' => '/recuperarcontraseña','method'=>'get')) !!}
				        		<div class="form-group">
									{!! Form::label('email', 'Correo Electronico', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::email('email','', array('class' => 'form-control','placeholder'=>'Ingresa tu email', 'autofocus'=>'autofocus')) !!}
									</div>
								</div>
								      
					        {!! Form::close() !!}
						</div>						
					</div>
		        </div>
		        <div class="modal-footer">
		          <button type="submit" form = "rpsw" class="btn btn-default " >Enviar</button>
		          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>		                  
		        </div>     
	      </div>
      </div>
	</div>
	
	<!--Modal para el Formulario de registro-->
	<div class="modal fade" id="registry_modal" role="dialog" >
	    <div class="modal-dialog  modal-sm">	    
	      <!-- Modal content-->
	      <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Formulario de Registro</h4>
				</div>
				<div class = "alerts-module"></div>				
				<div class="modal-body">
					<div class="row" style="margin-bottom: 15px;">
						<div class="col-md-12 col-md-offset-0 row_init">
							{!! Form::open(array('id'=>'registry','url' => '/registro','method'=>'get','onsubmit'=>'javascript:return seg_user.validateRegistry()')) !!}
				        		<div class="form-group">				        		

									{!! Form::label('usuario', 'Usuario', array('class' => 'col-md-12 control-label')) !!}						
									<div class="col-md-12">
										{!! Form::text('usuario', old('usuario'), array('class' => 'form-control','placeholder'=>'Ingresa tu nombre usuario', 'autofocus'=>'autofocus'))!!}
									</div>
									
									{!! Form::label('email', 'Correo Electronico', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::email('email','', array('class' => 'form-control','placeholder'=>'Ingresa tu email, no es obligatorio')) !!}
									</div>			
									
									{!! Form::label('contraseña_uno', 'Contraseña', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::password('contraseña_uno', array('class' => 'form-control','placeholder'=>'Ingresa tu contraseña')) !!}
									</div>
									
									{!! Form::label('contraseña_dos', 'Contraseña Nuevamente', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::password('contraseña_dos', array('class' => 'form-control','placeholder'=>'Ingresa nuevamente tu contraseña')) !!}
									</div>									
								</div>								      
					        {!! Form::close() !!}					        
						</div>									
					</div>
					<a href="{{ url('/welcome/terminosycondiciones')}}"  target="_blank" style="font-size: 16px;margin:auto;">Terminos y Condiciones</a>		
		        </div>
		        <div class="modal-footer">		          
		          <button type="submit" form = "registry" class="btn btn-default " >Enviar</button>
		          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>		                  
		        </div>     
	      </div>
      </div>
	</div>

	@else
	<!-- Modal para usuarios logueados-->
	<!-- Modal para editar datos de perfil -->
	<div class="modal fade" id="cpep_modal" role="dialog" >
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Editar Datos de Perfil {{Session::get('comjunplus.usuario.name')}}</h4>
				</div>
				<div class = "alerts-module"></div>	
				<div class="modal-body col-md-12">
					
					{!! Form::open(array('id'=>'cpfep','url' => '/editarperfil','method'=>'post','files'=>true,'onsubmit'=>'javascript:return seg_user.validateEditPerfil()')) !!}
					<div class="row col-md-12 ">
					{!! Form::hidden('usuario_id', Session::get('comjunplus.usuario.id')) !!}	
					<div class="col-md-4">																			
						<div class="form-group ">														
							{!! Form::label('usuario', 'Usuario', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('usuario',value(Session::get('comjunplus.usuario.name')), array('class' => 'form-control','placeholder'=>'Ingresa tu nombre de usuario','disabled'=>'disabled')) !!}
							</div>
							
							{!! Form::label('nombres', 'Nombres', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('nombres',value(Session::get('comjunplus.usuario.names')), array('class' => 'form-control','placeholder'=>'Nombres completos', 'autofocus'=>'autofocus')) !!}
							</div>
							
							{!! Form::label('apellidos', 'Apellidos', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('apellidos',value(Session::get('comjunplus.usuario.surnames')), array('class' => 'form-control','placeholder'=>'Apellidos completos')) !!}
							</div>
							
							{!! Form::label('identificacion', 'Identificación', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('identificacion',value(Session::get('comjunplus.usuario.identificacion')), array('class' => 'form-control','placeholder'=>'C.C, C.E ó T.I')) !!}
							</div>
							
							{!! Form::label('fecha_nacimiento', 'Fecha de Nacimiento', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">																
								{!! Form::text('fecha_nacimiento',value(Session::get('comjunplus.usuario.birthdate')), array('class' => 'form-control','placeholder'=>'aaaa-mm-dd')) !!}								
							</div>	
							
							{!! Form::label('correo_electronico', 'Correo Electronico', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('correo_electronico',value(Session::get('comjunplus.usuario.email')), array('class' => 'form-control','placeholder'=>'Ingresa tu email')) !!}
							</div>
																							
						</div>						
					</div>
					
					<div class=" col-md-4 ">						
						<div class="form-group ">								
													
							{!! Form::label('departamento', 'Departamento', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::select('departamento',$departamentos,value(Session::get('comjunplus.usuario.state')), array('class' => 'form-control','placeholder'=>'Departamento de residencia')) !!}
							</div>
							
							{!! Form::label('municipio', 'Municipio', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::select('municipio',$ciudades,value(Session::get('comjunplus.usuario.city')), array('class' => 'form-control','placeholder'=>'Municipio de recidencia')) !!}
							</div>
							
							{!! Form::label('direccion', 'Dirección', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('direccion',value(Session::get('comjunplus.usuario.adress')), array('class' => 'form-control','placeholder'=>'Dirección Recidencial')) !!}
							</div>	
							
							{!! Form::label('telefono_movil', 'Teléfono Fijo', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('telefono_fijo',value(Session::get('comjunplus.usuario.fix_number')), array('class' => 'form-control','placeholder'=>'Ingresa tu Fijo')) !!}
							</div>
							
							{!! Form::label('telefono_movil', 'Teléfono Móvil', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::text('telefono_movil',value(Session::get('comjunplus.usuario.movil_number')), array('class' => 'form-control','placeholder'=>'Ingresa tu Celular')) !!}
							</div>
							
							{!! Form::label('fuente', 'Fuente Tipográfica', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								{!! Form::select('fuente_tipografica',array('default' => 'default', 'flowers' => 'flowers'),value(Session::get('comjunplus.usuario.template')), array('class' => 'form-control','placeholder'=>'Elige Una fuente tipografica')) !!}			
							</div>
																						
						</div>						
					</div>

					<div class=" col-md-4 ">	
						<div class="form-group ">
							{!! Form::label('img_user', 'Imagen de Usuario', array('class' => 'col-md-12 control-label')) !!}
							{{ Html::image('users/'.Session::get('comjunplus.usuario.name').'/profile/'.Session::get('comjunplus.usuario.avatar'),'Imagen no disponible',array( 'style'=>'width: 100%; border:2px solid #ddd;border-radius: 0%;' ))}}
							
						</div>
						<div>
							{!! Form::file('image',array('id'=>'img_user','style'=>'font-size: 14px;')) !!}
						</div>
					</div>
					
					</div>
					
					{!! Form::close() !!}
					</div><!-- Cierre de modal body -->				
				
				<div class="modal-footer">
		          <button type="submit" form = "cpfep" class="btn btn-default " >Enviar</button>
		          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>		                  
		        </div>
		         
			</div>
		 </div>
	</div>

	<!--Modal para el Formulario de registro-->
	<div class="modal fade" id="registry_modal" role="dialog" >
	    <div class="modal-dialog  modal-sm">	    
	      <!-- Modal content-->
	      <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Formulario de Registro</h4>
				</div>
				<div class = "alerts-module"></div>				
				<div class="modal-body">
					<div class="row" style="margin-bottom: 15px;">
						<div class="col-md-12 col-md-offset-0 row_init">
							{!! Form::open(array('id'=>'registry','url' => '/registro','method'=>'get','onsubmit'=>'javascript:return seg_user.validateRegistry()')) !!}
				        		<div class="form-group">				        		

									{!! Form::label('usuario', 'Usuario', array('class' => 'col-md-12 control-label')) !!}						
									<div class="col-md-12">
										{!! Form::text('usuario', old('usuario'), array('class' => 'form-control','placeholder'=>'Ingresa tu nombre usuario', 'autofocus'=>'autofocus'))!!}
									</div>
									
									{!! Form::label('email', 'Correo Electronico', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::email('email','', array('class' => 'form-control','placeholder'=>'Ingresa tu email, no es obligatorio')) !!}
									</div>			
									
									{!! Form::label('contraseña_uno', 'Contraseña', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::password('contraseña_uno', array('class' => 'form-control','placeholder'=>'Ingresa tu contraseña')) !!}
									</div>
									
									{!! Form::label('contraseña_dos', 'Contraseña Nuevamente', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::password('contraseña_dos', array('class' => 'form-control','placeholder'=>'Ingresa nuevamente tu contraseña')) !!}
									</div>									
								</div>								      
					        {!! Form::close() !!}					        
						</div>									
					</div>
					<a href="{{ url('/welcome/terminosycondiciones')}}"  target="_blank" style="font-size: 16px;margin:auto;">Terminos y Condiciones</a>		
		        </div>
		        <div class="modal-footer">		          
		          <button type="submit" form = "registry" class="btn btn-default " >Enviar</button>
		          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>		                  
		        </div>     
	      </div>
      </div>
	</div>
	@endif

	<!--Modal generico para mensajes a tendero-->
	@if(Session::has('orden_data'))
	<div class="modal fade" id="message_order_modal" role="dialog" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id = "ordmes_title" >Mensaje para Tienda</h4>
				</div>
				<div class = "alerts-module"></div>				
				<div class="modal-body">					
					<div class="row ">
						@if(count(Session::get("orden_data")["annotations"]))
							<div class="col-md-12" id="msg_annotations">
								<div class="col-md-12" style="border-bottom: 1px solid black;">
									<div class="col-md-3"> Usuarios</div>
									<div class="col-md-6"> Descripciòn</div>
									<div class="col-md-3"> Fecha</div>
								</div>
								@foreach(Session::get("orden_data")["annotations"] as $annotation)
									<div class="col-md-12">
										<div class="col-md-3"> {{$annotation->user_name}}</div>
										<div class="col-md-6"> {{$annotation->description}}</div>
										<div class="col-md-3"> {{$annotation->date}}</div>
									</div>
								@endforeach							
							</div>							
						@endif
						<div class="col-md-12 col-md-offset-0 row_init">
							{!! Form::open(array('id'=>'formordmess','url' => 'welcome/messageorder','method'=>'post','onsubmit'=>'javascript:return seg_user.validateMessageOrder()')) !!}
								{!! Form::hidden('msg_usuario_id',null,array('id'=>'msg_usuario_id')) !!}	
								{!! Form::hidden('msg_orden_id',null,array('id'=>'msg_orden_id')) !!}
								{!! Form::hidden('msg_store_id',null,array('id'=>'msg_store_id')) !!}
								{!! Form::textarea('message_orden_text',null, array('class' => 'form-control','rows' => 3,'placeholder'=>'Escribe aqui el mensaje para el tendero.')) !!}
							 {!! Form::close() !!}
						</div>
					</div>
				</div>
				<div class="modal-footer">
		          <button type="submit" form = "formordmess" class="btn btn-default " >Enviar</button>		          	                  
		        </div>
			</div>
		</div>
	</div>
	@endif

	@if(Session::has('orden_data_resena'))	
	<div class="modal fade" id="resena_order_modal" role="dialog" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id = "ordmes_title" >Cuantifica el servicio de la Tienda {{ucwords(Session::get('orden_data_resena')['orden'][0]->store)}}!</h4>
				</div>
				<div class = "alerts-module"></div>				
				<div class="modal-body" style="text-align: center;">					
					<div class="row ">						
						<div class="col-md-12 col-md-offset-0 row_init">
							{!! Form::open(array('id'=>'formordrsn','url' => 'welcome/reseniaorder','method'=>'post','onsubmit'=>'javascript:return seg_user.validateReseniaOrder()')) !!}
								{!! Form::hidden('rsn_usuario_id',null,array('id'=>'rsn_usuario_id')) !!}	
								{!! Form::hidden('rsn_orden_id',null,array('id'=>'rsn_orden_id')) !!}
								{!! Form::hidden('rsn_store_id',null,array('id'=>'rsn_store_id')) !!}
								{!! Form::hidden('rsn_resenia',null,array('id'=>'rsn_resenia')) !!}								

								{{--
									{{ Form::radio('resenia', 'true') }} Si<br>
									{{ Form::radio('resenia', 'none', true) }} No estoy Seguro<br>
									{{ Form::radio('resenia', 'false') }} No <br>
								--}}

								<span class="rating" style="font-size: 24px;">
						        	<span id="star_1" class="star  glyphicon glyphicon-star"></span>
						        	<span id="star_2" class="star  glyphicon glyphicon-star"></span>
						        	<span id="star_3" class="star  glyphicon glyphicon-star"></span>
						        	<span id="star_4" class="star  glyphicon glyphicon-star-empty"></span>
						        	<span id="star_5" class="star  glyphicon glyphicon-star-empty"></span>
						        </span><br>
						        <span>
						        	Servicio <span id="service_text" style="color:#ffcc00">Regular</span>
						        </span>
						        <br>
						        <br>

								{!! Form::textarea('rsn_resenia_text',null, array('class' => 'form-control','rows' => 3,'placeholder'=>'Comparte tu opinión sobre la tienda y su servicio.')) !!}
							 {!! Form::close() !!}
						</div>
					</div>
				</div>
				<div class="modal-footer">
		          <button type="submit" form = "formordrsn" class="btn btn-default " >Enviar</button>		          	                  
		        </div>
			</div>
		</div>
	</div>
	@endif

	{!! Form::open(array('id'=>'form_consult_city','url' => 'user/consultarcity')) !!}		
    {!! Form::close() !!}
    {!! Form::open(array('id'=>'form_home','url' => '/')) !!}		
    {!! Form::close() !!}

@endsection

@section('script')
	<!-- Cambio de Contraseña -->
	@if(Session::has('user'))		
		<script> $("#cpsw_modal").modal(); </script>
	@endif
	<!-- Perfil de usuario -->
	@if(Session::has('message'))
		@if(in_array('Perfil2',Session::get('message')))
			<script> 
				$("#cpep_modal").modal(); 
				$('#cpep_modal .alerts-module').html('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>!El perfil de usuario esta incompleto!</strong> Faltan campos por diligenciar.</div>');
			</script>
		@endif
		@if(in_array('Perfil3',Session::get('message')))
			<script> 
				$("#cpep_modal").modal();
				$('#cpep_modal .alerts-module').html('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>!El perfil de usuario esta incompleto!</strong> Para crear tu primer tienda primero debes diligenciar la informaciòn de tu perfil de usuario.</div>');
			</script>
		@endif		
	@endif
	<!-- Modals especiales opcionalmente activados-->
	@if(Session::has('modal'))
		@if(Session::get('modal') == 'modalregistro')
			<script> $("#registry_modal").modal(); </script>
		@endif

		@if(Session::get('modal') == 'modallogin')
			<script> 
				$("#login_modal").modal(); 
				$('#login_modal .alerts-module').html('<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>Luego de ingresar, se mostrara en pantalla el pedido solicitado.</div>');
			</script>			
			{{Session::flash('orden_id', Session::get('orden_id'))}}
		@endif

		@if(Session::get('modal') == 'modalmessagetotender')
			<script>
				$("#message_order_modal").modal(); 
				$('#ordmes_title').html('Mensaje para tienda -  {{ucwords(Session::get("orden_data")["orden"][0]->store)}} ({{Session::get("orden_data")["orden"][0]->names}} {{Session::get("orden_data")["orden"][0]->surnames}})');
				$('#msg_usuario_id').val({{Session::get("orden_data")["orden"][0]->user_id}});
				$('#msg_orden_id').val({{Session::get("orden_data")["orden"][0]->id}});				
				$('#msg_store_id').val({{Session::get("orden_data")["orden"][0]->store_id}});				

			</script>
		@endif

		@if(Session::get('modal') == 'modalresenatostore')
			<script> 
				$("#resena_order_modal").modal(); 
				$('#rsn_usuario_id').val({{Session::get("orden_data_resena")["orden"][0]->user_id}});
				$('#rsn_orden_id').val({{Session::get("orden_data_resena")["orden"][0]->id}});				
				$('#rsn_store_id').val({{Session::get("orden_data_resena")["orden"][0]->store_id}});
			</script>

		@endif	
	@endif	

	<script type="text/javascript">  
		$('#fecha_nacimiento').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,			
			language: "es"
		});

		$( "#departamento" ).change(function() {
			var datos = new Array();
			datos['id'] =$( "#departamento option:selected" ).val();			   
			seg_ajaxobject.peticionajax($('#form_consult_city').attr('action'),datos,"seg_user.consultaRespuestaCity");
		});

		
		$('[data-toggle="popover"]').popover({
			html: true,
	        trigger: 'manual',			
			container: 'body'
		 }).on('click', function(e) {
		 	$('[data-toggle="popover"]').each(function () {
		        //the 'is' for buttons that trigger popups
		        //the 'has' for icons within a button that triggers a popup
		        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
		            $(this).popover('hide');
		        }
		    });	        
		 	$(this).popover('show');
		 });
		
		//esta funcion es para que el popover se cierre cuando demos clic fuera de el 
		$(document).on('click', function(e) {
			
	        $('[data-toggle="popover"]').each(function () {
		        //the 'is' for buttons that trigger popups
		        //the 'has' for icons within a button that triggers a popup
		        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
		            $(this).popover('hide');
		        }
		    });

		    //redirecciòn de subcategorias
			$('.popover-content ul li').on('click', function(e) {		        
		        window.location=$('#form_home').attr('action')+"/"+this.textContent;
		    });

			//redirecciòn de categorias
		    $('.popover-title').on('click', function(e) {		        
		        window.location=$('#form_home').attr('action')+"/"+this.textContent;
		    });
	    });

	   	$('.popoverStore').popover();
	    //$(".popoverStore").popover({ trigger: "hover focus" });

		$('#rsn_resenia').val(3);
	    $(".star").mouseover(function() {
		  	for(var i=1;i<=$(this)[0].id.split("_")[1];i++){
		  		//vamos cambiar todo ha para atras
		  		$('#star_'+i).removeClass().addClass('start glyphicon glyphicon-star');
		  	}
		  	for(var i=5;i>$(this)[0].id.split("_")[1];i--){
		  		//vamos cambiar todo ha para atras
		  		$('#star_'+i).removeClass().addClass('start glyphicon glyphicon-star-empty');
		  	}

		  	if($(this)[0].id.split("_")[1] == "1"){
		  		$('#service_text').text('Muy Malo');
		  		$('#service_text').css('color','red');
		  	}
		  	if($(this)[0].id.split("_")[1] == "2"){
		  		$('#service_text').text('Malo');
		  		$('#service_text').css('color','#ff9900');
		  	}
		  	if($(this)[0].id.split("_")[1] == "3"){
		  		$('#service_text').text('Regular');
		  		$('#service_text').css('color','#ffcc00');
		  	}
		  	if($(this)[0].id.split("_")[1] == "4"){
		  		$('#service_text').text('Bueno');
		  		$('#service_text').css('color','#66ccff');
		  	}
		  	if($(this)[0].id.split("_")[1] == "5"){
		  		$('#service_text').text('Muy Bueno');
		  		$('#service_text').css('color','#00cc66');
		  	}
		  	$('#rsn_resenia').val($(this)[0].id.split("_")[1]);			  	
		  })		  
	</script>
@endsection
