@if($errors->any())
    <div class="alert alert-danger">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif