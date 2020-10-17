/*jshint esversion: 8 */

//da li je administrator ulogovan
let admin_logged_in = false;

//autentifikovani korisnik
let auth_user = null;

//generisanje stranice
async function SetupPage() {


    //podešavanja kontrola paginacije
    InitPaginationControls();


    //podesi numeral.ja biblioteku za ispravan format ispisa brojeva
    SetupNumeralJs();


    //trenutno logovan korisnik - pohranjuje se u globalnoj promenljivoj auth_user
    auth_user = await GetAuthUser();

    //da li je trenutno ulogovani korisnik administrator

    if (auth_user != undefined) {
        admin_logged_in = auth_user.user_type_name == "Admin";
    }

    //u zavisnosti od toga da li je neko ulogovan ili ne postavi odgovarajući link
    //postaviti link na 'logout'

    //ako niko nije ulogovan
    if (auth_user.length == 0) {
        //prikaži link za log-in
        $('#nav1').append('<li id="login" ><a href="/app1/public/login"><span class="glyphicon glyphicon-log-in"></span> Prijava</a></li>');

        //prikaži link za registraciju
        $('#nav1').append('<li id="register" ><a href="/app1/public/register"><span class="glyphicon glyphicon-user"></span> Registruj se</a></li>');

        //ako niko nije ulogovan, prikazuju se samo aktivne ponude, checkbox a skriven
        $("#active_only").attr("checked", "true");
        $("#active_only_cb").css("display", "none");

    } else {

        //ako neko jeste ulogovan
        //podešavanja strane u zavisnosti od toga
        //da li je ulogovan admin ili običan korisnik
        if (admin_logged_in == true) {
            //dugme za dodavanje nove ponude
            $("#naslov_lista").html('Vozila sa lagera:<a href="/app1/public/edit?action=insert&id_pon=-1" class="btn btn-xs btn-primary pull-right" role="button">&nbsp;&nbsp;Dodaj nov unos...&nbsp;&nbsp;</a>');

            //dodaj link za acct mgmt
            $("#nav1").append('<li id="acct_mgmt"><a href="/app1/public/acct_mgmt"><span class="glyphicon glyphicon-user"></span> Upravljanje nalozima</a></li>');
            $("#nav1").append('<li id="acct_mgmt"><a href="/app1/public/settings"><span class="glyphicon glyphicon-cog"></span> Postavke</a></li>');

            //prikaži checkbox za selekciju svih ili samo aktivnih ponuda
            $("#active_only_cb").css("visibility", "visible");

        } else {
            //prikazuju se samo aktivne ponude, a checkbox je isključen
            $("#active_only").attr("checked", "true");
            $("#active_only_cb").css("visibility", "hidden");

            //bez dugmeta za dodavanje novih vozila
            $("#naslov_lista").html('Vozila sa lagera: ');

            //bez linka za acct_mgmgt
            $("#acct_mgmt").remove();
        }

        //prikaži korisničko ime i link za logout
        $('#nav1').append('<li id="logout" ><a href="/app1/public/logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;' + auth_user.username + ' - izloguj se</a></li>');

        //ukloni link za registraciju
        $('#register').remove();
    }

    //brisanje modalne forme - galerija slika    
    $("#image_gallery").on("hidden.bs.modal", function (ev) {
        //slike
        $("#img_slider").empty();
    });

    //brisanje komentara
    $("#comments_form").on("hidden.bs.modal", function (ev) {

        //slike
        $("#lista_komentara").empty();

        //komentar
        document.getElementById("komentar").value = "";

        //obriši id ponude u data atribut na comment formi
        document.getElementById("comments_form").dataset.id_pon = "";

    });

    //cancel dugme komentara
    $("#comment_cancel_btn").on("click", () => {

        $("#comments_form").modal('hide');

    });

    //submit dugme komentara
    $("#comment_submit_btn").on("click", async function (ev) {
       
        if(auth_user.user_type_name){

        await WriteComment(document.getElementById("comments_form").dataset.id_pon, document.getElementById("komentar").value);

        document.getElementById("komentar").value = "";

        await RefreshComments();

        } else {
            alert("Samo ulogovani korisnik može pisati komentar...");
        }

    });

    //postavi dropdown/subdropdown menu za navbar
    await LoadNavbarDropDownMenu();


    //navbar dropdown & subdropdown menu akcije
    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        $(this).parent().siblings().removeClass('open');
        $(this).parent().toggleClass('open');
    });


    //popuni filter za brend
    await LoadBrandDropDownList();

    //inicijalizacija stranice
    SetCurrentPage(1);

    //prikazi listu
    await RenderListView();

}


//klik na naslovnu sliku - pregled svih slika vozila
async function ShowImageGallery(id_pon) {

    //nađi sve id-jeve slika ponude
    let images = await GetImagesInfo(id_pon);

    let x = "";

    let s = "";

    let i = "";

    //elementi liste
    x += `<ul id="slider" class="rslides">`;

    //prolazak kroz listu slika
    await asyncForEach(images, async (val, idx, arr) => {

        //učitaj sliku
        let response = await fetch("./api/images/get_image/" + val.image_id.toString());

        //blob
        let blob = await response.blob();

        //postavi url u listu
        x += `<li><img src='${URL.createObjectURL(blob)}' alt="${val.image_name}"></li>`;

    });

    //završni tag
    x += `</ul>`;

    //ubaci u slajder
    $("#img_slider").append(x);

    //aktiviraj slajder
    $("#slider").responsiveSlides({
        auto: false,
        pager: true,
        nav: true,
        speed: 500,
        maxwidth: 900,
        namespace: "centered-btns"
    });

    //prikaži modalnu formu
    $("#image_gallery").modal();

    //uradi update broja pregleda
    UpdateStats(1,id_pon);

}




