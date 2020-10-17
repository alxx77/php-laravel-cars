/*jshint esversion: 8 */

//postavljanje inicijalnih vrednosti liste brendova
async function InitBrandList() {

    //učitaj podatke
    let data = await GetBrands();

    let items="";

    //unos u listu
    data.forEach(element => {
        items += "<option value='" + element.id_brand + "'>" + element.brand + "</option>";
    });

    //postavi opcije u stranicu
    $("#list_brand").html(items);

    //postavi početni unos na listu
    $("#list_brand").val(data[0].id_brand);


}

//postavljanje inicijalnih vrednosti liste modela
async function InitModelList() {

    let data = await GetModels($("#list_brand").val(), $("#lista_vrsta_vozila").val());

    let items="";

    //unos u listu
    data.forEach(element => {
        items += "<option value='" + element.id_model + "'>" + element.model + "</option>";
    });

    //postavi opcije u stranicu
    $("#list_model").html(items);


}


//osveži tipove kaorserije
async function InitTipoviKaroserijeList() {

    let data = await
    $.ajax({
        method: "POST",
        url: "./api/ui/get_tipovi_karoserije_data",
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",

    });

    let items = '';

    data.forEach(element => {
        items += "<option value='" + element.kod + "'>" + element.naziv + "</option>";
    });

    //postavi opcije u stranicu
    $("#tipovi_karoserije_lista").html(items);
    $("#tipovi_karoserije_lista").val('001');

}


//postavi vrste goriva
async function InitVrsteGorivaList() {

    let data = await
    $.ajax({
        method: "POST",
        url: "./api/ui/get_vrsta_goriva_data",
        data: {
            "func": "get_vrste_goriva_data"
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    });

    let items = '';

    data.forEach(element => {
        items += "<option value='" + element.kod + "'>" + element.naziv + "</option>";
    });

    //postavi opcije u stranicu
    $("#vrste_goriva_lista").html(items);
    $("#vrste_goriva_lista").val('001');


}




//postavi vrstu vozila
async function PostaviVrstuVozila() {

    let data = await
    $.ajax({
        method: "POST",
        url: "./api/ui/get_vrsta_vozila_data",
        cache: false,
        async: true,
        dataType: "json",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });


    let items = '';

    //unos u listu
    data.forEach(element => {
        items += "<option value='" + element.kod + "'>" + element.naziv + "</option>";
    });

    //postavi opcije u stranicu
    $("#lista_vrsta_vozila").html(items);

    //postavi automobile kao početne
    $("#lista_vrsta_vozila").val('001');
}

