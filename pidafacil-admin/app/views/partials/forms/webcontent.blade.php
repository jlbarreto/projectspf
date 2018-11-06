<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Sobre el Web Content</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('webContent', 'Incluir web content:', ['class' => 'input-group-addon']) }}
                {{ Form::checkbox('webContent', 'existe', false, ['class' => 'form-control']) }}
            </div>
        </div>

        <div id="contFormWeb">
            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('header', 'Header:', ['class' => 'input-group-addon']) }}
                    {{ Form::file('header', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('logo', 'Logo:', ['class' => 'input-group-addon']) }}
                    {{ Form::file('logo', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <!--<div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('banner', 'Banner:', ['class' => 'input-group-addon']) }}
                    {{ Form::file('banner', null, ['class' => 'form-control']) }}
                </div>
            </div>-->
            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('slogan',null, ['class' => 'input-group-addon']) }}
                    {{ Form::text('slogan', null, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('title_1', 'Título 1:', ['class' => 'input-group-addon']) }}
                    {{ Form::text('title_1', null, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('text_1', 'Texto 1:', ['class' => 'input-group-addon']) }}
                    {{ Form::textarea('text_1', null, ['class' => 'form-control']) }}
                </div>
            </div>

            <!--<div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('title_2', 'Título 2:', ['class' => 'input-group-addon']) }}
                    {{ Form::text('title_2', null, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('text_2', 'Texto 2:', ['class' => 'input-group-addon']) }}
                    {{ Form::text('text_2', null, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('title_3', 'Título 3:', ['class' => 'input-group-addon']) }}
                    {{ Form::text('title_3', null, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="input-group ui-widget">
                    {{ Form::label('text_3', 'Texto 3:', ['class' => 'input-group-addon']) }}
                    {{ Form::text('text_3', null, ['class' => 'form-control']) }}
                </div>
            </div>-->
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#webContent").click(function (ev) {
            if ($(this).is(":checked")) {
                $("#contFormWeb").fadeIn();
            } else {
                $("#contFormWeb").fadeOut();
            }
        });
    });

</script>