<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	public static function bread($separator = '&lt;', $home = 'Inicio'){
		
		// Camino
		$path = Request::path();

		// Raiz
		$raiz = Request::root();

		// Parse
		$segmentos = explode('\n', $path);
		foreach ($segmentos as $segmento) {
			$segmento_partes = explode('/', $segmento);
		}

		$breadcrumb = '';
		$breadcrumb .= '<ol class="breadcrumb">';
		// Loop del constructor
		for ($i=0; $i < sizeof($segmento_partes); $i++) { 
			$constructorUrl[] = $raiz.'/'.$segmento_partes[$i];
			if($i == 0){
				$breadcrumb .= '<li><a href="'.$raiz.'">'.$home.'</a></li>';
			}
			$breadcrumb .= '<li><a href="'.$constructorUrl[$i].'">'.ucfirst($segmento_partes[$i]).'</a></li>';
		}
		$breadcrumb .= '</ol>';

		return $breadcrumb;
	}

}
