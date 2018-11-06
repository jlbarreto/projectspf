@extends('general.admin_layout')
@section('content')

<div class="container-fluid center_content white_content">
@if(isset($restaurants))

	<div class="container">
		<div class="row space_15" style="padding-bottom: 1em;">
			<?php $i = 0; ?>
			@foreach($restaurants as $key => $value)
				<?php $i += 1; ?>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 promo_rest space_15">
                                    <a href="{{ URL::to('admin/restaurant/branchs/'.$value->slug) }}" title="Ver sucursales de {{ $value->name }}" >
						<section>
                            <div class="logo" >
								{{ HTML::image('http://images.pf.techmov.co/'.$value->landing_page['logo']) }}
                            </div>
                            <div class="nombre">
                                {{ $value->name }}
                            </div>
						</section>
					</a>
				</div>
			@endforeach
			@if($i == 0)
				<p>No hay restaurantes aún</p>
			@endif
		</div>
	</div>
@endif
</div>

@stop