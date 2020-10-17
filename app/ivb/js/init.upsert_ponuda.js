/*jshint esversion: 8 */

//inicijalizacija strane
async function InitPage() {
    //***************************************************** */

    //popunjavanje vrednosti svih kontrola na formi
    //i njihovo inicijalno podešavanje
    //ovo se radi bez obzira koji je mod rada forme


    //***************************************************** */




    //postavi naslov u zavisnosti od toga da li je u pitanju
    //unos novog sloga ili izmena postojećeg

    let action = GetURLParameter("action");

    if (action == "insert") {
        $("#naslov_forme").text("Unos podataka");
    } else {
        $("#naslov_forme").text('Izmena podataka ');
    }


    //postavljanje vrednosti u dropdown listu za vrstu vozila
    await PostaviVrstuVozila();


    //postavi drop down listu za brend,model,vrstu

    //brendovi
    await InitBrandList();

    //modeli
    await InitModelList();


    //postavi tipove karoserije
    //ali prvo proveri da li je tip vozila automobil
    //samo u tom slučaju ima potrebe da se lista 'tip karoserije' vidi, inače ne 
    InitTipoviKaroserijeList();


    //postavi listu godina
    $("#god_proizv_lista").html(KreirajListuGodina());

    //vrste goriva
    await InitVrsteGorivaList();


    //potrebno je postaviti change event handler za tip vozila
    $('#lista_vrsta_vozila').change(async () => {

        //uradi update liste modela
        await InitModelList();

        //proveri vidljivost tipa karoserije
        ProveriVidljivostListeSaTipovimaKaroserija();

    });

    //postavi change event handler za listu brendova
    //pošto se za svaku izmenu brenda mora izmeniti i lista modela
    $('#list_brand').change(async () => {
        //uradi update liste modela
        await InitModelList();
    });

    //bruto i neto cena
    let bc = 0;
    let nc = 0;

    //provera unosa cene
    $("#bruto_cena_val").focusout(function () {
        //ako nema ničeg stavi 0
        if ($('#bruto_cena_val').val().length == 0) {
            $('#bruto_cena_val').val(0);
        }

        //proveri šta je unutra...
        //pokušaj da konvertuješ u double
        try {
            bc = numeral().unformat($("#bruto_cena_val").val());
        } catch (error) {

            alert('Nevalidan unos bruto cene:' + bc);
            $('#bruto_cena_val').val(0);
        }
        //ako je sve u redu
        //upiši ponovo vrednost nazad
        $('#bruto_cena_val').val(numeral(bc).format("0,0.00"));

    });

    //na fokus elementa selektuj sav sadržaj     
    $("#bruto_cena_val").focusin(function () {
        $(this).select();
    });

    //neto cena     
    $("#neto_cena_val").focusout(function () {
        //ako nema ničeg stavi 0
        if ($('#neto_cena_val').val().length == 0) {
            $('#neto_cena_val').val(0);
        }

        //proveri šta je unutra...
        //pokušaj da konvertuješ u double
        try {
            nc = numeral().unformat($("#neto_cena_val").val());
        } catch (error) {

            alert('Nevalidan unos neto cene...!');
            $('#neto_cena_val').val(0);
        }

        //ako je sve u redu
        //upiši ponovo vrednost nazad
        $('#neto_cena_val').val(numeral(nc).format("0,0.00"));

    });

    //na fokus elementa selektuj sav sadržaj  
    $("#neto_cena_val").focusin(function () {

        $(this).select();
    });

    //na fokus elementa selektuj sav sadržaj  
    //kilometraža
    $("#a006_km").focusin(function () {

        $(this).select();
    });

    $("#a004_ccm").focusin(function () {

        $(this).select();
    });

    $("#a005_kw").focusin(function () {

        $(this).select();
    });


}


