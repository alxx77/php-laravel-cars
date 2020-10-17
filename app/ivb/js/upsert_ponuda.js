/*jshint esversion: 8 */


//kad god je stranica učitana
async function SetupPage() {

    //submit dugme
    $("#submit_btn").on("click", () => {

        MainFormSubmit();

    });


    //cancel dugme
    $("#cancel_btn").on("click", () => {

        //vrati na glavnu stranicu
        window.location ="/app1/public/";

    });

    //sprečavanje podr. submit
    $('#frm_main').on("submit", (ev) => {
        ev.preventDefault();
    });


    //formati brojeva RS
    SetupNumeralJs();


    //inicijalizacija vrednosti kontrola
    await InitPage();

    //akcija
    let action = GetURLParameter("action");

    //ako je u pitanju update akcija popuni kontrole sa vrednostima ponude
    if (action == 'update') {
        await FillValues();

    }

    $('html').removeClass("wait");
}

//submit handler
async function MainFormSubmit() {

    //kontejner za podatke
    let formData = new FormData();

    //tip obrade
    formData.append('action', GetURLParameter("action"));

    //id ponude
    formData.append('id_pon', GetURLParameter("id_pon"));


    //podaci iz forme

    //vrsta vozila
    //je određena kroz model

    //id brand
    formData.append('id_brand', $("#list_brand").val());

    //id modela
    formData.append('id_model', $("#list_model").val());

    //tip (text)
    formData.append('tip', $("#tip").val());

    //godina proizvodnje
    formData.append('god_proizv', parseInt(($("#god_proizv_lista").val())));

    //tip karoserije
    formData.append('a000_karoserija', $("#tipovi_karoserije_lista").val());

    //vrsta goriva
    formData.append('a002_tip_goriva', $("#vrste_goriva_lista").val());


    //kilometraža
    formData.append('a006_km', numeral().unformat($("#a006_km").val()));

    //CCM
    formData.append('a004_ccm', numeral().unformat($("#a004_ccm").val()));

    //snaga
    formData.append('a005_kw', numeral().unformat($("#a005_kw").val()));

    //datum ponude
    let d1 = $("#datetimepicker1").data("DateTimePicker").date();

    if (d1 != null) {
        formData.append('dat_pon', d1.format("YYYY-MM-DD HH:mm:ss"));
    }

    //datum zatvaranja ponude
    let d2 = $("#datetimepicker2").data("DateTimePicker").date();

    if (d2 != null) {
        formData.append('dat_zatv', d2.format("YYYY-MM-DD HH:mm:ss"));
    }

    //provera iznosa

    bc = numeral().unformat($("#bruto_cena_val").val());
    nc = numeral().unformat($("#neto_cena_val").val());

    //bruto cena mora biti veća od 0 i manja od 1000000
    if (((bc) <= 0) || ((bc) > 1000000)) {
        alert('Nedozvoljena bruto cena...!');
        return;
    }

    //isto važi i za neto cenu
    if (((nc) <= 0) || ((nc) > 1000000)) {
        alert('Nedozvoljena neto cena...!');
        return;
    }

    //neto cena ne sme biti veća od bruto cene
    if ((nc - bc) > 0) {
        alert('Neto cena ne može biti veća od bruto cene...!');
        return;
    }

    //bruto cena
    formData.append('bruto_cena_val', bc);

    //neto cena
    formData.append('neto_cena_val', nc);

    //pdv
    formData.append('pdv_cena_val', bc - nc);

    //mogućnost korišćenja PDV-a
    formData.append('vat_ddct', $("#vat_ddct_chkbox").prop('checked'));

    //opis
    formData.append('opis', $("#text_descr").val());

    //dodaj sve slike

    //prvo nađi redosled html elmenata koji se bave uploadom podataka
    //pošto će to biti redosled kojim će se određivati redosled uploada slika

    //svi elementi kontrole slika
    let html_elems = document.getElementById("image_control_group_row").children;

    //brojač
    let counter = 0;

    //let images=[];

    //prolazak kroz kolekciju
    for (let item of html_elems) {

        //nađi identifikator grupe
        const uuid = $(item).attr("id").substring(4);

        //nađi sliku koja pripada grupi
        //slika je prilikom uploada skalirana na 800px širine
        //i pohranjena u listu u obliku blob-a
        let res = image_blobs.filter(x => {
            return (x.uuid === uuid);
        });

        //blob
        let b = res[0].blob;

        //dodaj sliku u podatke za upload
        formData.append("img-" + counter, b, "img-" + uuid);

        //uvećaj brojač
        counter++;

    }


    //ajax poziv
    let x = await $.ajax({
        method: "POST",
        url: "./api/ponuda/upsert_ponuda",
        data: formData,
        cache: false,
        async: true,
        processData: false,
        contentType: false
    });

    //reuzltat
    if (x.success == true) {
        alert("Upis uspešan...!");

        //vrati na glavnu stranicu
        window.location ="/app1/public/";

    } else {
        alert("Greška: " + x.error);
    }

}



