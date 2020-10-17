/*jshint esversion: 8 */

//vraća podatke o brendovima
async function GetBrands() {

    let data = await
    $.ajax({
        method: "POST",
        url: "./api/ui/get_brand_data",
        cache: false,
        async: true,
        dataType: "json",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });

    return data;
}


//vraća podatke o modelima
async function GetModels(id_brand,tip_vozila) {

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

    return data;
}

//vraća id podrazumevane (naslovne) slike za ponudu
async function GetDefaultImage(id_pon) {

    let x=await

    $.ajax({
        url: "./api/images/get_default_image_id_by_id_pon",
        type: "POST",
        data: {"id_pon":id_pon},
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });

    return x;
}


//vraća trenutno ulogovanog korisnika
async function GetAuthUser() {
    let x=await
    $.ajax({
        url: "./api/users/get_auth_user",
        cache: false,
        async: true,
        type: "POST",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
        });

    return x;
}

//vraća podatke za željenog korisnika
async function GetUser(user_id) {
    let x=await
    $.ajax({
        url: "./api/users/get_user_by_id",
        cache: false,
        async: true,
        data: {"user_id":user_id},
        type: "POST",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });

    return x;
}

//podaci o ponudi
async function GetPonudaData(id_pon) {
    let x=await
    $.ajax({
        url: "./api/ponuda/get_ponuda_data",
        cache: false,
        async: true,
        data: {"id_pon":id_pon},
        type: "POST",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",

    });

    return x;
}

//vraća podatke o slici
async function GetImageData(image_id) {
    let x=await
    $.ajax({
        url: "./api/images/get_image_data",
        type: "POST",
        data: {"image_id":image_id},
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"

    });

    return x;

}

//vraća info o svim slikama za datu ponudu 
async function GetImagesInfo(id_pon) {

    let x = await

    $.ajax({
        url: "./api/images/get_images_data_by_id_pon",
        type: "POST",
        data: {
            "id_pon": id_pon
        },
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8"
    });

    return x;
}