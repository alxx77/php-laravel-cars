<?php

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Prijavljivanje:</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/bootstrap.min.js"></script>
    <script src="/app1/app/ivb/js/common.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
</head>

<body>
    <div class="container">

        <div class="form-group">
            <div class="col-md-4">
                <h2><br></h2>
            </div>
        </div>

        <div class="panel panel-default">
            <form class="form-horizontal" method="post" action="/app1/public/api/users/login">
                <?php echo csrf_field(); ?>
                <!-- naslov -->
                <div class="form-group">
                    <div class="col-md-4">
                        <h3 id="naslov_forme">Prijava korisnika:</h3>
                    </div>
                </div>

                <!-- E-mail-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="email">E-mail:</label>
                    <div class="col-md-4">
                        <input id="email" name="email" placeholder="e-mail" class="form-control input-md" type="email"
                            required>
                    </div>
                </div>

                <!-- Password-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="password">Lozinka:</label>
                    <div class="col-md-4">
                        <input id="password" name="password" placeholder="lozinka" class="form-control input-md"
                            type="password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="btn-login"></label>
                    <div class="col-md-4">
                        <button id="btn-login" name="btn-login" type="submit" class="btn btn-primary">Prijava</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="btn-login"></label>
                    <div class="col-md-4">
                        <a class="form-control-static" href="/app1/public/register">Registracija...</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="btn-login"></label>
                    <div class="col-md-4">
                        <a class="form-control-static" href="/app1/public">Početna stranica...</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="btn-login"></label>
                    <div class="col-md-4">
                        <a class="form-control-static" href="/app1/public/reset_password">Zaboravljena lozinka...</a>
                    </div>
                </div>

        </div>

        </form>
    </div>
    </div>
</body>

</html>