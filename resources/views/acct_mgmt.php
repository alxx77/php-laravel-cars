<?php

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Upravljanje korisničkim nalozima</title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
    <script src="/app1/app/ivb/js/acct_mgmt.js"></script>
    <script src="/app1/app/ivb/js/common.js"></script>
    <script src="/app1/app/ivb/js/ajax.common.js"></script>
</head>

<body>
    <div class="container">
        <h2><br></h2>
        <div class="panel panel-default">
            <div class="panel-heading text-center">Korisnički nalozi:</div>
            <h3><br></h3>
            <ul id="users" class="list-group"></ul>
            <h3><br></h3>
            <div class="panel-footer"></div>
        </div>
        <a class="pull-right" href="/app1/public">Početna stranica</a>
    </div>

    <!-- Modal  -->
    <div class="modal fade" id="modal_pwd_set" role="dialog">
        <div class="modal-dialog">


            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Uređivanje korisničkog naloga:</h4>
                    <h6 id="label1" class="modal-title"></h6>
                </div>
                <div class="modal-body">

                    <form id="frm_edit_user" method="post">

                        <div class=" form-group">
                            <label for="user_email">Email:</label>
                            <input class="form-control" id="user_email" name="user_email" placeholder="" type="text" value="">
                        </div>
                        <div class=" form-group">
                            <label for="username">Korisničko ime:</label>
                            <input class="form-control" id="username" name="username" placeholder="" type="text" value="">
                        </div>

                        <div class="form-group">
                            <label for="new_pwd">Nova lozinka:</label>
                            <input class="form-control" id="new_pwd" name="new_pwd" placeholder="password" type="password" value="">
                        </div>

                        <div class="form-group">
                            <label for="user_type">Tip korisnika:</label>
                            <div class="radio-inline">
                                <label><input type="radio" id="optradio_admin" name="optradio">Admin</label>
                            </div>
                            <div class="radio-inline">
                                <label><input type="radio" id="optradio_user" name="optradio">Korisnik</label>
                            </div>
                            <div class="radio-inline">
                                <label><input type="radio" id="optradio_guest" name="optradio">Gost</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_type"></label>
                            <div class="checkbox-inline">
                                <label><input id="chk_delete_user" name="chk_delete_user" type="checkbox" value="">Obriši korisnika</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button id="save_btn" type="submit" class="btn btn-default">&nbsp;Snimi&nbsp;</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Otkaži</button>
                        </div>

                        <input type="hidden" id="user_id" name="user_id" value="">

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        //setup stranice
        $(function() {
            //postavljanje XSRF tokena na svaki ajax poziv
            $.ajaxSetup({
                headers: {
                    'X-XSRF-TOKEN': getCookie("XSRF-TOKEN")
                }
            });

            SetupPage();
        });
    </script>
</body>

</html> 