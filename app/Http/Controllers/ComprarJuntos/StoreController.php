<?php namespace App\Http\Controllers\ComprarJuntos;

use App\Core\ComprarJuntos\Tienda;
use App\Core\ComprarJuntos\Producto;
use App\Core\ComprarJuntos\Categoria;
use App\Core\ComprarJuntos\Orden;
use App\Core\ComprarJuntos\Anotacion;
use App\Core\ComprarJuntos\Mensaje;
use Validator;
use Mail;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class StoreController extends Controller {
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	protected $auth;
	
	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
		$this->middleware('guest');
	}

	public function getIndex(){	
		//no funcionara debido a la ruta de busqueda por url
	}
	
	public function getListar(){
		
		$moduledata['detalles']=\DB::table('clu_order_detail')
		->select('clu_order_detail.*')
		->leftjoin('clu_order', 'clu_order_detail.order_id', '=', 'clu_order.id')
		->where('clu_order.id',1)		
		->orderBy('id', 'asc')
		->get();

		//dd($moduledata['detalles']);

		$message =array();
		//Control de perfil de usuario.
		if(empty(Session::get('comjunplus.usuario.name')) || empty(Session::get('comjunplus.usuario.adress')) || empty(Session::get('comjunplus.usuario.state')) || empty(Session::get('comjunplus.usuario.city')) || empty(Session::get('comjunplus.usuario.birthdate')) || empty(Session::get('comjunplus.usuario.email'))){		
			$message[] = 'Perfil3';
			return Redirect::to('/')->with('message', $message);
		}

		//calculo de datos para desplegar las tiendas del usuario
		$moduledata = array();	
		//Controlador para opciones, para el nuevo modelo para que sepa que pintar
		Session::flash('controlador', '/mistiendas/');

		//consultamos los Departamentos los selects
		$departments = \DB::table('seg_department')->orderBy('department','asc')->get();		
		foreach ($departments as $department){
			$departamentos[$department->department] = $department->department;
		}
		$moduledata['departamentos']=$departamentos;

		//consultamos las ciudades del departamento		
		$moduledata['ciudades']= array();
		if(!empty(Session::get('comjunplus.usuario.state'))){
			//hay un departamento asignado
			$department_id = \DB::table('seg_department')
				->where('department',Session::get('comjunplus.usuario.state'))
				->get()[0]->id;

			$citys = \DB::table('seg_city')->orderBy('city','asc')
				->where('department_id',$department_id)
				->get();

			foreach ($citys as $city){
				$ciudades[$city->city] = $city->city;
			}
			$moduledata['ciudades']=$ciudades;
		}

		//Tiendas
		try {$moduledata['tiendas']=\DB::table('clu_store')
		->where('clu_store.user_id',Session::get('comjunplus.usuario.id'))
		->orderBy('order', 'asc')
		->get();
		}catch (ModelNotFoundException $e) {
			$message = ['Problemas al hallar datos de las tiendas'];
			return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata)->with('error', $message);
		}
		//si no tiene tiendas, verificador.
		if(!count($moduledata['tiendas']))$message = ['Tiendas0'];

		//categorias
		$categories = Categoria::select('id','name')->where('category_id',0)->get()->toArray();
		foreach ($categories as $categoria){
			$categorias[$categoria['id']] = $categoria['name'];
		}
		$moduledata['categorias']=$categorias;

		if(Session::has('orden_id')){						
			//consultamos la tienda de la orden
			try {
				$moduledata['tienda_orden']=\DB::table('clu_store')
				->select('clu_store.*')
				->leftjoin('clu_order', 'clu_store.id', '=', 'clu_order.store_id')
				->where('clu_order.id',Session::get('orden_id'))						
				->get();
			}catch (ModelNotFoundException $e) {
				$message = ['Problemas al hallar datos de la tienda'];
				return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata)->with('error', $message);
			}
			Session::flash('orden_id', Session::get('orden_id'));			 				
		}

		//VERIFICACIÒN DE MENSAJES		

		//verificacion de mensajes de error
		if(Session::get('error')){			
			return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata)->with('error', Session::get('error'));
		}

		//verificacion de mensajes tipo message, pueden venir de los pop ups
		if(Session::get('message')){			
			$message = array_merge ($message,Session::get('message'));
		}		

		if(!empty($message)){
			return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata)->with('message', $message);
		}else{
			return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata);
		}
		
		//return view('comprarjuntos/tienda')->with($moduledata);
	}

	public function getInicio(){
		return view('comprarjuntos/tienda');
	}

	public function postNuevatienda(Request $request){

		//verificamos si el tendero puede tener una tienda màs
		if(!$request->input('edit')){			
			$tiendas=\DB::table('clu_store')
			->select(\DB::raw('count(*) as total'))
			->where('clu_store.user_id', '=', Session::get('comjunplus.usuario.id'))			
			->groupBy('user_id')
			->get();
			
			if(!empty($tiendas)){
				if($tiendas[0]->total >= (int)Session::get('comjunplus.usuario.stores')){
					$message[] = 'Problemas al crear la tienda';
					$message[] = 'No puedes crear màs de '.Session::get('comjunplus.usuario.stores'). ' tiendas. Para màs informaciòn envìa tu sugerencia al administrador en tu perfil de usuario.';
					return Redirect::to('mistiendas/listar')->with('error', $message);
				}
			}
		}
		
		//rutina para refinar los inputs			
		$array_input = array();
		$array_input['_token'] = $request->input('_token');
		$array_input['nombre'] = str_replace(' ','',mb_strtolower($request->input('nombre')));
		$array_input['categorias'] = $request->input('categorias');	
		$array_input['color_uno'] = $request->input('color_uno');
		$array_input['color_dos'] = $request->input('color_dos');		
		$array_input['descripcion'] = ucfirst(mb_strtolower($request->input('descripcion')));		
		$array_input['image_store'] = $request->input('image_store');
		$array_input['image_banner'] = $request->input('image_banner');
		$array_input['sitio_web'] = $request->input('sitio_web');
		$array_input['facebook_web'] = $request->input('facebook_web');
		$array_input['movil'] = $request->input('movil');
		$array_input['ubicacion'] = $request->input('ubicacion');
		$array_input['prioridad'] = 0;
		if(is_numeric($request->input('prioridad')))$array_input['prioridad'] = $request->input('prioridad');

		foreach($request->input() as $key=>$value){
			if($key != "_token" && 
				$key != "nombre" && 
				$key != "categorias" && 
				$key != "color_uno" &&
				$key != "color_dos" &&	
				$key != "descripcion" &&				
				$key != "image_store" &&
				$key != "image_banner" &&
				$key != "sitio_web" &&
				$key != "facebook_web" &&
				$key != "movil" &&
				$key != "ubicacion" &&
				$key != "prioridad")
			{				
				$array_input[$key] = ucwords(mb_strtolower($value));
			}
		}
		$request->replace($array_input);
		
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'numeric' => 'El :attribute  debe ser un número',			
			'date' => 'El :attribute  no es una fecha valida',
			'mimes' => 'La :attribute debe ser de tipo jpeg, png o bmp',
		];
		
		$rules = array(			
			'nombre' => 'required',
			'departamento'    => 'required',					
			'municipio' => 'required',
			'direccion' => 'required',	
			'categorias' => 'required',						

		);		
		
		$validator = Validator::make($request->input(), $rules, $messages);		
		if ($validator->fails()) {			
			return Redirect::back()->withErrors($validator)->withInput();
		}else{			
			//preparación y validacion de imagen de tienda
			if(!empty(Input::file('image_store'))){
				$file = array('image_store' => Input::file('image_store'));
				$rules = array(
					'image_store'=>'required|mimes:jpeg,bmp,png',
				);
				$validator = Validator::make($file, $rules, $messages);
				if ($validator->fails()) {			
					return Redirect::back()->withErrors($validator)->withInput();;
				}else{
					if(Input::file('image_store')->isValid()){						
						$destinationPath = 'users/'.Session::get('comjunplus.usuario.name').'/stores';
						$extension = Input::file('image_store')->getClientOriginalExtension(); // getting image extension
						$fileName_image = rand(1,9999999).'.'.$extension; // renameing image
						Input::file('image_store')->move($destinationPath, $fileName_image); 						
					}

				}	
			}

			//preparación y validacion de imagen banner
			if(!empty(Input::file('image_banner'))){
				$file = array('image_banner' => Input::file('image_banner'));
				$rules = array(
					'image_banner'=>'required|mimes:jpeg,bmp,png',
				);
				$validator = Validator::make($file, $rules, $messages);
				if ($validator->fails()) {			
					return Redirect::back()->withErrors($validator)->withInput();;
				}else{					
					if(Input::file('image_banner')->isValid()){						
						$destinationPath = 'users/'.Session::get('comjunplus.usuario.name').'/banners';
						$extension = Input::file('image_banner')->getClientOriginalExtension(); // getting image extension
						$fileName_banner = rand(1,9999999).'.'.$extension; // renameing image
						Input::file('image_banner')->move($destinationPath, $fileName_banner); 						
					}

				}	
			}

			$store = new Tienda();
			if($request->input('edit')){					
				//se actualizan los datos de la tienda
				$store = Tienda::find($request->input('store_id'));
				$store->status =  $request->input('estado');
			}else{
				//nueva tienda
				$store->status =  'Activa';
			}

			$store->name =  $request->input()['nombre'];
			$store ->nit =  '';
			$store->department =  $request->input()['departamento'];
			$store->city =  $request->input()['municipio'];
			$store->adress =  $request->input()['direccion'];			
			$store->description =  $request->input()['descripcion'];
			//reemplazando dimensiones de iframe
			$ubication = str_replace('width="400"','width="100%"',$request->input()['ubicacion']);
			$ubication = str_replace('width="600"','width="100%"',$ubication);
			$ubication = str_replace('width="800"','width="100%"',$ubication);
			$ubication = str_replace('height="300"','height="450"',$ubication);
			$ubication = str_replace('height="600"','height="450"',$ubication);
			$store->ubication =  $ubication;
			if(empty($store->image))$store->image =  'default.png';
			if(!empty($fileName_image))$store->image =  $fileName_image;
			if(empty($store->banner))$store->banner =  'default.png';
			if(!empty($fileName_banner))$store->banner =  $fileName_banner;
			$store->color_one =  $request->input()['color_uno'];
			$store->color_two =  $request->input()['color_dos'];			
			$store->order = 1;
			if(!empty($request->input()['prioridad']))$store->order =  $request->input()['prioridad'];			
			$store->metadata =  $request->input()['categorias'];			
			$store->web =  $request->input()['sitio_web'];
			$store->fanpage =  $request->input()['facebook_web'];
			$store->movil =  $request->input()['movil'];			
			$store->user_id = Session::get('comjunplus.usuario.id');			

			try {			
				$store->save();
				if($request->input('edit')){
					return Redirect::to('mistiendas/listar')->withInput()->with('message', ['Tienda '.$store->name.' editada Exitosamnte']);
				}
				return Redirect::to('mistiendas/listar')->withInput()->with('message', ['Tienda creada Exitosamnte']);
			}catch (\Illuminate\Database\QueryException $e) {
				$message[] = 'Problemas al crear la tienda';
				$message[] = $e->getMessage();
				return Redirect::to('mistiendas/listar')->with('error', $message);
			}	

		}

	}

	public function getActualizar($id_store=null,$id_user){		
		
		if(empty($id_store) || empty($id_user) || empty(Session::get('comjunplus.usuario.id'))){
			return Redirect::to('/')->withInput()->with('alerta', ['No se procesaròn los datos suministrados']);
		}
		//verificamos tienda y su tendero
		if($id_user != Session::get('comjunplus.usuario.id')){
			return Redirect::to('/')->withInput()->with('alerta', ['No se procesaròn los datos suministrados']);
		}

		$message = array();
		//consultamos los datos de la tienda
		try {$tienda=\DB::table('clu_store')
		->where('clu_store.id',$id_store)
		->where('clu_store.user_id',Session::get('comjunplus.usuario.id'))
		->orderBy('order', 'asc')
		->get();
		}catch (ModelNotFoundException $e) {
			$message = ['Problemas al hallar datos de la tienda'];
			return Redirect::to('mistiendas/listar')->with('error', $message);
		}
		//preparacion de datos
		Session::flash('controlador', '/mistiendas/');

		//consultamos los Departamentos los selects
		$departments = \DB::table('seg_department')->orderBy('department','asc')->get();		
		foreach ($departments as $department){
			$departamentos[$department->department] = $department->department;
		}
		$moduledata['departamentos']=$departamentos;

		//consultamos las ciudades del departamento		
		$moduledata['ciudades']= array();
		if(!empty(Session::get('comjunplus.usuario.state'))){
			//hay un departamento asignado
			$department_id = \DB::table('seg_department')
				->where('department',Session::get('comjunplus.usuario.state'))
				->get()[0]->id;

			$citys = \DB::table('seg_city')->orderBy('city','asc')
				->where('department_id',$department_id)
				->get();

			foreach ($citys as $city){
				$ciudades[$city->city] = $city->city;
			}
			$moduledata['ciudades']=$ciudades;
		}
		//Tiendas
		try {$moduledata['tiendas']=\DB::table('clu_store')
		->where('clu_store.user_id',Session::get('comjunplus.usuario.id'))
		->orderBy('order', 'asc')
		->get();
		}catch (ModelNotFoundException $e) {
			$message = ['Problemas al hallar datos de las tiendas'];
			return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata)->with('error', $message);
		}

		//categorias
		$categories = Categoria::select('id','name')->where('category_id',0)->get()->toArray();
		foreach ($categories as $categoria){
			$categorias[$categoria['id']] = $categoria['name'];
		}
		$moduledata['categorias']=$categorias;

		Session::flash('_old_input.store_id', $tienda[0]->id);
		Session::flash('_old_input.nombre', $tienda[0]->name);
		Session::flash('_old_input.departamento', $tienda[0]->department);
		Session::flash('_old_input.municipio', $tienda[0]->city);
		Session::flash('_old_input.direccion', $tienda[0]->adress);
		Session::flash('_old_input.categorias', $tienda[0]->metadata);
		Session::flash('_old_input.categorias_select', explode(",", $tienda[0]->metadata));
		Session::flash('_old_input.color_uno', $tienda[0]->color_one);
		Session::flash('_old_input.color_dos', $tienda[0]->color_two);
		Session::flash('_old_input.descripcion', $tienda[0]->description);
		Session::flash('_old_input.img_store', $tienda[0]->image);
		Session::flash('_old_input.sitio_web', $tienda[0]->web);
		Session::flash('_old_input.facebook_web', $tienda[0]->fanpage);
		Session::flash('_old_input.movil', $tienda[0]->movil);
		Session::flash('_old_input.ubicacion', $tienda[0]->ubication);
		Session::flash('_old_input.prioridad', $tienda[0]->order);
		Session::flash('_old_input.img_banner', $tienda[0]->banner);
		Session::flash('_old_input.status', $tienda[0]->status);
		Session::flash('_old_input.store_id', $id_store);
		Session::flash('_old_input.edit', true);
				
		if(!empty($message)){
			return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata)->with('message', $message);
		}else{
			return Redirect::to('mistiendas/inicio')->with('modulo',$moduledata);
		}
	}

	//FUNCION PARA CAMBIAR EL ID DE LA TIENDA, ANTE UN CLIC EN LA OPCION PROCUDTOS
	public function postConsultarproducts(Request $request){
		//total de productos
		$productos=Producto::where('store_id',$request->input('id'))->count();

		//consultamos las categorias de la tienda seleccionada.
		$categorias=array();
		try {$categorias=\DB::table('clu_store')
		->select('metadata')
		->where('clu_store.id',$request->input('id'))		
		->get()[0]->metadata;
		}catch (ModelNotFoundException $e) {
			$message = ['Problemas al hallar categorias de la Tienda'];			
		}

		$subcategorias = array();
		if(!empty($categorias)){
			//si hay categorias
			$categorias_id = explode(",", $categorias);
			$categorias_db=\DB::table('clu_category')
			->select('id','name')
			->where(function($q) use ($categorias_id){
				foreach($categorias_id as $key => $value){
					$q->orwhere('category_id', '=', $value);
				}
			})->get();
			foreach($categorias_db as $categoria){
				$subcategorias[$categoria->id] = $categoria->name;
			}
		}
		
		//antes de enviar, asignamos el id de tienda par el listarajax
		Session::put('store.id', $request->input('id'));
		Session::put('store.name', $request->input('name'));
		
		if(count($subcategorias)) return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>$subcategorias,'productos'=>$productos]);
		return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>null]);
	}

	public function postConsultarproduct(Request $request){
		//consultamos, el nùmero de veces vendido, 
		return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>null]);	
	}

	//LISTAR LOS PRODUCTOS DE UNA TIENDA
	public function getListarajax(Request $request){

		//Tienda id
		if(empty(Session::get('store.id'))){
			//algo anda muy mal, no se udo asignar el id de tienda en la funcion Consultarproductos
			return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[]]);
		}

		$moduledata['total']=Producto::where('store_id',Session::get('store.id'))->count();

		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['productos']=
			Producto::
			select('clu_products.*','clu_category.name as category_name')
			->leftjoin('clu_category', 'clu_products.category', '=', 'clu_category.id')
			->where('clu_products.store_id',Session::get('store.id'))		
			->where(function ($query) {
				$query->where('clu_products.name', 'like', '%'.Session::get('search').'%')
				->orWhere('clu_products.price', 'like', '%'.Session::get('search').'%')
				->orWhere('clu_products.category', 'like', '%'.Session::get('search').'%');								
			})
			->skip($request->input('start'))->take($request->input('length'))
			->orderBy('order', 'asc')
			->get();		
			$moduledata['filtro'] = count($moduledata['productos']);
		}else{			
			$moduledata['productos']=\DB::table('clu_products')
			->select('clu_products.*','clu_category.name as category_name')
			->leftjoin('clu_category', 'clu_products.category', '=', 'clu_category.id')
			->where('clu_products.store_id',Session::get('store.id'))
			->skip($request->input('start'))->take($request->input('length'))
			->orderBy('order', 'asc')
			->get();			
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['productos']]);
	}

	public function postNuevoproducto(Request $request){		
		
		//VERIFICACIONES
		//verificamos si tienda puede tener màs productos
		if(!$request->input('edit')){			
			$productos=\DB::table('clu_products')
			->select(\DB::raw('count(*) as total'))
			->where('clu_products.store_id', '=', Session::get('store.id'))			
			->groupBy('store_id')
			->get();
			
			if(!empty($productos)){
				if($productos[0]->total >= (int)Session::get('comjunplus.usuario.products')){
					$message[] = 'Productos0';					
					return Redirect::to('mistiendas/listar')->with('error', $message);
				}
			}
		}

		//rutina para refinar los inputs			
		$array_input = array();
		$array_input['_token'] = $request->input('_token');
		$array_input['precio'] = $request->input('precio');
		$array_input['descripcion_producto'] = ucfirst(mb_strtolower($request->input('descripcion_producto')));
		$array_input['categoria_select'] = $request->input('categoria_select');
		$array_input['unidades_medida'] = $request->input('unidades_medida');
		$array_input['colores'] = $request->input('colores');				
		$array_input['tallas'] = $request->input('tallas');
		$array_input['sabores'] = $request->input('sabores');
		$array_input['materiales'] = $request->input('materiales');
		$array_input['modelos'] = $request->input('modelos');
		$array_input['prioridad_producto'] = 0;
		if(is_numeric($request->input('prioridad_producto')))$array_input['prioridad_producto'] = $request->input('prioridad_producto');

		foreach($request->input() as $key=>$value){
			if($key != "_token" && 
				$key != "precio" && 
				$key != "descripcion_producto" &&
				$key != "categoria_select" &&
				$key != "unidades_medida" &&
				$key != "colores" &&
				$key != "tallas" &&
				$key != "sabores" &&
				$key != "materiales" &&	
				$key != "modelos" &&	
				$key != "prioridad")
			{				
				$array_input[$key] = ucwords(mb_strtolower($value));
			}
		}
		$request->replace($array_input);

		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'numeric' => 'El :attribute  debe ser un número',			
			'date' => 'El :attribute  no es una fecha valida',
			'mimes' => 'La :attribute debe ser de tipo jpeg, png o bmp',
		];
		
		$rules = array(			
			'nombre_producto' => 'required',
			'precio'    => 'required', 

		);		
		
		$validator = Validator::make($request->input(), $rules, $messages);		
		if ($validator->fails()) {			
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			//preparación y validacion de imagen de producto
			if(!empty(Input::file('imge_product'))){
				$file = array('imge_product' => Input::file('imge_product'));
				$rules = array(
					'imge_product'=>'required|mimes:jpeg,bmp,png',
				);
				$validator = Validator::make($file, $rules, $messages);
				if ($validator->fails()) {			
					return Redirect::back()->withErrors($validator)->withInput();;
				}else{
					if(Input::file('imge_product')->isValid()){						
						$destinationPath = 'users/'.Session::get('comjunplus.usuario.name').'/products';
						$extension = Input::file('imge_product')->getClientOriginalExtension(); // getting image extension
						$fileName_image1 = rand(1,9999999).'.'.$extension; // renameing image
						Input::file('imge_product')->move($destinationPath, $fileName_image1); 						
					}
				}	
			}

			$product = new Producto();
			if($request->input('edit_product')){					
				//se actualizan los datos del producto
				$product = Producto::find($request->input('product_id'));
				$product->active =  $request->input('estado_producto');
				//$product->active =  1;
			}else{
				//nueva tienda
				$product->active =  1;
			}

			$product->name =  $request->input()['nombre_producto'];
			$product->price = $request->input()['precio'];
			$product->category = $request->input()['categoria_select'];
			$product->unity_measure = 'Unidad';
			if(!empty($request->input()['unidades_medida']))$product->unity_measure=  $request->input()['unidades_medida'];
			$product->colors = $request->input()['colores'];
			$product->sizes = $request->input()['tallas'];
			$product->flavors = $request->input()['sabores'];
			$product->materials = $request->input()['materiales'];
			$product->models = $request->input()['modelos'];
			$product->description = $request->input()['descripcion_producto'];
			$product->order = 1;
			if(!empty($request->input()['prioridad_producto']))$product->order =  $request->input()['prioridad_producto'];	
			if(empty($product->image1))$product->image1 =  'default.png';
			if(!empty($fileName_image1))$product->image1 =  $fileName_image1;
			$product->store_id = Session::get('store.id');
		
			try {			
				$product->save();
				if($request->input('edit_product')){
					$message[] = 'ProductosEDITOK';	
					return Redirect::to('mistiendas/listar')->withInput()->with('message',$message);
				}
				$message[] = 'ProductosOK';	
				return Redirect::to('mistiendas/listar')->withInput()->with('message', $message);
			}catch (\Illuminate\Database\QueryException $e) {
				$message[] = 'Problemas al crear la tienda';
				$message[] = $e->getMessage();
				return Redirect::to('mistiendas/listar')->with('error', $message);
			}

		}			
			
	}

	public function postConsultarorder(Request $request){
		//consultamos, los detalles y las entradas del foro
		$moduledata['detalles']=\DB::table('clu_order_detail')
		->select('clu_order_detail.*')
		->leftjoin('clu_order', 'clu_order_detail.order_id', '=', 'clu_order.id')
		->where('clu_order.id',$request->input('id_order'))		
		->orderBy('id', 'asc')
		->get();

		$moduledata['entradas']=\DB::table('clu_order_annotation')
		->select('clu_order_annotation.*')
		->leftjoin('clu_order', 'clu_order_annotation.order_id', '=', 'clu_order.id')
		->where('clu_order.id',$request->input('id_order'))		
		->orderBy('id', 'desc')
		->get();

		if(count($moduledata['detalles'])){
			return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>$moduledata['detalles'],'annotations'=>$moduledata['entradas']]);	
		}

		return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>null]);	
	}

	public function postConsultarorders(Request $request){
		//antes de enviar, asignamos el id de tienda par el listarajax
		Session::put('store.id', $request->input('id'));
		Session::put('store.name', $request->input('name'));
		//para la consulta de orden por email
		//if(Session::has('orden_id'))Session::flash('orden_id', Session::get('orden_id'));
		return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>null]);
	}

	//LISTAR LAS ORDENES DE UNA TIENDA
	public function getListarajaxorders(Request $request){
		//Tienda id
		if(empty(Session::get('store.id'))){
			//algo anda muy mal, no se udo asignar el id de tienda en la funcion Consultarproductos
			return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[]]);
		}

		$moduledata['total']=Orden::where('store_id',Session::get('store.id'))->count();

		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['ordenes']=
			Orden::
			select('clu_order.*')			
			->where('clu_order.store_id',Session::get('store.id'))		
			->where(function ($query) {
								
				if(strpos(Session::get('search'),'Orden_') !== false){
					//solo consultamos por orden, para la consulta desde correo electronico
					//$query->where('clu_order.id', 'like', Session::get('search'));
					$query->where('clu_order.id', str_replace('Orden_','', Session::get('search')));					
				}else{
					$query->where('clu_order.id', 'like', '%'.Session::get('search').'%')
					->orWhere('clu_order.name_client', 'like', '%'.Session::get('search').'%')
					->orWhere('clu_order.number_client', 'like', '%'.Session::get('search').'%');
				}
												
			})
			->skip($request->input('start'))->take($request->input('length'))
			->orderBy('id', 'desc')
			->get();		
			$moduledata['filtro'] = count($moduledata['ordenes']);			
		}else{			
			$moduledata['ordenes']=\DB::table('clu_order')
			->select('clu_order.*')
			->where('clu_order.store_id',Session::get('store.id'))					
			->skip($request->input('start'))->take($request->input('length'))
			->orderBy('id', 'desc')
			->get();			
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['ordenes']]);

	}

	//funciòn para ante el cambio de estado de la orden
	public function postCambioestadoorder(Request $request){
		//verificamos la orden y la tienda
		//Tiendas		
		try {
			$orden=\DB::table('clu_order')			
			->leftjoin('clu_store', 'clu_order.store_id', '=', 'clu_store.id')
			->where('clu_order.id',$request->input()['id_order'])		
			->where('clu_store.id',$request->input()['id_store'])			
			->get();

			$estado = ''; 
			
			if(!empty($orden)){				
				$order = Orden::find($request->input()['id_order']);

				//operaciòn
				$bandera_stage = false;
				if($request->input()['stage'] == 'aceptado'){
					$order->stage_id = 2;
					$order->save();
					$estado = 'ACEPTADA';
					$bandera_stage = true;
					
				}
				if($request->input()['stage'] == 'rechazado'){
					$order->stage_id = 3;
					$order->save();
					$estado = 'RECHAZADA';
					$bandera_stage = true;
					
				}
				if($request->input()['stage'] == 'finalizado'){
					$order->stage_id = 4;
					$order->save();
					$estado = 'ETREGADO';
					$bandera_stage = true;					
				}

				if($bandera_stage){

					//ENVIAR MENSAJE A CLIENTE
					//consultamos la tienda
					$tienda = \DB::table('clu_store')
					->select('clu_store.*','seg_user.email','seg_user.name as uname','seg_user_profile.names as nombres_tendero','seg_user_profile.surnames as apellidos_tendero','seg_user_profile.movil_number','seg_user_profile.fix_number','seg_user.id as user_id')
					->leftjoin('seg_user', 'clu_store.user_id', '=', 'seg_user.id')
					->leftjoin('seg_user_profile', 'clu_store.user_id', '=', 'seg_user_profile.user_id')								
					->where('clu_store.id',$request->input()['id_store'])
					->get();

					//consultamos la orden
					$orden = \DB::table('clu_order')
					//->leftjoin('clu_order_detail', 'clu_order.id', '=', 'clu_order_detail.order_id')
					->where('clu_order.id',$request->input()['id_order'])
					->get();

					//consultamos los detalles
					$detalles = \DB::table('clu_order')
					->select('clu_order_detail.*')
					->leftjoin('clu_order_detail', 'clu_order.id', '=', 'clu_order_detail.order_id')
					->where('clu_order.id',$request->input()['id_order'])
					->get();

					//anotaciones
					$anotaciones = \DB::table('clu_order_annotation')
					->where('clu_order_annotation.order_id',$request->input()['id_order'])
					->get();

					$data = Array();					
					$data['tienda'] = $tienda[0]->name;
					$data['orden_id'] = $request->input()['id_order'];
					$data['email'] = $tienda[0]->email;
					$data['direccion_tienda'] = $tienda[0]->city.' - '.$tienda[0]->adress;
					$data['ciudad_tienda'] = $tienda[0]->city;
					$data['telefono_tienda'] = $tienda[0]->movil_number.' - '.$tienda[0]->fix_number;
					$data['imagen'] = 'users/'.$tienda[0]->uname.'/stores/'.$tienda[0]->image;

					$data['estado'] = $estado;

					$data['nombres_tendero'] =  $tienda[0]->nombres_tendero;
					$data['apellidos_tendero'] = $tienda[0]->apellidos_tendero;					

					$data['url'] = $request->url();

					$data['detalles'] = $detalles;
					$data['mensaje_orden'] = $request->input()['menssage_order'];//puede estar vacio
					$data['anotaciones'] = $anotaciones;

					$data['id_tender'] = $tienda[0]->user_id;
					$data['id_client'] = $orden[0]->client_id;
					
					try{
						Mail::send('email.order_change',$data,function($message) use ($orden) {
							$message->from(Session::get('mail'),Session::get('app').' - '.$orden[0]->id);
							$message->to($orden[0]->email_client,$orden[0]->name_client)->subject('Orden de Pedido.');
						});
					}catch (\Exception  $e) {	
						return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>$e->getMessage()]);
					}

					//agregamosel mensaje del tendero a las notaciones de la orden
					if($data['mensaje_orden'] != ""){
						$anotacion = new Anotacion();
						$hoy = new DateTime();
						$anotacion->user_name = $data['nombres_tendero'];
						$anotacion->date = $hoy->format('Y-m-d H:i:s');
						$anotacion->description = $data['mensaje_orden'];
						$anotacion->active = true;
						$anotacion->order_id = $data['orden_id'] ;				
						try {
							//guardado de anotacion de pedido
							$anotacion->save();
						}catch (ModelNotFoundException $e) {				
							//si no se guarda la nota no hay problema, se sigue la linea y se retorna
							Session::flash('orden_id', $request->input()['id_order']);
							return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>true]);
						}

						//envio de mensaje al mailbox del ciente, copia para tendero en caso de existir
						$mensaje = new Mensaje();
						$mensaje->subject = 'Cambio de estado en Orden de Pedido';
						$mensaje->date = $hoy->format('Y-m-d H:i:s');
						$mensaje->object = 'clu_order';
						$mensaje->object_id = $data['orden_id'];
						$mensaje->user_sender_id = $tienda[0]->user_id;//tendero			
						$mensaje->user_receiver_id = 0;//enviada al cliente
						$mensaje->message = 'Nuevo cambio de estado en Orden de pedido, codigo: '.$data['orden_id'].' '.$data['mensaje_orden'].', Estado actual: '.$estado;
						//enviada al cliente
						if($orden[0]->client_id){
							$mensaje->user_receiver_id = $orden[0]->client_id;
						}else{
							$mensaje->message = 'Nuevo cambio de estado en Orden de pedido, codigo: '.$data['orden_id'].' '.$data['mensaje_orden'].', Estado actual: '.$estado.' Cliente: '.$orden[0]->name_client.' - '.$orden[0]->email_client.' - '.$orden[0]->number_client.' - '.$orden[0]->adress_client;
						}						
						$html = '<div>'.
									'Nuevo cambio de estado en Orden de pedido, codigo: '.$data['orden_id'].''.
									''.$data['mensaje_orden'].', Estado actual: '.$estado.''.
								'</div>';
						$mensaje->body = $html;

						try {				
							$mensaje->save();	
						}catch (ModelNotFoundException $e) {				
							//no hacer nada
						}		
					}					
					
					Session::flash('orden_id', $request->input()['id_order']);
					return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>true]);
				}

				return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>false]);
			}


		}catch (ModelNotFoundException $e) {			
			return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>false]);
		}		
		return response()->json(['respuesta'=>true,'request'=>$request->input(),'data'=>false]);
	}

}