let image_blobs = [];
let fileReader = new FileReader();
let filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;


let loadImageFile = function (group_uuid) {

    //odgovarajući element
    let uploadImage = document.getElementById("load_img_btn_" + group_uuid);

    //provera fajlova
    if (uploadImage.files.length === 0) {
        return;
    }

    //da li je slika
    let uploadFile = uploadImage.files[0];
    if (!filterType.test(uploadFile.type)) {
        alert("Izabrani fajl nije slika...!!");
        return;
    }

    //postavljanje funkcije koja vrši resize slike pri uploadu
    fileReader.onload = event => {

        //novi image objekat
        let img = new Image();

        img.onload = event => {

            let canvas = document.createElement("canvas");

            let w = 800;

            let f = (img.naturalWidth / 800);

            let h = img.naturalHeight / f;

            canvas.width = w;
            canvas.height = h;

            let ctx = canvas.getContext("2d");

            ctx.drawImage(img,
                0,
                0,
                w,
                h
            );

            let resized_image = canvas.toDataURL("image/jpeg", 1);

            //postavi preview 
            document.getElementById("img_prev_" + group_uuid).src = resized_image;

            //podela dataURL na delove
            let block = resized_image.split(";");

            //tip sadržaja  
            let contentType = block[0].split(":")[1];

            //podaci base64string
            let realData = block[1].split(",")[1];

            //konverzija u blob objekt kako bi se slika mogla uploadovati
            let blob = b64toBlob(realData, contentType);

            //proveri da li možda postoji slika sa istim uuid-om (moguće ukoliko se više puta biraju različite slike)
            //ako postoji ukloni unos iz liste
            //kako bi se mogao dodati novi unos za isti uuid 
            image_blobs.forEach((val, idx, arr) => {
                if (val.uuid === group_uuid) {
                    image_blobs.splice(idx, 1);
                }

            });

            //sačuvaj blob
            image_blobs.push({
                "uuid": group_uuid,
                "blob": blob
            });

        };

        //postavi podatke
        img.src = event.target.result;

    };

    //pročitaj fajl
    fileReader.readAsDataURL(uploadFile);
};





//dodavanje grupe za upload slike
function AddImageControlGroup() {

    //broj grupa
    let group_number = document.getElementById("image_control_group_row").children.length;

    //uvećaj za 1
    group_number++;

    //ako nije od 1 do 5 izađi
    if (!(group_number >= 1 && group_number <= 5)) {
        return;
    }

    let uuid = uuidv4();

    //html kod grupe
    let htmlstring = `
    <div class="row" id="icg_${uuid}" style="margin-bottom: 10px;">

    <!-- dugme za upload slike -->

    <div class="col-md-4" style="padding-top: 5px;">
        <button id="del_pic_btn_${uuid}" onclick="RemoveImageGroup('${uuid}')" class="btn btn-default btn-file pull-right delete-btn-style">&nbsp;X&nbsp;</button>
        <span class="btn btn-default btn-file pull-right"><input type="file" id="load_img_btn_${uuid}"
                onchange="loadImageFile('${uuid}');" accept="image/*">&nbsp;Učitaj
            sliku...&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-picture"></span>
        </span>
 
    </div>
    <!-- preview slike -->
    <div class="col-md-4">
        <img id="img_prev_${uuid}" class="img-responsive img-rounded" style="border:1px solid #f2f2f2"
            src="/app1/app/ivb/resources/image_holder.jpg">
    </div>


</div> `;


    //dodaj u dokument
    $("#image_control_group_row").append(htmlstring);

    return uuid;
}

//ukloni grupu
function RemoveImageGroup(group_uuid) {


    //ukloni iz DOM-a
    $("#icg_" + group_uuid).remove();

    //ukloni blob iz liste
    image_blobs.forEach((val, idx, arr) => {

        if (val.uuid === group_uuid) {
            image_blobs.splice(idx, 1);
        }

    });

}