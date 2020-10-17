<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Brisanje ponude</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
    <script src="/app1/app/ivb/js/delete_ponuda.js"></script>
    <script src="/app1/app/ivb/js/common.js"></script>
    <script src="/app1/app/ivb/js/ajax.common.js"></script>

</head>

<body>

    <div class="container">
        <h1><br><br></h1>
        <div class="panel panel-default">

            <form id="frm_main" class="form-horizontal" method="post">
                <fieldset>

                    <!-- naslov -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for=""></label>
                        <div class="col-md-4">
                            <h3 id="naslov_forme" class="text-center">Potvrda brisanja ponude</h3>
                        </div>
                    </div>

                    <div class="form-group">
                        <div>
                            <h4 id="labela_ponuda" name="labela_ponuda" class="form-control-static text-center"></h4>
                        </div>
                    </div>

                    <!-- Submit OK -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="submit_btn"></label>
                        <div class="col-md-4 text-center">
                            <span>
                                <button id="submit_btn" name="submit_btn" type="submit" class="btn btn-primary">Obriši!</button>
                                <button id="cancel_btn" name="cancel_btn" href="/app1/public" class="btn">Otkaži</button>
                            </span>
                        </div>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>
    <script>
        $(function() {

            //postavljanje XSRF tokena na svaki ajax poziv
            $.ajaxSetup({
                headers: {
                    'X-XSRF-TOKEN': getCookie("XSRF-TOKEN")
                }
            });

            //spreči submit forme na ENTER taster  
            $(document).on('keyup keypress', 'form input[type="text"]', function(e) {

                if (e.keyCode == 13) {
                    e.preventDefault();
                }
            });
            //postavi stranicu
            SetupPage();

        });
    </script>
</body>

</html> 