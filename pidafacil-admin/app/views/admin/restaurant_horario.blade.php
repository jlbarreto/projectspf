@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1  class="title">Horarios de Restaurante {{ $restaurant->name }}</h1>

    <a href ="{{ URL::to('admin/restaurant/edit/'.$restaurant->slug) }}" class="btn btn-success">Ir a restaurante</a>
    <br/>
    <br/>
    <br/>
    {{ Form::open(array('url' => 'admin/restaurant/set_horarios/'.$restaurant->slug, 'id'=>'formEnv')) }}


    <style>
        select
        {
            min-width: 90px;
            width: 90px;
        }
    </style>
    @foreach($servicios as $servicio)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ $servicio->service_type }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-hover">
                <tr>
                    <th>Abierto</th>
                    <th>Día</th>
                    <th>Hora de abrir</th>
                    <th>Hora de cerrar</th>
                </tr>
                @foreach($arrDias as $k=>$dia)
                <tr>
                    <td>{{ Form::checkbox($servicio->service_type_id.'_dias[]', $k, true) }}</td>
                    <td>{{ $dia }}</td>
                    <td>
                        <div class="form-group">
                            <div class="input-group ui-widget">
                                {{ Form::selectRange($servicio->service_type_id.'_hora_a_'.$k, 1, 24); }} : 
                                {{ Form::selectRange($servicio->service_type_id.'_minuto_a_'.$k, 00, 59); }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <div class="input-group ui-widget">
                                {{ Form::selectRange($servicio->service_type_id.'_hora_c_'.$k, 1, 24); }} : 
                                {{ Form::selectRange($servicio->service_type_id.'_minuto_c_'.$k, 0, 59, ['class'=>'disabled']); }}
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endforeach
    <script>
        $(document).ready(function () {
            $("input[name$='dias[]']").click(function () {
                var tr = $(this).parent().parent();
                var type = $(this).prop('name').split('_')[0];
                var checked = $(this).is(':checked');
                if (checked) {
                    tr.removeClass('danger');
                } else {
                    tr.addClass('danger');
                }

                $("select[name='" + type + "_hora_a_" + $(this).val() + "']").prop('disabled', !checked);
                $("select[name='" + type + "_minuto_a_" + $(this).val() + "']").prop('disabled', !checked);
                $("select[name='" + type + "_hora_c_" + $(this).val() + "']").prop('disabled', !checked);
                $("select[name='" + type + "_minuto_c_" + $(this).val() + "']").prop('disabled', !checked);
            });
        });
    </script>

    {{ Form::submit('Ingresar Horario', ['class' => 'btn btn-primary']) }}
    {{ Form::close() }}

</div>
<script>
    $(document).ready(function () {
        <?php if(count($arr_schedules)==0){ ?>
        $('select').change(function () {
            var ele = $(this).prop('name').split('_');
            var servicio = ele[0];
            var time = ele[1];
            var type = ele[2];
            for (var i = 1; i <= 7; i++) {
                var select = $('select[name="' + servicio + '_' + time + '_' + type + '_' + i + '"]');
                if ((time=='hora' && select.val() == 1) || (time=='minuto' && select.val() == 0)) {
                    select.val($(this).val());
                }
            }
        });
        <?php }else{ ?>
        $.each($("input[type='checkbox']"), function(){
            $(this).prop('checked', false);
        });
        
        setTimeout(function(){
        @foreach($arr_schedules as $schedule)
            $("input[name='{{ $schedule['service'] }}_dias[]'][value='{{ $schedule['dia'] }}']").prop('checked', true);
            $("select[name='{{ $schedule['service'] }}_hora_{{ $schedule['type'] }}_{{ $schedule['dia'] }}']").val({{ $schedule['hora'] }});
            $("select[name='{{ $schedule['service'] }}_minuto_{{ $schedule['type'] }}_{{ $schedule['dia'] }}']").val({{ $schedule['minuto'] }});
        @endforeach
        }, 1000);
        
        setTimeout(function(){
            $.each($("input[type='checkbox']"), function(){
                var checked=$(this).is(':checked');
                if(!checked){
                    $(this).parent().parent().addClass('danger');
                    var type = $(this).prop('name').split('_')[0];
                    $("select[name='" + type + "_hora_a_" + $(this).val() + "']").prop('disabled', !checked);
                    $("select[name='" + type + "_minuto_a_" + $(this).val() + "']").prop('disabled', !checked);
                    $("select[name='" + type + "_hora_c_" + $(this).val() + "']").prop('disabled', !checked);
                    $("select[name='" + type + "_minuto_c_" + $(this).val() + "']").prop('disabled', !checked);
                }
            });
        }, 1000);
        <?php } ?>
    });
</script>
@stop