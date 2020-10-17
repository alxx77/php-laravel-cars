/*jshint esversion: 8 */

//prikaz vozila
async function SetupPage() {

    //osveži listu brendova na tabu brendovi i modeli
    await SetBrandList();

    //lista tipova vozila
    await SetVehiclesType();

    //osveži modele
    await SetModelsList($('#model_list_brand').val(), $('#lista_vrsta_vozila').val());

    //postavi change event handler za listu brendova
    //pošto se za svaku izmenu brenda mora izmeniti i lista modela
    $('#model_list_brand').change(async function () {
        //uradi update liste modela
        await SetModelsList($("#model_list_brand").val(), $("#lista_vrsta_vozila").val());
    });

    //potrebno je postaviti change event handler za tip vozila
    $('#lista_vrsta_vozila').change(async function () {

        //uradi update liste modela
        await SetModelsList($("#model_list_brand").val(), $("#lista_vrsta_vozila").val());

    });

    RefreshCMSDataGrid();


    //submit forme 1
    $("#frm_main").on("submit", async function (e) {

        //spreči deafult akciju i event bubbling
        e.stopPropagation();
        e.preventDefault();

        //kontejner za podatke
        var formData = new FormData();

        //podaci iz forme
        formData.append('brand', $('#brand').val());

        let x = await
        $.ajax({
            method: "POST",
            url: "./api/ui/insert_brand",
            data: formData,
            cache: false,
            async: true,
            processData: false,
            contentType: false //mora se sprečiti izmena tipa podataka 
        });

        //prikaži poruku
        if (x.success == true) {
            alert("Upis uspešan...!");
        } else {
            alert("Upis neuspešan..!");
        }

        //osveži listu
        await SetBrandList();

        $('#brand').val('');

        await RefreshModelsTab();



    });


    //submit forme 2
    $("#frm_model").on("submit", async function (e) {

        //zaustavi sve...
        e.stopPropagation();
        e.preventDefault();

        //kontejner za podatke
        var formData = new FormData();

        //podaci iz forme
        formData.append('model', $('#model').val());

        formData.append('id_brand', $('#model_list_brand').val());

        formData.append('tip_vozila', $('#lista_vrsta_vozila').val());

        let x = await
        $.ajax({
            method: "POST",
            url: "./api/ui/insert_model",
            data: formData,
            cache: false,
            async: true,
            processData: false,
            contentType: false
        });

        //prikaži poruku
        if (x.success == true) {
            alert("Upis uspešan...!");
        } else {
            alert("Upis neuspešan..!");
        }

        await RefreshModelsTab();

    });

}

async function RefreshModelsTab() {
    //osveži modele
    await SetModelsList($('#model_list_brand').val(), $('#lista_vrsta_vozila').val());

    //obriši unos
    $('#model').val('');

}

//postavi listu brendova
//na tabu za unos brendova
async function SetBrandList() {


    //postavi drop down listu za brend
    let data = await
    $.ajax({
        method: "POST",
        url: "./api/ui/get_brand_data",
        cache: false,
        async: true,
        dataType: "json",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",

    });


    var items = '';
    var id_min = 1000000;
    //prolazak kroz JSON objekt
    $.each(data, function (index) {
        //red za 1 opciju
        items += "<option value='" + data[index].id_brand + "'>" + data[index].brand + "</option>";
        if (data[index].id_brand < id_min) {
            id_min = data[index].id_brand;
        }
    });

    //tab sa brendovima
    //postavi opcije u stranicu
    $("#list_brand").html(items);

    //postavi brend sa najmanjim id-jem kao početni
    $("#list_brand").val(id_min);

    //tab sa modelima
    //postavi opcije u stranicu
    $("#model_list_brand").html(items);

    //postavi brend sa najmanjim id-jem kao početni
    $("#model_list_brand").val(id_min);

}



