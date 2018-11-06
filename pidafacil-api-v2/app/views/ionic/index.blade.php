<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body style="background-image:url(http://localhost/pidafacil/public/images/background.jpg); background-repeat: no-repeat;">
        @section('sidebar')            
        @show

        <div class="container">                        
            <?
            
            ?>
            <form action="{{$url}}"  method="POST" name="formStep2" id="formStep2">
                <table>
                    <tr><td><input type ="hidden" name="billing-cc-number" value="{{$number_credit_card}}"></td></tr>
                    <tr><td><input type ="hidden" name="billing-cc-exp" value="{{$ccexp}}"></td></tr>
                    <tr><td><input type ="hidden" name="cvv" value="{{$cvv}}"></td></tr>
                </table>
                <input type="submit" value="Enviar">
            </form>                
        </div>
    </body>
</html>