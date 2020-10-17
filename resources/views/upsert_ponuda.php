<?php
declare (strict_types = 1);

namespace App\Ivb\App\Views;

?>

<!DOCTYPE html>
<html lang="en" class="wait">

<head>
    <title>unos/izmena podataka o ponudi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/app1/app/ivb/css/upsert_ponuda.css">
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap-datetimepicker.css">

    <script src="/app1/app/ivb/bootstrap/js/moment.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/sr.js"></script>

    <script src="/app1/app/ivb/bootstrap/js/bootstrap.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/app1/app/ivb/js/upsert_ponuda.js"></script>
    <script src="/app1/app/ivb/js/init.upsert_ponuda.js"></script>
    <script src="/app1/app/ivb/js/ajax.upsert_ponuda.js"></script>
    <script src="/app1/app/ivb/js/common.js"></script>
    <script src="/app1/app/ivb/js/ajax.common.js"></script>
    <script src="/app1/app/ivb/numeraljs/numeral.min.js"></script>
    <script src="/app1/app/ivb/numeraljs/languages.min.js"></script>
</head>

<body>

    <div class="container">
        <div class="panel panel-default">

            <form id="frm_main" class="form-horizontal" autocomplete="off">
                <fieldset>

                    <!-- naslov -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for=""></label>
                        <div class="col-md-4">
                            <h2 id="naslov_forme">-</h2>
                        </div>
                    </div>

                    <!-- datum ponude -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="datetimepicker1">Datum ponude:</label>
                        <div class="col-md-4">
                            <div class='input-group date' id='datetimepicker1'>
                                <input type='text' class="form-control" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- datum zatvaranja ponude -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="datetimepicker2">Datum zatvaranja ponude:</label>
                        <div class="col-md-4">
                            <div class='input-group date' id='datetimepicker2'>
                                <input type='text' class="form-control" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- vrsta vozila -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="lista_vrsta_vozila">Vrsta vozila:</label>
                        <div class="col-md-4">
                            <select id="lista_vrsta_vozila" name="lista_vrsta_vozila" class="form-control">
                                <option value="0">-</option>
                            </select>
                        </div>
                    </div>

                    <!-- brend -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="list_brand">Proizvođač:</label>
                        <div class="col-md-4">
                            <select id="list_brand" name="list_brand" class="form-control">
                                <option value="0">-</option>
                            </select>
                        </div>
                    </div>

                    <!-- model -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="list_model">Model:</label>
                        <div class="col-md-4">
                            <select id="list_model" name="list_model" class="form-control">
                                <option value="0">-</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tip-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="tip">Tip:</label>
                        <div class="col-md-4">
                            <input id="tip" name="tip" placeholder="dodatne oznake" class="form-control input-md" type="text">
                        </div>
                    </div>

                    <!-- Godina proizvodnje -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="god_proizv_lista">Godina proizvodnje</label>
                        <div class="col-md-2">
                            <select id="god_proizv_lista" name="god_proizv_lista" class="form-control">
                                <option value="0">-</option>
                            </select>
                        </div>
                    </div>

                    <!-- tip karoserije -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="tipovi_karoserije_lista">Karoserija:</label>
                        <div class="col-md-4">
                            <select id="tipovi_karoserije_lista" name="tipovi_karoserije_lista" class="form-control">
                                <option value="0">-</option>
                            </select>
                        </div>
                    </div>

                    <!-- vrsta goriva -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="vrste_goriva_lista">Vrsta goriva:</label>
                        <div class="col-md-4">
                            <select id="vrste_goriva_lista" name="vrste_goriva_lista" class="form-control">
                                <option value="0">-</option>
                            </select>
                        </div>
                    </div>

                    <!-- broj pređenih kilometara-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="a006_km">Pređena kilometraža:</label>
                        <div class="col-md-4">
                            <input id="a006_km" name="a006_km" placeholder="broj pređenih km" class="form-control input-md" type="text" value="0">
                        </div>
                    </div>

                    <!-- kubikaža motora-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="a004_ccm">Radna zapremina motora ccm:</label>
                        <div class="col-md-4">
                            <input id="a004_ccm" name="a004_ccm" placeholder="radna zapremina motora ccm" class="form-control input-md" type="text" value="0">
                        </div>
                    </div>

                    <!-- snaga u KW-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="a005_kw">Snaga motora u KW:</label>
                        <div class="col-md-4">
                            <input id="a005_kw" name="a005_kw" placeholder="snaga motora u KW" class="form-control input-md" type="text" value="0">
                        </div>
                    </div>

                    <!-- bruto cena-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="bruto_cena_val">Bruto cena eur:</label>
                        <div class="col-md-4">
                            <input id="bruto_cena_val" name="bruto_cena_val" placeholder="bruto_cena u eur" class="form-control input-md" type="text" value="0">
                        </div>
                    </div>

                    <!-- neto cena-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="neto_cena_val">Neto cena eur:</label>
                        <div class="col-md-4">
                            <input id="neto_cena_val" name="neto_cena_val" placeholder="neto_cena u eur" class="form-control input-md" type="text" value="0">
                        </div>
                    </div>

                    <!-- Mogućnost korišćenja PDV-a -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="vat_ddct_chkbox"></label>
                        <div class="col-md-4">
                            <label class="checkbox-inline" for="vat_ddct_chkbox">
                                <input name="vat_ddct_chkbox" id="vat_ddct_chkbox" type="checkbox"> Moguć povrat PDV-a
                            </label>
                        </div>
                    </div>

                    <!-- Opis -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="text_descr">Opis:</label>
                        <div class="col-md-4">
                            <textarea class="form-control" placeholder="detaljan opis..." id="text_descr" rows="4" name="text_descr"></textarea>
                        </div>
                    </div>
                </fieldset>

                <!-- kontrole za uppload slika -->
                <div id="image_control_group_row">

                </div>


                <!-- dodaj sliku -->
                <div class="row">
                </div>
                <div id="add_pic_section" class="row" style="margin-top: 20px;">
                    <div class="col-md-4">
                        <button id="add_pic_btn" type="button" onclick="AddImageControlGroup()" class="btn pull-right">&nbsp;Dodaj
                            sliku...&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></span></button>
                    </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>


                <div class="row">
                    <p>&nbsp;</p>
                </div>

                <!-- Submit -->
                <div class="row" style="margin-bottom: 20px;">

                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4">
                        <button id="cancel_btn" class="btn pull-right" style="margin-right: 15px;">&nbsp;Otkaži...&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-remove"></span></button>
                        <button id="submit_btn" data class="btn btn-primary pull-right" style="margin-right: 15px;">&nbsp;Upis
                            podataka&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>
                    </div>


                </div>

            </form>
        </div> <!-- panel -->
    </div> <!-- container -->
    <script>
        //postavljanje XSRF tokena na svaki ajax poziv
        $.ajaxSetup({
            headers: {
                'X-XSRF-TOKEN': getCookie("XSRF-TOKEN")
            }
        });


        $(function() {

            //spreči submit forme na ENTER taster  
            $(document).on('keyup keypress', 'form input[type="text"]', (e) => {
                if (e.keyCode == 13) {
                    e.preventDefault();
                }
            });

            //podešavanje kontrola kalendara
            $('#datetimepicker1').datetimepicker({
                locale: 'sr',
                showClear: true,
                showClose: true,
                showTodayButton: true
            });

            $('#datetimepicker2').datetimepicker({
                locale: 'sr',
                showClear: true,
                showClose: true,
                showTodayButton: true
            });

            //update kursora tokom ajax poziva
            $(document).ajaxStart(function() {
                $('html').addClass("wait");
            })

            $(document).ajaxStop(function() {
                $('html').removeClass("wait");
            });

            //postavi stranicu
            SetupPage();
        });
    </script>
</body>

</html> 