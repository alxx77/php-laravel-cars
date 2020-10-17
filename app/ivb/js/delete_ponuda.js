/*jshint esversion: 8 */

//prikaz vozila
async function SetupPage() {


    let id_pon = -1;

    //pokušaj da izdvojiš parametar iz url-a
    try {
        id_pon=GetURLParameter("id_pon");

        id_pon = parseInt(id_pon);
    } catch (error) {

    }

    //ako parametar nije izdvojen
    //vrati grešku
    if (id_pon == -1) {
        //ako nije izađi...
        window.location = "/app1/public/general_error";
    }

    //podaci o ponudi
    let ponuda = await GetPonudaData(id_pon);


    if (ponuda == null) {
        window.location = "/app1/public/general_error";
    }

    //upiši info o ponudi na stranicu
    $('#labela_ponuda').text('Obriši ponudu broj ' + ponuda.brpon + ' - ' + ponuda.brand + ' ' + ponuda.model + '?');


    //submit event forme
    //**************************************************************
    $("#frm_main").on("submit", function (e) {

        //zaustavi sve...
        e.stopPropagation();
        e.preventDefault();

        //kontejner za podatke
        var formData = new FormData();

        //id ponude
        formData.append('id_pon', GetURLParameter("id_pon"));


        $.ajax({
            method: "POST",
            url: "./api/ponuda/delete_ponuda",
            data: formData,
            cache: false,
            async: true,
            processData: false,
            contentType: false,
            success: function (data) {
                //sačuvaj rezultat

                var x = false;

                x = data.deleted;

                //prikaži poruku
                if (x == true) {
                    alert("Unos je uspešno obrisan!");
                } else {
                    alert("Greška - unos nije obrisan!");
                }

                window.location = "/app1/public";

            }
        });


    });






}