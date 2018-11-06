@extends('general.general_white')

@section('content')
<script>
    $(document).ready(function(){
        var url = "http://onelink.to/ff4pft";    
        $(location).attr('href',url);
    });
</script>
@stop