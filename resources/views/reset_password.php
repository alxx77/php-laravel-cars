<?php
declare (strict_types = 1);

namespace App\Ivb\App\Views;

use App\Ivb\App\IVB_User;
use Illuminate\Support\Facades\Redirect;
use Exception;



require_once(app_path().'\ivb\app\class.user.php');



//ako je obrada zahteva
if (isset($_POST['email'])) {

    //pokušaj da nađeš korisnika
    $u = IVB_User::getUserByEmail($_POST['email']);

    //ako korisnik postoji
    if (!isset($u)) {
        Redirect::route('user_not_found_error');
    }

    //resetuj lozinku i pošalji na mail

    //Dozvoljeni karatkteri u lozinci
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $count = strlen($chars);

    // Generisanje 12 random bajtova
    $bytes = random_bytes(12);

    //izlaz
    $new_password = '';

     //podeli niz slučajnih bajtova na pojedinačne karaktere i spoj ih
    foreach (str_split($bytes) as $byte) {
        $new_password .= $chars[ord($byte) % $count];
    }

    $msg = "Poštovani, primljen je zahtev za promenu lozinke na sajtu www.___________.rs za korisnika: " . $u->getUserName() . "\r\n";

    $msg = $msg. "Nova lozinka je: " . $new_password;

    //pokušaj da pošalješ mail
    try {
        mail($u->getEmail(), "Zahtev za promenu lozinke", $msg);
    } catch (Exception $e) {
        Redirect::route('general_error');
    }

    //postavi hash passworda
    $u->setPasswordHash(md5($new_password . $u->getPasswordSalt()));

    //pokušaj upis
    try {

        $u->UpdateUser();
    } catch (Exception $e) {
        Redirect::route('general_error');
    }

    //ako je sve ok prikaži stranicu sa potvrdom
    Redirect::route('reset_password_sent');

}

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Prijavljivanje:</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/app1/app/ivb/css/upsert_ponuda.css">
</head>

<body>
    <div class="container">

        <div class="form-group">
            <div class="col-md-4">
                <h2><br></h2>
            </div>
        </div>

        <div class="panel panel-default">
            <form class="form-horizontal" method="post">

                <!-- naslov -->
                <div class="form-group">
                    <div class="col-md-4">
                        <h3 id="naslov_forme">Resetovanje zaboravljene lozinke:</h3>
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


                <div class="form-group">
                    <label class="col-md-4 control-label" for="btn-reset_password"></label>
                    <div class="col-md-4">
                        <button id="btn-reset_password" type="submit" class="btn btn-primary">Resetuj
                            lozinku</button>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label" for=""></label>
                    <div class="col-md-4">
                        <a class="form-control-static" href="/ivb/login.php">Prijava...</a>
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
</body>
</html>