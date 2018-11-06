<?php

class ProductController extends \BaseController {

    /**
     * Display a listing of the resource.
     * GET /{restslug}
     *
     * @return Response
     */
    public function index($restslug) {

        $date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

        $promociones = Product::where('activate',1)
                        ->where('promotion',1)
                        ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
                        ->count();

        //Ya se maneja en restaurante
        $data['restaurant'] = ParentRestaurant::getParent($restslug);
        $data['section'] = $data['restaurant']->sections()->firstOrFail();
        $data['products'] = $data['restaurant']->products()->get();

        return View::make('admin.product_list')->with('promociones', $promociones)->with($data);
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

        $date = new DateTime();
        $fecha = $date->format('Y-m-d H:i:s');

        $promociones = Product::where('activate',1)
                        ->where('promotion',1)
                        ->where('res_products.start_date', '<=', $fecha)->where('res_products.end_date', '>=', $fecha)
                        ->count();

        $response['restaurant'] = ParentRestaurant::getParent($restslug);
        $response['landingpage'] = $response['restaurant']->landingPage;
        $response['sections'] = $response['restaurant']->sections()->where('activate', true)->orderBy('section_order_id', 'asc')->get();
        $response['product'] = $response['restaurant']->products()->where('slug', $prodslug)->firstOrFail();
        $response['condition'] = $response['product']->conditions()
                ->with(['opciones'=>function($query){
                    $query->where('active', 1);
                }])->orderBy('condition_order', 'ASC')->get();
                $response['ingredient'] = $response['product']->ingredients()
                                                      ->where('active', 1)
                                                      ->orderBy('position', 'asc')
                                                      ->get();

        return View::make('web.products', $response)->with('promociones', $promociones);
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
