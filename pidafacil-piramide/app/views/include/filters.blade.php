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
						<a onclick="explorar(\''.$val->tag_name. '\')" href="'.$url.$val->tag_name.'" style="text-decoration: none;">
							<img src="'. URL::to('images/tags/'.$val->tag_id.'.png').'" /><br/>
							'.$val->tag_name.'
						</a>
						</article></div>'; ?>
					@elseif($val->tag_type_id == 2)
						<?php 
						$modos .= '<div class="like-a-button"><article>
						<a onclick="explorar(\''.$val->tag_name. '\')" href="'.$url.$val->tag_name.'" style="text-decoration: none;">
							<img src="'. URL::to('images/tags/'.$val->tag_id.'.png').'" /><br/>
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
					<div class="ifilter-bar">
						{{ $tipos }}
					</div>
				</div>
				@endif
				@if(!empty($modos))
				<div class="col-sm-12">
					<h4>Tus ocasiones</h4>
					<div class=" ifilter-bar">
						{{ $modos }}
					</div>
				</div>
				@endif
			</div>
		
		</div>
	</div>
</section>