//osveži listu modela
//mora se navesti brend i tip vozila koja se traži (automobil, kamion itd)
async function SetModelsList(id_brand, tip_vozila) {

    //postavi modele za brend
    let data = await
    $.ajax({
        method: "POST",
        url: "./api/ui/get_model_data",
        data: {
            "id_brand": id_brand,
            "tip_vozila": tip_vozila
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"

    });


    var items = '';
    //prolazak kroz JSON objekt
    $.each(data, function (index) {
        //red za 1 opciju
        items += "<option value='" + data[index].id_model + "'>" + data[index].model + "</option>";
    });
    //postavi opcije u stranicu
    $("#list_model").html(items);


}

//postavi vrstu vozila
async function SetVehiclesType() {

    let data = await
    $.ajax({
        method: "POST",
        url: "./api/ui/get_vrsta_vozila_data",
        cache: false,
        async: true,
        dataType: "json",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",

    });

    var items = '';

    //prolazak kroz JSON objekt
    $.each(data, function (index) {
        //red za 1 opciju
        items += "<option value='" + data[index].kod + "'>" + data[index].naziv + "</option>";
    });
    //postavi opcije u stranicu
    $("#lista_vrsta_vozila").html(items);

    //postavi automobile kao početne
    $("#lista_vrsta_vozila").val('001');


}

//osvežava data grid
function RefreshCMSDataGrid() {
    $.ajax({
        method: "POST",
        url: "./api/cms/get_cms_pages_info_by_root",
        data: {
            "rootx": "usluge"
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        success: function (data) {
            var items = '';

            items += `<table class="table">`;
            items += `<thead><tr>
                                <th scope="col">Id</th>
                                <th scope="col">Adresa</th>
                                <th scope="col">Opis</th>
                                <th scope="col">Vrsta unosa</th>
                                <th scope="col">Nivo podmenija</th>
                                <th scope="col">Redni br.</th>
                                <th scope="col">&nbsp;</th>
                                <th scope="col">&nbsp;</th>
                        </tr></thead>`;

            //prolazak kroz JSON objekt
            $.each(data, function (index) {

                //1 red
                items += '<tbody>';

                if (data[index].level === "0") {
                    items += '<tr><th scope="row" style="color:red;">' + data[index].id_page + '</th>';
                } else {
                    items += '<tr><th scope="row">' + data[index].id_page + '</th>';
                }


                if (data[index].node_type === "0") {
                    items += '<td style="color:blue;">' + data[index].pathx + '</td>';
                    items += '<td style="color:blue;">' + data[index].descr + '</td>';
                    items += '<td style="color:blue;">' + (data[index].node_type === "0" ? "podmeni" : "stranica") + '</td>';
                } else {
                    items += '<td>' + data[index].pathx + '</td>';
                    items += '<td>' + data[index].descr + '</td>';
                    items += '<td>' + (data[index].node_type === "0" ? "podmeni" : "stranica") + '</td>';
                }


                items += '<td>' + data[index].level + '</td>';
                items += '<td>' + data[index].idx + '</td>';

                if (data[index].node_type === "1") {

                    items += `<td><button type="button" onclick="EditPage('${data[index].id_page}')" class="btn btn-xs">Uredi stranicu</button></td>`;
                } else {
                    items += `<td><button type="button" onclick="" style="visibility:hidden;" class="btn btn-xs">Uredi stranicu</button></td>`;
                }

                if (data[index].level === "0") {

                } else {
                    if (data[index].node_type === "1") {

                        items += `<td><button type="button" onclick="DeleteItem('${data[index].id_page}')" class="btn btn-xs btn-default">Obriši stranicu</button></td></tr>`;
                    } else {
                        items += `<td><button type="button" onclick="DeleteItem('${data[index].id_page}')" class="btn btn-xs btn-warning">Obriši podmeni</button></td></tr>`;
                    }
                }


            });

            items += '</tbody></table>';

            //postavi opcije u stranicu
            $("#cms_pages").html(items);

        }
    });
}

//edit
function EditPage(id_page) {
    window.location = `/app1/public/cms_editor?id_page=${parseInt(id_page)}`;
}

async function DeleteItem(id_page) {

    if (!confirm("Obriši stavku...?")) return;

    let x = await
    $.ajax({
        method: "POST",
        url: "./api/cms/delete_cms_page",
        data: {
            "id_page": id_page
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    });

    RefreshCMSDataGrid();
}
