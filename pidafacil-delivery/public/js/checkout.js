/*jQuery(document).ready(function(){
	$('#delievery').on('click',
		function(event){
			event.preventDefault();

			$(this).addClass("active");
			$('#address').toggle('show');
			$('#pickup').removeClass('active');
			$("#restaurant").css("display","none");
		});

	$('#pickup').on('click',
		function(event){
			event.preventDefault();

			$(this).addClass("active");
			$('#restaurant').toggle('show');
			$('#delievery').removeClass('active');
			$("#address").css("display","none");
		});

	$('#cash').on('click',
		function(event){
			event.preventDefault();

			$(this).addClass("active");
			$('#money').toggle('show');
			$('#credit').removeClass('active');
			$("#creditcard").css("display","none");
			

		});

	$('#credit').on('click',
		function(event){
			event.preventDefault();

			$(this).addClass("active");
			$('#creditcard').toggle('show');
			$('#cash').removeClass('active');
			$("#money").css("display","none");

		});

	$('input:radio').change(function(){
		var $radio	=	$(this);

		$radio.closest('.box').find('div.on').removeClass('on');
		$radio.closest('.off').addClass('on');
	});
});

jQuery(document).ready(function(){
	$('.tab-pane').hide();
	$('ul.nav-tabs li:first').addClass('active').show().find('input:radio').attr('checked','');
	$('.tab-pane:first').show();

	$('ul.nav-tabs li').click(function(){
		$('ul.nav-tabs li').removeClass('active');
		$('ul.nav-tabs li').find('input:radio').attr('checked','checked');
		$('.tab-pane').hide();

		var activeTab	=	$(this).find('input:radio').val();
		$('#' + activeTab).fadeIn();
		return false;
	});
}); 
*/
jQuery(document).ready(function(){
	$('.tabstoe').hide();
	$('ul.tabie li:first').addClass('active').show().find('input:radio').attr('checked','');
	$('.tabstoe:first').show();

	$('a.tab-toggle').click(function(){
		$('ul.tabie li').removeClass('active').addClass('none');
		$('ul.tabie li').find('input:radio').attr('checked','');
		$(this).addClass('active').find('input:radio').attr('checked', 'checked');

		var activeTab	=	$(this).find('input:radio').val();
		$('#' + activeTab).fadeIn();
	});

	//Inicia primera//
	$('.tabsi').hide();
	$('ul.tabsie li:first').addClass('active').show().find('input:radio').attr('checked','');
	$('.tabsi:first').show();

	$('a.tab-toggler').click(function(){
		$('ul.tabsie li').removeClass('active');
		$('ul.tabsie li').find('input:radio').attr('checked','');
		$(this).addClass('active').find('input:radio').attr('checked', 'checked');

		var activeTab	=	$(this).find('input:radio').val();
		$('#' + activeTab).fadeIn();
	});
});
