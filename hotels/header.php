
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hotel Search</title>
        
        <script>
            window.app = {};
            app.baseUrl = '';
            app.assetUrl = '';
            app.disableElement = function($domId){
                $("#"+$domId).attr("disabled", "disabled");
            };
            app.enableElement = function($domId){
                $("#"+$domId).removeAttr("disabled");
            };
                        
            app.parseInt = function(val, defaultval){
                return !isNaN(parseInt(val)) ? parseInt(val) : (defaultval == undefined ? 0 : defaultval) ;
            };
            app.parseFloat = function(val, defaultval){
                return !isNaN(parseFloat(val)) ? parseFloat(val) : (defaultval == undefined ? 0.00 : defaultval) ;
            };
        </script>
        
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <script href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" /></script>
        
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>        
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" rel="stylesheet">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        <script src="../js/jquery/js/jquery.ui.autocomplete.html.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
        
        <style>
        .ui-autocomplete-loading {
            background: white url('ui-anim_basic_16x16.gif') right center no-repeat;
        }    
            
        label.error{
            color : red
        }
        input.error, select.error{
            border: 1px solid red
        }
        </style>
        
    </head>
    <body style="width: 100%">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-xs-12 col-md-12">
                    <ol class="breadcrumb">
                        <li><a href="index.php">Hotel Search</a></li>
                        <li><a href="order_list.php">Order List</a></li>
                        <li><a href="multibookingsearch.php">Multi Booking Search</a></li>
                    </ol>
                </div>
            </div>            
        </div>