//popunjavanje kontrola sa vrednostima iz već postojeće ponude
async function FillValues() {

    //ponuda
    let id_pon = -1;

    //pokušaj da izdvojiš parametar iz url-a
    try {
        id_pon = parseInt(GetURLParameter("id_pon"));
    } catch (error) {

    }

    //ako parametar nije izdvojen
    //vrati grešku
    if (id_pon == -1) {
        //ako nije izađi...
        window.location = "/app1/public/general_error";
    }

    //učitaj podatke o ponudi
    let ponuda = await GetPonudaData(id_pon);

    if (ponuda == null) {
        alert('Greška u obradi...!');
        window.location = "/app1/public";
    }

    //datum ponude
    if (ponuda.dat_pon != null) {
        $("#datetimepicker1").data("DateTimePicker").date(new Date(ponuda.dat_pon.date));
    }


    if (ponuda.dat_zatv != null) {
        //datum zatvaranja ponude
        $("#datetimepicker2").data("DateTimePicker").date(new Date(ponuda.dat_zatv.date));
    }

    //dodaj i broj ponude
    $("#naslov_forme").html('Izmena podataka<br>' + '<h5 style="color:blue"> ponuda br.: ' + ponuda.brpon + '</h5>');

    //postavi vrstu vozila
    $("#lista_vrsta_vozila").val(ponuda.tip_vozila_kod);

    //postavi brend
    $("#list_brand").val(ponuda.id_brand);

    //osveži listu modela (zbog moguće promene brenda)
    await InitModelList();

    //postavi model
    $("#list_model").val(ponuda.id_model);

    //proveri karoserije
    ProveriVidljivostListeSaTipovimaKaroserija();

    //Tip
    $("#tip").val(ponuda.tip);

    //godina proizvodnje
    $("#god_proizv_lista").val(ponuda.god_proizv);

    //tip karoserije
    $("#tipovi_karoserije_lista").val(ponuda.a000_karoserija);

    //vrsta goriva
    $("#vrste_goriva_lista").val(ponuda.a002_tip_goriva);

    //broj pređenih kilometara
    $("#a006_km").val(numeral(parseFloat(ponuda.a006_km)).format("0,0"));

    //radna zapremina
    $("#a004_ccm").val(numeral(parseFloat(ponuda.a004_ccm)).format("0,0"));

    //snaga KW
    $("#a005_kw").val(numeral(parseFloat(ponuda.a005_kw)).format("0,0.00"));

    //bruto cena eur
    $('#bruto_cena_val').val(numeral(parseFloat(ponuda.bruto_cena_val)).format("0,0.00"));

    //neto cena val
    $('#neto_cena_val').val(numeral(parseFloat(ponuda.neto_cena_val)).format("0,0.00"));

    //povrat PDV-a
    if (ponuda.vat_ddct == true) {
        //postavi checked atribut
        $("#vat_ddct_chkbox").prop('checked', true);
    } else {
        $("#vat_ddct_chkbox").prop('checked', false);
    }

    //opis
    $('#text_descr').val(ponuda.opis);

    //slike
    //učitaj info za sve slike za ponudu
    let images = await GetImagesInfo(id_pon);


    //prolazak kroz listu slika
    await asyncForEach(images, async (val, idx, arr) => {

        //dodaj html kontrole za sliku
        let group_uuid = AddImageControlGroup();

        let response = await fetch("./api/images/get_image/" + val.image_id.toString());

        let blob = await response.blob();

        let img = new Image();

        img.src = URL.createObjectURL(blob);

        //dodaj blob u listu blobova
        image_blobs.push({
            "uuid": group_uuid,
            "blob": blob
        });

        document.getElementById("img_prev_" + group_uuid).src = img.src;

    });

    $('html').removeClass("wait");

}

//lista sa tipom karoserija treba biti onemogućena ako je vrsta vozila bilo šta sem automobila
function ProveriVidljivostListeSaTipovimaKaroserija() {
    if ($("#lista_vrsta_vozila").val() == '001') {
        $("#tipovi_karoserije_lista").removeAttr('disabled');
    } else {
        $("#tipovi_karoserije_lista").attr('disabled', '');
    }
}

//kreiraj listu godina
function KreirajListuGodina() {
    let currentDate = new Date();
    let start_year = currentDate.getFullYear() + 1;
    let end_year = 2000;
    let items = '';

    //kreiraj opadajuću listu
    for (let i = start_year; i >= end_year; i--) {
        items += "<option value='" + i + "'>" + i + "</option>";
    }

    return items;
}

