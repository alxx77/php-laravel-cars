<?php

declare (strict_types = 1);

namespace App\Ivb\App\Views;


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Prodaja vozila</title>
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
    <link href="/app1/app/ivb/css/index.css" rel="stylesheet">
    <script src="/app1/app/ivb/js/index.js"></script>
    <script src="/app1/app/ivb/js/init.index.js"></script>
    <script src="/app1/app/ivb/js/ajax.index.js"></script>
    <script src="/app1/app/ivb/js/ajax.common.js"></script>
    <script src="/app1/app/ivb/js/listview.index.js"></script>
    <script src="/app1/app/ivb/js/pagination.index.js"></script>
    <script src="/app1/app/ivb/js/common.js"></script>

    <script src="/app1/app/ivb/numeraljs/numeral.min.js"></script>
    <script src="/app1/app/ivb/numeraljs/languages.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/moment.js"></script>

    <script src="/app1/app/ivb/slider/responsiveslides.min.js"></script>
    <link href="/app1/app/ivb/slider/responsiveslides.css" rel="stylesheet">
    <link rel="stylesheet" href="/app1/app/ivb/slider/themes/themes.css?v=1.5">
</head>

<body>
    <!-- navbar -->

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container" style="padding-left: 0px;padding-right: 0px;">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">ABC</a>
            </div>
            <div class="collapse navbar-collapse">

                <ul id="root_menu_navbar" class="nav navbar-nav">
                    <li><a href="#about">O nama</a></li>
                    <li><a href="/app1/public/contact">Kontakt</a></li>
                </ul>

                <ul id="nav1" class="nav navbar-nav navbar-right">
                </ul>

            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>


    <!-- stranica -->
    <div class="container" style="padding-left: 0px;padding-right: 0px;">

        <!-- naslovna slika + info o firmi -->
        <div class="row">
            <div class="col-md-8">
                <img class="img-responsive img-rounded" src="/app1/app/ivb/resources/hfoto.jpg" alt="">
            </div>
            <div class="col-md-4">
                <img class="img-responsive img-rounded center-block" width="100" src="/app1/app/ivb/resources/logo.bmp" alt="">
                <h2 class="text-center" id="company-name">&nbsp;&nbsp;ABC Solutions</h2>
                <ul>
                    <li>Usluge uvoza vozila</li>
                    <li>Prodaja svih vrsta polovnih vozila i radnih mašina</li>
                    <li>Finansijski konsalting</li>
                    <li>Zastupništvo firme Sejari za region zapadne i južne Srbije za:</li>
                    <ul>
                        <li>Krone</li>
                        <li>MAN</li>
                        <li>Neoplan</li>
                    </ul>
                </ul>
            </div>
        </div>

        <hr>

        <!-- logo -->
        <div id="img_header" class="row">
            <div class="col-md-3">
                <img class="img-responsive img-rounded" width="250px" src="/app1/app/ivb/resources/sejari_logo.jpg" alt="">
            </div>
            <div class="col-md-3">
                <img class="img-responsive img-rounded" width="250px" src="/app1/app/ivb/resources/krone_logo.jpg" alt="">
            </div>
            <div class="col-md-3">
                <img class="img-responsive img-rounded" width="250px" src="/app1/app/ivb/resources/man_logo.jpg" alt="">
            </div>
            <div class="col-md-3">
                <img class="img-responsive img-rounded" width="250px" src="/app1/app/ivb/resources/neoplan_logo.jpg" alt="">
            </div>
        </div>

        <hr>

        <!-- paginacija -->
        <div id="pc1" class="container">

            <div class="row" style="padding:0; margin-bottom: 0px;">
                <!--broj elemenata po strani-->
                <div class="col-md-4" style="padding:0; margin-bottom: 0px;">
                    <!--broj elemenata po strani-->
                    <div class="form-group" style="padding:0; margin-bottom: 0px;">
                        <label class="control-label">Vozila po strani:</label>
                        <a id="page_size_2" href="">&nbsp;2&nbsp;</a>
                        <a id="page_size_3" href="">&nbsp;3&nbsp;</a>
                        <a id="page_size_5" href="">&nbsp;5&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                        <!--samo aktivni-->
                        <label id="active_only_cb" class="control-label checkbox-inline" style="visibility:hidden">
                            <input onClick="SetCurrentPage(1);RenderListView();" id="active_only" type="checkbox" checked="true">Samo aktivne ponude
                        </label>
                    </div>
                </div>

                <!-- lista brendova -->
                <div class="form-group col-md-4" style="margin-bottom: 0px;">
                    <select id="list_brand" name="list_brand" class="form-control" onChange="SetCurrentPage(1);RenderListView();">
                        <option value="0">-</option>
                    </select>
                </div>
            </div>

            <!-- paginacija - stranice-->
            <div class="row" id="pagination" style="padding:0;">
                <div class="form-group col-md-4" style="padding:0;margin:0">
                    <div class="pagination" style="padding:0;margin:0">
                        <div id="data-page_size" style="display:none" data-page_size=<?php echo $page_size==null ? env("page_size") : $page_size; ?>></div>
                        <span><a id="first_page" class="first_page" href="">&lt; &lt;</a></span>
                        <span>&nbsp;</span>
                        <span><a id="prev_page" class="prev_page" href="">&lt;</a></span>
                        <span>&nbsp;</span>
                        <span>
                            <a id="pagegapmin" class="pagegapmin" style="display:none">...</a>
                        </span>
                        <span><a id="p1" class="p1" href=""></a></span>
                        <span><a id="p2" class="p2" href=""></a></span>
                        <span><a id="p3" class="p3" href=""></a></span>
                        <span><a id="p4" class="p4" href=""></a></span>
                        <span><a id="p5" class="p5" href=""></a></span>
                        <span>
                            <a id="pagegapmax" class="pagegapmax" style="display:none">...</a>
                        </span>
                        <span>&nbsp;</span>
                        <span><a id="next_page" class="next_page" href="">&gt;</a></span>
                        <span>&nbsp;</span>
                        <span><a id="last_page" class="last_page" href=""> &gt; &gt;</a></span>
                    </div>
                </div>
            </div>
        </div><!-- paginacija -->

        <div class="row" style="padding:0;">
            <p>&nbsp;</p>
        </div>

        <!-- lista vozila -->
        <div class="row" id="list_container" style="margin-left: 0px; margin-right: 0px;">
            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                <h4 id="naslov_lista"></h4>
            </div>
            <div>
                <ul id="lista_ponuda" class="list-group"></ul>
            </div>
        </div>

        <!-- paginacija - stranice-->
        <div class="row" id="pagination2" style="padding:0;">
            <div class="form-group col-md-4" style="padding:15;margin:0">
                <div class="pagination" style="padding:0;margin:0">
                    <span><a id="first_page2" class="first_page" href="">&lt; &lt;</a></span>
                    <span>&nbsp;</span>
                    <span><a id="prev_page2" class="prev_page" href="">&lt;</a></span>
                    <span>&nbsp;</span>
                    <span>
                        <a id="pagegapmin2" class="pagegapmin" style="display:none">...</a>
                    </span>
                    <span><a id="p12" class="p1" href=""></a></span>
                    <span><a id="p22" class="p2" href=""></a></span>
                    <span><a id="p32" class="p3" href=""></a></span>
                    <span><a id="p42" class="p4" href=""></a></span>
                    <span><a id="p52" class="p5" href=""></a></span>
                    <span>
                        <a id="pagegapmax2" class="pagegapmax" style="display:none">...</a>
                    </span>
                    <span>&nbsp;</span>
                    <span><a id="next_page2" class="next_page" href="">&gt;</a></span>
                    <span>&nbsp;</span>
                    <span><a id="last_page2" class="next_page" href=""> &gt; &gt;</a></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modalna forma za galeriju slika-->
    <div id="image_gallery" class="modal fade">
        <div class="modal-dialog .modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-body">
                    <div id="img_slider" class="rslides_container">

                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- Modalna forma za komentare-->
    <div id="comments_form" class="modal fade" style="overflow: hidden">
        <div class="modal-dialog .modal-dialog-centered">
            <div class="modal-content ">
                <div class="modal-header">
                    <div class="col-md-3">
                        <h5>Komentari:</h5>
                    </div>
                </div>
                <div id="comment_modal_body" class="modal-body" style="max-height: 60vh; overflow: auto;padding-bottom: 0px;padding-top: 0px; padding-left: 35px; padding-right: 35px;">
                    <ul id="lista_komentara" class="list-group" style=" margin-bottom: 0px;">
                    </ul>
                </div>
                <hr>
                <div class="panel-body" style="padding-left: 35px; padding-right: 50px;">
                    <div style="height:70px;">
                        <textarea class="form-control" placeholder="komentar..." id="komentar" rows="4"></textarea>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="modal-footer" style="padding-right: 35px;">
                    <button id="comment_cancel_btn" class="btn pull-right" style="margin-right: 15px;">&nbsp;Otkaži...&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-remove"></span></button>
                    <button id="comment_submit_btn" data-id_pon="" class="btn btn-primary pull-right" style="margin-right: 15px">&nbsp;Upiši
                        komentar...&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>
                    <div>&nbsp;</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="text-muted">Copyright &copy; ABC Solutions 2019</div>
        </div>
    </footer>

    <script>
        "use strict";

        //sprečavanje POST Resubmit događaja kod osvežavanja stranice
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

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