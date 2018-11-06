<style type="text/css">
	.slick-prev {
	    left: 0px !important;
	}
	.slick-next {
	    right: 0px !important;
	}
</style>
<section>
	<div class="filters-holder">
		<div class="filters">
			<?php 
				if(strpos($_SERVER['REQUEST_URI'], 'promociones/') !== FALSE){
					$url = '';
				}elseif(strpos($_SERVER['REQUEST_URI'], 'promociones') !== FALSE){
					$url = 'promociones/';
				}else{
					$url = '';
				}
			?>
			@if(isset($tags))
				<?php $tipos = ""; $modos = ""; ?>
				@foreach($tags as $k => $val)
					@if($val->tag_type_id == 1)
						<?php 
						$tipos .= '<div class="like-a-button ico_size"><article>
						<a onclick="explorar(\''.$val->tag_name. '\')" href="'.$url.$val->tag_name.'" style="text-decoration: none; margin:0px auto;">
							<img src="'. URL::to('http://172.16.20.254/imagespf/app-icons/web/'.$val->image).'" style="margin:0px auto; width: 32px;"/>
							'.$val->tag_name.'
						</a>
						</article></div>'; ?>
					@elseif($val->tag_type_id == 2)
						<?php 
						$modos .= '<div class="like-a-button"><article>
						<a onclick="explorar(\''.$val->tag_name. '\')" href="'.$url.$val->tag_name.'" style="text-decoration: none; margin:0px auto;">
							<img src="'. URL::to('http://172.16.20.254/imagespf/app-icons/web/'.$val->image).'" style="margin:0px auto; width: 32px;"/>
							'.$val->tag_name.'
						</a>
						</article></div>'; ?>
					@endif
				@endforeach
			@endif

			<div class="row">
				@if(!empty($tipos))
				<div class="col-sm-12">
					<h1>Tipos de comida</h1>
					<div class="prueba">
						{{ $tipos }}
					</div>
				</div>
				@endif
				<!--@if(!empty($modos))
				<div class="col-sm-12">
					<h4>Tus ocasiones</h4>
					<div class="prueba">
						{{ $modos }}
					</div>
				</div>
				@endif-->
			</div>
		</div>
	</div>
</section>