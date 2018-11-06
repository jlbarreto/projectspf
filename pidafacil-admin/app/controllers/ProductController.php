<?php

class ProductController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /{restslug}
     *
     * @return Response
     */
    public function index($restslug) {
        //Ya se maneja en restaurante 
        $data['restaurant'] = ParentRestaurant::getParent($restslug);
        
        $data['section'] = $data['restaurant']->sections()->firstOrFail();

        $data['products'] = $data['restaurant']->products()->get();

        return View::make('admin.product_list')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     * GET {restslug}/product/create
     *
     * @param  string  $restslug
     * @return Response
     */
    public function create($id_section) {
        $section = Section::find($id_section);
        $data = $this->getLoaders($section->restaurant()->firstOrFail());
        
        $data['section'] = $section;
        $data['ingredients_selected'] = array();
        $data['conditions_selected'] = array();
        
        return View::make('admin.product_create')->with($data);
    }
    
    private function getLoaders($restaurant){
        $data['tag_types'] = TagType::with('tags')->get();
        $data['restaurant'] = $restaurant;
        
        $data['ingredients'] = $data['restaurant']->ingredients()->get();
        $sections = $data['restaurant']->sections()->get();
        
        $data['sections'] = array();
        
        foreach ($sections as $s) {
            $data['sections'][$s->section_id] = $s->section;
        }
        
        $data['conditions'] = $data['restaurant']->getConditions();
        
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     * POST {restslug}/product/create
     *
     * @return Response
     */
    public function store() {
//		$restaurant = ParentRestaurant::getParent($restslug);
        //Creación de producto
        return $this->saveProduct();
    }

    public function saveProduct($id = NULL) {
        //--------Validación -----------------------
        $validation = Product::validacion(Input::all());
        //-------------------------------------------
        if ($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        } else {

            $section = Section::where('section_id', Input::get('section_id'))->firstOrFail();
            $restaurant = $section->restaurant()->firstOrFail();
//            $prodslug = Str::slug(Input::get('product'), '-');
//            $unicidad = $restaurant->products()->where('slug', $prodslug)->count();
//            if ($unicidad > 0)
//                return Redirect::back()->withInput()->withErrors('Existe un producto con el mismo nombre dentro de su restaurante');
//            else {
            if ($id == NULL) {
                $product = new Product();
            } else {
                $product = Product::find($id);
            }

            $product->product = Input::get('product');
            $product->description = Input::get('description');
            $product->value = Input::get('value');
            $product->section_id = Input::get('section_id');
            $product->activate = (Input::has('activate')) ? 1 : 0;
            $product->promotion = (Input::has('promotion')) ? 1 : 0;

            if (Input::has('start_date')) {
                $start = date_parse_from_format("d/m/Y H:i", Input::get('start_date'));
                $product->start_date = $start['year'] . '-' . $start['month'] . '-' . $start['day'] . ' ' . $start['hour'] . ':' . $start['minute'];
            }else{
                $product->start_date = NULL;
            }


            if (Input::has('end_date')) {
                $end = date_parse_from_format("d/m/Y H:i", Input::get('end_date'));
                $product->end_date = $end['year'] . '-' . $end['month'] . '-' . $end['day'] . ' ' . $end['hour'] . ':' . $end['minute'];
            }else{
                $product->end_date = NULL;
            }
            
            //----manejo de ingredientes-----
            $product->save();
            
            $inputIngredients = (Input::has('ingredients'))? Input::get('ingredients'):array();
            $ingredients = array();
            

            $removables = (Input::has('removables'))? Input::get('removables'):array();

            foreach ($inputIngredients as $key=>$value) {
                $ingredients[$key]=array('removable'=>  (array_key_exists($key, $removables))? 1:0 );
            }
            
            $product->ingredients()->sync($ingredients);
            
            $arConditions = array();
            
            if(Input::has('conditions')){
                foreach (Input::get('conditions') as $v) {
                    $arConditions[$v] = array('condition_order'=>Input::get('positions')[$v]);
                }
                
                $product->conditions()->sync(Input::get('conditions'));
            }
            
            $product->conditions()->sync($arConditions);
            
            

            $product->tags()->sync((Input::has('tags')) ? Input::get('tags') : array());
//                $validator = Validator::make(
//                                array('image_web' => Input::file('image_web')), array('image_web' => 'image|mimes:jpeg|max:1000')
//                );

            if (Input::hasFile('imagen')) {
                if (Image::make(Input::file('imagen'))->width() == 300 & Image::make(Input::file('imagen'))->height() == 300) {

                    $image_web = Input::file('imagen');

                    $destino = '/var/www/pidafacil/imagespf/';
                    
                    $destinationPath = 'restaurants/' . $restaurant->slug . '/' . $product->slug;

                    $uploadSuccess = $image_web->move($destino.''. $destinationPath, 'image_web.jpeg');
                    log::info($uploadSuccess);
                    #$uploadSuccess = $image_web->move($destino, 'image_web.jpeg');

                    if ($uploadSuccess) {
                        $product->image_web = '/'.$destinationPath . '/image_web.jpeg';
                        $product->image_app = '/'.$destinationPath . '/image_web.jpeg';
                        $product->save();
                        Session::flash('flash_message', 'Producto almacenado con éxito');
                    } else
                        Session::flash('flash_message', 'Se guardó el producto pero la imagen no se puedo almacenar');
                }else {
                    Session::flash('flash_message', 'Se guardó el producto pero la imagen debe ser de tamaño 300x300 pixeles');
                }
            } else {
                Session::flash('flash_message', 'Producto almacenado con éxito');
            }

            return Redirect::to('admin/product/edit/' . $product->product_id);
//            }
        }
    }

    /**
     * Display the specified resource.
     * GET /{restslug}/{prodslug}
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function show($restslug, $prodslug) {
        $response['restaurant'] = ParentRestaurant::getParent($restslug);
        $response['landingpage'] = $response['restaurant']->landingPage;
        $response['product'] = $response['restaurant']->products()->where('slug', $prodslug)->firstOrFail();
        $response['condition'] = $response['product']->conditions()->with('opciones')->orderBy('condition_order', 'ASC')->get();
        $response['ingredient'] = $response['product']->ingredients()->get();

        return View::make('web.products', $response);
    }

    public function sectionList($id_section) {
        $data['section'] = Section::find($id_section);

        $data['products'] = $data['section']->products()->get();
        $data['restaurant'] = Restaurant::find($data['section']->restaurant_id);

        return View::make('admin.product_list')->with($data);
    }

    public function changeActivate($id) {
        $product = Product::find($id);

        $product->activate = ($product->activate==1)? 0:1;
        $product->save();
        Session::flash('flash_message', 'Operación realizada con éxito');

        return Redirect::back();
    }
    /**
     * Show the form for editing the specified resource.
     * GET /{restslug}/{prodslug}/edit
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function edit($id) {
        $product = Product::find($id);
        $section = $product->section()->firstOrFail();
        
        $response = $this->getLoaders($section->restaurant()->firstOrFail());
        $response['product'] = $product;
        $response['section'] = $section;
        
        if($response['product']->start_date != NULL)
            $response['product']->start_date = date("d/m/Y H:i", strtotime($response['product']->start_date));
        
        if($response['product']->end_date!=NULL)
            $response['product']->end_date = date("d/m/Y H:i", strtotime($response['product']->end_date));
        
        
        $response['tags'] = $response['product']->tags()->get();
        $response['ingredients_selected'] = $response['product']->ingredients()->get();
        $response['conditions_selected'] = $response['product']->conditions()->get();
        
        return View::make('admin.product_edit')->with($response);
    }

    /**
     * Update the specified resource in storage.
     * POST /{restslug}/{prodslug}/update
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function update($id) {
        return $this->saveProduct($id);
    }

    /**
     * Remove the specified resource from storage.
     * POST /{restslug}/{prodslug}/delete
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function destroy($restslug, $prodslug) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        $product->delete();
        $response['restaurant'] = $restaurant;
        $response['message'] = 'éxito';

        return $response;
    }

    /**
     * Show product ingredients.
     * GET /{restslug}/{prodslug}/ingredients
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function showingredient($restslug, $prodslug) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        return $product->ingredients;
    }

    /**
     * Add ingredients to a product.
     * POST /{restslug}/{prodslug}/ingredients
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function addingredient($restslug, $prodslug) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();

        $exist = $restaurant->ingredients()->where('ingredient_id', Input::get('ingredient'))->count();
        if ($exist == 1) {  //verificando la existencia del producto
            $ingredient_id = Input::get('ingredient');
            $removable = Input::get('removable');
            $ingredients = $product->ingredients;

            if ($ingredients->contains($ingredient_id)) {// Verificando si ya existe la relación previamente
                $data['message'] = 'El ingrediente ya ha sido añadido';
                $data['error'] = true;
            } else {

                //Si no existe se crea
                $product->ingredients()->attach($ingredient_id, array('removable' => $removable));
                $data['message'] = 'Operación realizada con éxito';
                $data['error'] = false;
            }
        } else {

            $data['message'] = 'Error al procesar ingrediente';
            $data['error'] = true;
        }

        return $data;
    }

    /**
     * Add ingredients to a product.
     * POST /{restslug}/{prodslug}/ingredients/{id}/delete
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @param  integer $id
     * @return Response
     */
    public function deleteingredient($restslug, $prodslug, $ingredient_id) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        $exist = $restaurant->ingredients()->where('ingredient_id', $ingredient_id)->count();

        if ($exist == 1) { //verificando la existencia del producto
            $ingredients = $product->ingredients;
            if ($ingredients->contains($ingredient_id)) {

                $product->ingredients()->detach($ingredient_id);
                $data['message'] = 'Operación realizada con éxito';
                $data['error'] = false;
            } else {

                $data['message'] = 'Error al procesar operación';
                $data['error'] = true;
            }
        } else {

            $data['message'] = 'Error al procesar operación';
            $data['error'] = true;
        }
        return $data;
    }

    /**
     * Show product conditions.
     * GET /{restslug}/{prodslug}/conditions
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function showconditions($restslug, $prodslug) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();

        return $product->with('conditions')->get();
    }

    /**
     * addproduct conditions.
     * POST /{restslug}/{prodslug}/conditions/{id}
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @param  integer $id
     * @return Response
     */
    public function addconditions($restslug, $prodslug, $id) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        $condition = Condition::find($id);
        $product->conditions()->attach($condition);
        return $product->with('conditions')->get();
    }

    /**
     * delete product conditions.
     * POST /{restslug}/{prodslug}/conditions/{id}/delete
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function deleteconditions($restslug, $prodslug, $id) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        $condition = Condition::find($id);

        $product->conditions()->detach($condition);

        return $product->with('conditions')->get();
    }

    /**
     * Show product tags.
     * GET /{prodslug}/tags
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function showtags($restslug, $prodslug) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        return $product->with('tags')->get();
    }

    /**
     * Add product tags.
     * POST /{prodslug}/tags/{id}
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @return Response
     */
    public function addtags($restslug, $prodslug, $id) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        $condition = Tag::find($id);
        $product->tags()->attach($condition);
        return $product->with('tags')->get();
    }

    /**
     * Add product tags.
     * POST /{prodslug}/tags/{id}/delete
     *
     * @param  string  $restslug
     * @param  string  $prodslug
     * @param  integer $id
     * @return Response
     */
    public function deletetags($restslug, $prodslug, $id) {
        $restaurant = ParentRestaurant::getParent($restslug);
        $product = $restaurant->products()->where('slug', $prodslug)->firstOrFail();
        $condition = Tag::find($id);
        $product->tags()->detach($condition);
        return $product->with('tags')->get();
    }

}
