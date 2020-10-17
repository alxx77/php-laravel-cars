<?php
declare (strict_types = 1);

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Upravljanje korisničkim nalozima</title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/app1/app/ivb/js/settings.js"></script>
    <script src="/app1/app/ivb/js/common.js"></script>
    <script src="/app1/app/ivb/js/ajax.common.js"></script>
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
    <link href="/app1/app/ivb/css/settings.css" rel="stylesheet">
</head>

<body>
    <div class="container col-md-12">
        <h3><br><br></h3>
        <h3>Podešavanja aplikacije</h3>
        <div class="well">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#brands">Proizvođači</a>
                </li>
                <li><a data-toggle="tab" href="#models">Modeli</a>
                </li>
                <li><a data-toggle="tab" href="#cms">CMS</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="brands" class="tab-pane fade in active">

                    <!-- razdvajanje -->
                    <hr>

                    <form id="frm_main" class="form-horizontal" method="post">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="list_brand">Lista proizvođača:</label>
                                <div class="col-md-4">
                                    <select id="list_brand" name="list_brand" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="brand">Unos:</label>
                                <div class="col-md-4">
                                    <input id="brand" name="brand" placeholder="proizvođač" class="form-control input-md" type="text">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="insert_brand"></label>
                                <div class="col-md-4">
                                    <button id="insert_brand" name="insert_brand" type="submit" class="btn btn-primary btn-md">&nbsp;&nbsp;&nbsp;Upis&nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                            <fieldset>
                    </form>
                </div>
                <div id="models" class="tab-pane fade">

                    <!-- razdvajanje -->
                    <hr>

                    <form id="frm_model" class="form-horizontal" method="post">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="model_list_brand">Lista proizvođača:</label>
                                <div class="col-md-4">
                                    <select id="model_list_brand" name="model_list_brand" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="lista_vrsta_vozila">Vrsta vozila:</label>
                                <div class="col-md-4">
                                    <select id="lista_vrsta_vozila" name="lista_vrsta_vozila" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="list_model">Lista modela:</label>
                                <div class="col-md-4">
                                    <select id="list_model" name="list_model" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="model">Unos modela:</label>
                                <div class="col-md-4">
                                    <input id="model" name="model" placeholder="model" class="form-control input-md" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="insert_model"></label>
                                <div class="col-md-4">
                                    <button id="insert_model" name="insert_model" type="submit" class="btn btn-primary btn-sm">&nbsp;&nbsp;&nbsp;Upiši&nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                            <fieldset>
                    </form>

                </div>
                <div id="cms" class="tab-pane fade">
                    <div class="container">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Uređivanje menija CMS</div>
                            <div class="panel-body">
                                <div id="cms_pages" class="row">
                                </div>
                                <hr>
                                <div class="row">
                                    <form id="frm_cms" class="form-horizontal" action="/app1/public/api/cms/insert_cms_page" method="post">
                                        <?php echo csrf_field(); ?>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label" for="rootx">Osnovni meni:</label>
                                                <div class="col-md-4">
                                                    <input id="rootx" name="rootx" class="form-control input-md" value="usluge" readonly type="text">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-4 control-label" for="pathx">Adresa:</label>
                                                <div class="col-md-4">
                                                    <input id="pathx" name="pathx" placeholder="adresa noda" class="form-control input-md" type="text">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label" for="node_type">Tip
                                                    unosa:</label>
                                                <div class="col-md-4">
                                                    <select id="node_type" name="node_type" class="form-control">
                                                        <option value="0">Podmeni</option>
                                                        <option value="1">Stranica</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label" for="descr">Naziv:</label>
                                                <div class="col-md-4">
                                                    <input id="descr" name="descr" placeholder="naziv..." class="form-control input-md" type="text">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-4 control-label" for="idx">Redni broj u okviru
                                                    grupe:</label>
                                                <div class="col-md-4">
                                                    <input id="idx" name="idx" placeholder="redni br." class="form-control input-md" type="number">
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-4 control-label" for="insert_node"></label>
                                                <div class="col-md-4">
                                                    <button id="insert_node" type="submit" class="btn btn-primary btn-sm">&nbsp;&nbsp;&nbsp;Upiši&nbsp;&nbsp;&nbsp;</button>
                                                </div>
                                            </div>
                                            <fieldset>
                                    </form>
                                </div>
                            </div>
                            <div class="panel-footer">

                            </div>
                        </div>

                    </div>

                </div>
                <div id="view_stats" class="tab-pane fade">
                    <div class="container">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Statistika pregleda vozila</div>
                            <div class="panel-body">
                                <div id="cms_pages" class="row">
                                </div>
                            </div>
                            <div class="panel-footer">

                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div><a class="pull-right" href="/app1/public">Početna stranica</a></div>
        </div>
    </footer>
    <hr>

    <script>
        //setup stranice
        $(function() {
            //postavljanje XSRF tokena na svaki ajax poziv
            $.ajaxSetup({
                headers: {
                    'X-XSRF-TOKEN': getCookie("XSRF-TOKEN")
                }
            });

                        //update kursora tokom ajax poziva
                        $(document).ajaxStart(function() {
                $('html').addClass("wait");
            })

            $(document).ajaxStop(function() {
                $('html').removeClass("wait");
            });

            SetupPage();

        });
    </script>
</body>

</html> 