//tekuća stranica
function GetCurrentPage() {

    return parseInt(document.getElementById("pagination").dataset.current_page);
}

//postavi tekuću stranicu
function SetCurrentPage(x) {
    document.getElementById("pagination").dataset.current_page=x;
}

//ukupan broj stranica
function GetPageCount() {
    return parseInt(document.getElementById("pagination").dataset.page_count);
}

//postavi ukupan broj stranica
function SetPageCount(x) {
    document.getElementById("pagination").dataset.page_count=x;
}

function GetShowActiveElementsOnly() {

    return ($('#active_only').prop('checked')) == true ? 1 : 0;
}

function GetBrandFilterValue() {
    return parseInt($('#list_brand').val());
}



//broj elemenata po strani
function GetPageSize() {

    return parseInt(document.getElementById("data-page_size").dataset.page_size);

}

//upis komentara
async function WriteComment(id_pon, komentar) {

    if (komentar.trim().length === 0) {
        return;
    }

    let fd = new FormData();

    fd.append("user_id", auth_user.user_id);
    fd.append("id_pon", id_pon);
    fd.append("komentar", komentar);
    fd.append("dat_kom", moment().format("YYYY-MM-DD HH:mm:ss"));
    fd.append("proc", 1);

    await InsertKomentar(fd);

}

//formiranje dinamičkih linkova u navbaru
async function LoadNavbarDropDownMenu() {

    //učitaj podatke o stranciama
    let data = await GetCMSPagesInfo("usluge");

    //dropdown meni lokacija
    let root = document.getElementById("root_menu_navbar");

    //meni
    let s = document.createElement("div");

    data.forEach(el => {

        let arr = el.pathx.split(`/`);

        SearchAndAddNode(s, arr, el);

    });

    for (const iterator of s.children) {
        root.appendChild(iterator);
    }

}

//popunjavanje nodova menija
function SearchAndAddNode(tg, arr, el) {

    //ako nema delova adrese izađi
    if (arr.length === 0) return;
    if (tg == undefined) return;

    //novi odredišni element
    let new_target;


    //prođi kroz listu
    for (const target of tg.children) {

        let li = target.getElementsByTagName("LI")[0];

        let a = target.getElementsByTagName("A")[0];

        let ul = target.getElementsByTagName("UL");

        //ako nema UL elementa, znači da je target krajni nod (stranica) a ne link - podmeni
        //to znači da se ne proverava dalje
        if (ul.length === 0) {

        } else {

            //ako postoji UL nod to znači da je target link za podmeni
            //i da se provera nastavlja
            //ako ima poklapanja sa delom adrese
            if (ul[0].dataset.node_name === arr[0]) {

                //postavi tekući element kao novi target
                new_target = ul[0];

                //obrši pronađeni deo adrese
                arr.splice(0, 1);

                //nastavi tom stazom
                SearchAndAddNode(new_target, arr, el);

                return;
            }

        }
    }

    //ako se došlo doovde znači da nije bilo poklapanja
    //i treba dodati nod

    //novododati nod postavi kao target
    new_target = AddNode(tg, arr[0], el);

    //ukloni obrađeni unos
    arr.splice(0, 1);

    //pretraži dalje
    SearchAndAddNode(new_target, arr, el);

    return;


}


//dodaj nod
function AddNode(target, node_name, el) {

    let new_node;

    //krajnji nod              
    if (el.node_type === "1") {

        new_node = GenerateMenuElement(el.id_page, el.descr);

    } else {
        //podmeni
        new_node = GenerateSubMenuElement(node_name, el.descr, el.level);
    }

    target.appendChild(new_node);

    return new_node;

}





//generisanje linka za stranicu cms-a
function GenerateMenuElement(id, descr) {

    let li = document.createElement("li");

    let a = document.createElement("a");

    let y;

    try {
        y=parseInt(id);
    } catch (error) {
        
    }
   
    if(Number.isInteger(y)){
        //a.onclick = () => {
            //window.location = "/app1/public/show_cms_page?id_page=" + id;
        //};

        a.href = "/app1/public/show_cms_page?id_page=" + id;
    }else{
        a.href = "#";
    }

    

    a.innerText = descr;

    li.appendChild(a);

    return li;
}

//generisanje podmenija za cms
function GenerateSubMenuElement(pathx, descr, level) {

    //root element podmenija
    let li = document.createElement("li");

    //ako je u pitanju root menija
    if (level === "0") {
        //kasa je samo dropdown
        li.className = "dropdown";
    } else {
        li.className = "dropdown-submenu";
    }

    //a element podmenija
    let a = document.createElement("a");

    a.href = "#";

    a.className = "dropdown-toggle";

    a.dataset.toggle = "dropdown";


    let span = document.createElement("span");

    span.className = "nav-label";
    span.innerText = descr;
    a.appendChild(span);

    if (level === "0") {
        span = document.createElement("span");
        span.className = "caret";
        a.appendChild(span);
    }

    li.appendChild(a);


    //child lista
    let ul = document.createElement("ul");
    ul.className = "dropdown-menu";

    ul.dataset.node_name = pathx;

    li.appendChild(ul);

    return li;

}

