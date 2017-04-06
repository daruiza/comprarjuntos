function clu_tienda() {
	this.datos_pie = [];
	this.table_products = '';
	this.table_orders = '';
	this.fillable = ['Nombre','Precio','Categorìa','Descripciòn'];
	
}

clu_tienda.prototype.onjquery = function() {
};

clu_tienda.prototype.opt_select = function(controlador,metodo) {
	
	if(clu_tienda.table_products.rows('.selected').data().length){		
		window.location=metodo + "/" + clu_tienda.table_products.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

clu_tienda.prototype.validateNuevaTienda = function() {
	
	if($("#form_nueva_tienda :input")[1].value =="" || $("#form_nueva_tienda :input")[2].value =="" || $("#form_nueva_tienda :input")[3].value =="" || $("#form_nueva_tienda :input")[4].value ==""  || $("#form_nueva_tienda :input")[8].value ==""){
		$('#nuevatienda_modal .alerts-module').html('<div class="alert alert-warning alert-dismissable"><button type="button" class="close close_alert_edit_perfil" data-dismiss="alert">&times;</button><strong>!Envio Fallido!</strong></br> Faltan campos por diligenciar.</div>');
		//pintar los inputs problematicos
		for(var i=0; i < $("#form_nueva_tienda :input").length ; i++){
	        if( i==1 || i==2 || i==3 || i==4 || i==8) {
	            if($("#form_nueva_tienda :input")[i].value ==""){
	                $($("#form_nueva_tienda :input")[i]).addClass('input_danger');
	            }
	            if($("#form_nueva_tienda :input")[8].value ==""){
	            	$(".categorias").addClass('input_danger');
	            }
	        }
        }
        $(".close_alert_edit_perfil").on('click', function () { 
        	$("#form_nueva_tienda :input").removeClass("input_danger");
        	$(".categorias").removeClass('input_danger');
        });
		return false;
	}
	
	return true;
};

clu_tienda.prototype.validateNuevoProducto = function() {
	if($("#form_nuevo_producto :input")[1].value =="" || $("#form_nuevo_producto :input")[2].value ==""){
		$('#nuevoproducto_modal .alerts-module').html('<div class="alert alert-warning alert-dismissable"><button type="button" class="close close_alert_product" data-dismiss="alert">&times;</button><strong>!Envio Fallido!</strong></br> Faltan campos por diligenciar.</div>');
		//pintar los inputs problematicos
		for(var i=0; i < $("#form_nuevo_producto :input").length ; i++){
	        if( i==1 || i==2 ) {
	            if($("#form_nuevo_producto :input")[i].value ==""){
	                $($("#form_nuevo_producto :input")[i]).addClass('input_danger');
	            }	            
	        }
        }
        $(".close_alert_product").on('click', function () { 
        	$("#form_nuevo_producto :input").removeClass("input_danger");        	
        });
		return false;
	}
	return true;
}

clu_tienda.prototype.consultaRespuestaProducts = function(result) {

	$('#productos_modal .modal-title').html('Productos Tienda '+result.request.name);
	if(!result.productos){
		$('#productos_modal .alerts-module').html('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>!La tienda aùn no tiene Productos!</strong></br> Para crear un nuevo producto, no esperes màs y crea un producto dando click en la opciòn <a href="#"><div class="" id="btn_nueva_tienda_a" data-toggle="modal" data-target="#nuevoproducto_modal"><b> Crear un Producto</b></div></a></div>');
	}
	$('#productos_modal').modal();
	//llenamos el select de categorias, para la creación de nuevos productos, de esta tienda seleccionada
	categoria_select=document.getElementById('categoria_select')
	categoria_select.innerHTML = "";
	for (var key in result.data) {
		var opt = document.createElement('option');
		opt.value = key;
		opt.innerHTML = result.data[key];
		categoria_select.appendChild(opt);
	}
	//actualiza el select dinamico
	$('#categoria_select').trigger("chosen:updated");
	
	$('#productos_modal').modal();
};

clu_tienda.prototype.consultaRespuestaProduct = function(result) {
	clu_tienda.row.child( clu_tienda.format(clu_tienda.row.data(),result.request)).show();
	$('.editproduct').on('click', function (e) {
		//buscamos los datos seleccionados
		for(var i=0; clu_tienda.table_products.data().length; i++){
			if(this.className.split(' ')[0].split('_')[3]==clu_tienda.table_products.data()[i].id){
				//encontramos los datos
				$('#modal-title-product').html('Editar Producto');
				$( "input[name='edit_product']").val(true);
				$( "input[name='product_id']").val(clu_tienda.table_products.data()[i].id);
				$('#nombre_producto').val(clu_tienda.table_products.data()[i].name);
				$('#precio').val(clu_tienda.table_products.data()[i].price);
				$('#categoria_select').val(clu_tienda.table_products.data()[i].category);
				$('#descripcion_producto').val(clu_tienda.table_products.data()[i].description);
				$('#prioridad_producto').val(clu_tienda.table_products.data()[i].order);				
				//imagen, se reemplaza el src del elemento
				$('#img_product').attr('src',$('#img_product').attr('src').replace($('#img_product').attr('src').split('/')[$('#img_product').attr('src').split('/').length-1],clu_tienda.table_products.data()[i].image1));
				$('#unidades_select').val(clu_tienda.table_products.data()[i].unity_measure);
				$('#unidades_medida').val(clu_tienda.table_products.data()[i].unity_measure);
				$('#colores_select').val(clu_tienda.table_products.data()[i].colors);
				$('#colores').val(clu_tienda.table_products.data()[i].colors);
				$('#tallas_select').val(clu_tienda.table_products.data()[i].sizes);
				$('#tallas').val(clu_tienda.table_products.data()[i].sizes);
				$('#sabores_select').val(clu_tienda.table_products.data()[i].flavors);
				$('#sabores').val(clu_tienda.table_products.data()[i].flavors);
				$('#materiales_select').val(clu_tienda.table_products.data()[i].flavors);
				$('#materiales').val(clu_tienda.table_products.data()[i].materials);
				$('#modelos').val(clu_tienda.table_products.data()[i].models);
				$('.estado-roduct').show();
				$('input[name=estado_producto][value='+clu_tienda.table_products.data()[i].active+']').attr("checked", "checked");
				$('#modal-button-product').html('Editar Producto')

				//Mostrar Modal
				$('#nuevoproducto_modal').modal();
				//para hacer efectivo el cambio del chossen
				$('#categoria_select').trigger("chosen:updated");
				$('#unidades_select').val(clu_tienda.table_products.data()[i].unity_measure.split(',')).trigger("chosen:updated");
				$('#colores_select').val(clu_tienda.table_products.data()[i].colors.split(',')).trigger("chosen:updated");
				$('#tallas_select').val(clu_tienda.table_products.data()[i].sizes.split(',')).trigger("chosen:updated");
				$('#sabores_select').val(clu_tienda.table_products.data()[i].flavors.split(',')).trigger("chosen:updated");
				$('#materiales_select').val(clu_tienda.table_products.data()[i].materials.split(',')).trigger("chosen:updated");
				break;
			};
		}	    
	});
};

clu_tienda.prototype.format= function(d,r) {    			
    var html = ''+
    '<div class="panel panel-default">'+
    	'<div class="panel-heading">'+
    		'<a href="#" style="text-decoration: none; color: #777">'+
				'<div class="btn_editar_producto_'+d.id+' editproduct" id="btn_editar_producto" >'+
					'<span class="glyphicon glyphicon-cog" aria-hidden="true" style=""></span>'+
					'<span> Editar este Producto</span>'+
				'</div>'+
			'</a>'+
    	'</div>'+			        
    	'<div class="panel-body">'+
			'<div class="row">'+
				'<div class="col-md-12">'+
					'<div class="col-md-8 product_more">'+
						'<div>'+					
							'<label for="description" class="col-md-12 control-label">Descripciòn</label>'+
							''+d.description+''+
						'</div>';
						
						if(d.colors != ''){
							html = html + '<div>';
							html = html +'<label for="colores" class="col-md-12 control-label">Colores Disponibles</label>'+
							''+d.colors+'</div>';						
						}

						if(d.sizes != ''){
							html = html +'<div><label for="tallas" class="col-md-12 control-label">Tallas o Tamaños Disponibles</label>'+
							''+d.sizes+'</div>';						
						}	

						if(d.flavors != ''){
							html = html +'<div><label for="sabores" class="col-md-12 control-label">Sabores Disponibles</label>'+
							''+d.flavors+'</div>';				
						}

						if(d.materials != ''){
							html = html +'<div><label for="materiales" class="col-md-12 control-label">Materiales Disponibles</label>'+
							''+d.materials+'</div>';					
						}

						if(d.models != ''){
							html = html +'<div><label for="modelos" class="col-md-12 control-label">Modelos Disponibles</label>'+
							''+d.models+'</div>';					
						}

						html = html +'<div><label for="prioridad" class="col-md-12 control-label">Nivel de Prioridad</label>';
						html = html + 'Orden: '+d.order+'</div>';

						if(d.active){
							html = html +'<div><label for="active" class="col-md-12 control-label">Estado de Producto</label>';
							html = html + 'Activo </div>';
						}else{
							html = html +'<div><label for="active" class="col-md-12 control-label">Estado de Producto</label>';
							html = html + 'Desactivo </div>';
						}					
		html = html +'</div>'+
					'<div class="col-md-4" style="text-align: center;">'+
						'<label for="img_product_descrip" class="col-md-12 control-label">Imagen de Producto</label>'+
						'<img src="'+r.url+'/users/'+r.usuario+'/products/'+d.image1+'" id="img_product_descrip" style="width: 90%; border:2px solid #ddd;border-radius: 0%;" alt="Imagen no disponible">'+
						'<a href="#" class="btn btn-default col-md-12" style="text-decoration: none; color: #777; margin-top: 10px; " >'+
							'<div class="btn_editar_producto_'+d.id+' editproduct" id="btn_editar_producto" >'+
								'<span class="glyphicon glyphicon-cog" aria-hidden="true" style=""></span>'+
								'<span> Editar este Producto</span>'+
							'</div>'+
						'</a>'+
					'</div>'+
				'</div>'+
			'</div>'+
    	'</div>'+    
    '</div>';
    return html;
};

clu_tienda.prototype.consultaRespuestaOrders = function(result) {
	$('#odenes_modal').modal();
};

clu_tienda.prototype.consultaRespuestaOrder = function(result) {
	clu_tienda.row.child( clu_tienda.formatorder(clu_tienda.row.data(),result.request ,result.data)).show();
	//OPCIONES DE PEDIDO DE ORDEN
	$('.stage_cambio').on('click', function (e) {
		var datos = new Array();
		datos['stage'] = this.id.split('_')[1];
		datos['id_order'] = this.id.split('_')[2];
		datos['id_store'] = this.id.split('_')[3];
		seg_ajaxobject.peticionajax($('#form_stage_order').attr('action'),datos,"clu_tienda.cambiarRespuestaOrden");

		//loading
		var opts = {
			  lines: 13 // The number of lines to draw
			, length: 41 // The length of each line
			, width: 10 // The line thickness
			, radius: 56 // The radius of the inner circle
			, scale: 1 // Scales overall size of the spinner
			, corners: 1 // Corner roundness (0..1)
			, color: '#000' // #rgb or #rrggbb or array of colors
			, opacity: 0.25 // Opacity of the lines
			, rotate: 0 // The rotation offset
			, direction: 1 // 1: clockwise, -1: counterclockwise
			, speed: 1 // Rounds per second
			, trail: 60 // Afterglow percentage
			, fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
			, zIndex: 2e9 // The z-index (defaults to 2000000000)
			, className: 'spinner' // The CSS class to assign to the spinner
			, top: '50%' // Top position relative to parent
			, left: '50%' // Left position relative to parent
			, shadow: false // Whether to render a shadow
			, hwaccel: false // Whether to use hardware acceleration
			, position: 'absolute' // Element positioning
		}

		target = document.getElementById('odenes_modal');
		clu_tienda.spinner = new Spinner(opts).spin(target);


	});	
};

clu_tienda.prototype.formatorder= function(d,r,data) {
	 var html = ''+
    	'<div class="panel panel-default">'+
    		'<div class="panel-heading">'+
	    		'<a href="#" style="text-decoration: none; color: #777">'+
					'<div class=" btn_editar_orden_'+d.id+'" id="btn_editar_orden" >'+
						'<span class="glyphicon glyphicon-th-list" aria-hidden="true" style=""></span>'+
						'<span> Detalles de Orden</span>'+
					'</div>'+
				'</a>'+
	    	'</div>'+
	    	'<div class="panel-body">'+
				'<div class="row" style="text-align: center;"> '+
					'<div class="col-md-12">'+
						'<div class="col-md-12">'+
							'<div class="col-md-3">'+							
								'<label for="producto" class="col-md-12 control-label">PRODUCTO</label>'+	
							'</div>'+
							'<div class="col-md-3">'+							
								'<label for="description" class="col-md-12 control-label">DESCRPCIÒN</label>'+	
							'</div>'+
							'<div class="col-md-2">'+							
								'<label for="precio" class="col-md-12 control-label">PRECIO</label>'+	
							'</div>'+
							'<div class="col-md-2">'+							
								'<label for="cantidad" class="col-md-12 control-label">CANTIDAD</label>'+	
							'</div>'+						
							'<div class="col-md-2">'+							
								'<label for="total" class="col-md-12 control-label">TOTAL</label>'+	
							'</div>'+
						'</div>';

						fondo_bandera = -1;
						background = 'none';
						cantidad_total = 0;
        				precio_total = 0;

						for(var i=0; i < data.length ; i++){
							
							if(fondo_bandera==1){
				                background = "#e7e7e7";
				            }

			html = html +'<div class="col-md-12" style="background:'+background+';">'+
							'<div class="col-md-3">'+							
								''+data[i]['product']+
							'</div>'+
							'<div class="col-md-3">'+							
								''+data[i]['description']+
							'</div>'+
							'<div class="col-md-2">'+							
								''+data[i]['price']+
							'</div>'+							
							'<div class="col-md-2">'+							
								''+data[i]['volume']+
							'</div>'+							
							'<div class="col-md-2">'+							
								'$'+(parseInt(data[i]['volume'])*parseInt(data[i]['price']))+
							'</div>'+
						'</div>';

						cantidad_total = parseInt(cantidad_total) + parseInt(data[i]['volume']);
						precio_total = parseInt(precio_total) + (parseInt(data[i]['volume'])*parseInt(data[i]['price']));

						 fondo_bandera = fondo_bandera*-1;
						 background = 'none';

						}			

     html = html +'</div>'+			
				'</div>'+
	    	'</div>'+
			'<div class="panel-footer" >'+
				'<span> Cantidad de productos a llevar: <label>'+cantidad_total+'</label>. Total a Pagar: $'+precio_total+'.</span>';
	 
	 //los botones dependen del estado de la orden
	 if(d.stage_id == 1){
	 	//estado PENDIENTE
	 	html = html +'<div style="float: right;">'+				
					'<a href="#"  id = "stage_rechazado_'+d.id+'_'+r.id_tienda+'" class="btn btn-warning stage_cambio" style="text-decoration: none; margin-right: 10px;" >'+						
						'<span class="glyphicon glyphicon-remove" aria-hidden="true" style=""></span>'+
						'<span> Rechazar Pedido</span>'+						
					'</a>'+
					'<a href="#" id = "stage_aceptado_'+d.id+'_'+r.id_tienda+'" class="btn btn-success stage_cambio" style="text-decoration: none; margin-right: 10px;">'+						
						'<span class="glyphicon glyphicon-ok" aria-hidden="true" style=""></span>'+
						'<span> Aceptar Pedido</span>'+						
					'</a>'+
				'</div>';
	 }else{
	 	if(d.stage_id == 2){
	 		//ACEPTADO
	 		html = html +'<div style="float: right;">'+									
					'<a href="#" id = "stage_finalizado_'+d.id+'_'+r.id_tienda+'" class="btn btn-success stage_cambio" style="text-decoration: none; margin-right: 10px;">'+						
						'<span class="glyphicon glyphicon-ok" aria-hidden="true" style=""></span>'+
						'<span> Pedido Entregado</span>'+						
					'</a>'+
				'</div>';
	 	}else{
	 		//
	 		//RECHAZADO
	 	}
	 }	  


	html = html +'</div>'+		
   		'</div>';

   	 var html =  html +
    	'<div class="panel panel-default">'+
    		'<div class="panel-heading">'+
	    		'<a href="#" style="text-decoration: none; color: #777">'+
					'<div class="" id="" >'+
						'<span class="glyphicon glyphicon-list-alt" aria-hidden="true" style=""></span>'+
						'<span> Indicaciones y Sugerencias de Pedido</span>'+
					'</div>'+
				'</a>'+
	    	'</div>'+


    	'</div>';

	 return html;
};

clu_tienda.prototype.cambiarRespuestaOrden = function(result) {	
	clu_tienda.table_orders.ajax.reload();
	clu_tienda.spinner.el.remove();
	$('#odenes_modal .alerts-module').html('<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>!La Orden ha sido actualizada correctamente!</strong></br> Orden actualizada: '+result.id_order+'</br> Ahora la Orden esta en el estado: '+result.stage+'</div>');
};

var clu_tienda = new clu_tienda();





