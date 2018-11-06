@extends('general.admin_layout')
@section('content')

<div class="container-fluid center_content white_content">
@if(isset($restaurants))

	<div class="container">
            <h1>Sucursales de {{ $parent->name }}</h1>
            <a href ="{{ URL::to('admin/restaurant/list') }}" class="btn btn-success">Ir a restaurantes principales</a>
            <br/>
            <br/>
            
		<div class="row space_15" style="padding-bottom: 1em;">
			<?php $i = 0; ?>
			@forelse($restaurants as $key => $value)
				<?php $i += 1; ?>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 promo_rest space_15">
                                    <a href="{{ URL::to('admin/restaurant/edit/'.$value->slug) }}" title='Editar el restaurante {{ $value->name }}' >
						<section>
                                                    <div class="logo" >
							{{ HTML::image(($value->landing_page['logo']==null)? 'http://images.pf.techmov.co/'.$parent->landing_page['logo']:'http://images.pf.techmov.co'.$value->landing_page['logo']) }}
                                                    </div>
                                                    <div class="nombre">
                                                        {{ $value->name }}
                                                    </div>
						</section>
					</a>
				</div>
			@empty
				<p>No hay restaurantes aún</p>
			@endforelse
		</div>
	</div>
@endif
</div>

@stop