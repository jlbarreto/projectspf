<footer>    
</footer>
{{ HTML::script('js/details-shim.js') }}
{{ HTML::script('js/boobstrap/bootstrap.js') }}
{{ HTML::script('js/jquery.validate.min.js') }}
{{ HTML::script('js/ajax.js') }}
{{ HTML::script('js/moment.js') }}
{{ HTML::script('js/scripts.js') }}
<script>
    $("#CopyURL").click(function(e){
        e.preventDefault();
        prompt('Ctrl+c Para copiar URL', '{{Request::url()}}');
    });

</script>
