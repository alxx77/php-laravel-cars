/*jshint esversion: 8 */

//popunjavanje dropdown liste sa brendovima
async function LoadBrandDropDownList() {

    //postavi drop down listu za brend

    //učitaj podatke o brenodvima
    let data = await GetBrands();

    let items="";

    //unos za sve proizvođače
    items += "<option value=0>Svi proizvođači</option>";

    data.forEach(element => {
        items += "<option value='" + element.id_brand + "'>" + element.brand + "</option>";
    });

    //postavi opcije u stranicu
    $("#list_brand").html(items);

    //postavi brend sa najmanjim id-jem kao početni
    $("#list_brand").val(0);

}


//vraća broj aktivnih ponuda
async function GetPonudaCount(id_brand, active_only) {

    let x = await

    $.ajax({
        url: "./api/ponuda/get_ponuda_count",
        type: "POST",
        data: {
            "id_brand": id_brand,
            "active_only": active_only
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });

    return x;
}

//vraća podatke o stranicama cms
async function GetCMSPagesInfo(rootx) {

    let x = await

    $.ajax({
        url: "./api/cms/get_cms_pages_info_by_root",
        type: "POST",
        data: {
            "rootx": rootx
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });

    return x;
}


//vraća id-jeve 1 stranice podataka za listu
async function GetPonudaPage(index_start, page_size, id_brand, active_only) {

    let x = await
    $.ajax({
        url: "./api/ponuda/get_ponuda_page",
        type: "POST",
        data: {
            "index_start": index_start,
            "page_size": page_size,
            "id_brand": id_brand,
            "active_only": active_only
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    });

    return x;
}

//vraća redni broj stranice za datu ponudu
async function GetPageByIdPon(id_pon, page_size,id_brand,active_only) {

    let x = await
    $.ajax({
        url: "./api/ponuda/get_page_by_id_pon",
        type: "POST",
        data: {
            "id_pon": id_pon,
            "page_size": page_size,
            "id_brand": id_brand,
            "active_only": active_only
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    });

    return x;
}

//vraća listu komentara za ponudu
async function GetKomentariByIdPon(id_pon) {

    let x = await
    $.ajax({
        url: "./api/komentar/get_komentari_by_id_pon",
        type: "POST",
        data: {
            "id_pon": id_pon
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    });

    return x;
}

//upis komentara
async function InsertKomentar(formData) {

    let x = await
    $.ajax({
        url: "./api/komentar/insert_komentar",
        type: "POST",
        data: formData,
        cache: false,
        async: true,
        processData: false,
        contentType: false
    });

    return x;
}

//čuva veličinu stranice prikaza na serveru
async function SetListViewPageSize(page_size) {

    let x=await
    $.ajax({
        url: "./api/set_list_view_page_size",
        type: "POST",
        data: {
            "page_size": page_size
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });

    return x;

}

//update pregleda
function UpdateStats(id_stat_cntr,idxx){
    $.ajax({
        url: "./api/update_view_stats",
        type: "POST",
        data: {
            "id_stat_cntr": id_stat_cntr,
            "idxx": idxx,
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });
}