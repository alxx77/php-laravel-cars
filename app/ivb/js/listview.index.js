/*jshint esversion: 8 */


//osvežavanje ispisa liste vozila
async function RenderListView() {

    //postavi oznake paginacije
    await RenderPaginationValues();

    await RenderListViewPage(GetCurrentPage(), GetPageSize(), GetBrandFilterValue(), GetShowActiveElementsOnly());

}


//generisanje liste ponuda za datu stranu i njihovo injektovanje u DOM
async function RenderListViewPage(current_page, page_size, id_brand, active_only) {

    //proveri id_brand
    if (id_brand == undefined) {
        id_brand = 0;
    }

    //generiši početni indeks strane
    //jednak je broj strane -1
    let index_start = (current_page - 1) * page_size;


    //id-jevi elemenata na stranici
    let data = await GetPonudaPage(index_start, page_size, id_brand, active_only);

    //elementi
    let list_view_elements = document.createDocumentFragment();

    //asinhrona for-each petlja
    await asyncForEach(data, async (x) => {

        //formiranje html fragmenta
        list_view_elements.appendChild(await RenderListViewPageElement(x.id_pon));
    });

    //nađi element listu ponuda
    let l = document.getElementById("lista_ponuda");

    //ako ima elemenata, obriši ih
    while (l.children.length > 0) {
        l.firstChild.remove();
    }

    //dodaj novogenerisane elemente
    l.append(list_view_elements);

    //postavi slike
    await asyncForEach(data, async (x) => {

        let img = document.getElementById("img-" + x.id_pon);
        let img_link = await GetImageLink(x.id_pon);

        if (img_link) {
            img.src = img_link;
        }

    });

}



//podrazumevana slika ponude (trebala bi da bude slika sa img_num=0)
async function GetImageLink(id_pon) {

    //nađi id naslovne slike ponude
    let image_id = await GetDefaultImage(id_pon);

    let img_src = null;

    //ako slika postoji
    if (image_id > 0) {

        //učitaj podatke o slici
        let image_data = await GetImageData(image_id);

        //html element slike
        img_src = './api/images/get_image/' + image_data.image_id;
    }

    return img_src;

}



//generisanje elementa liste
async function RenderListViewPageElement(id_pon) {

    //učitaj podatke za datu ponudu (id)
    let ponuda = await GetPonudaData(id_pon);

    //izlaz
    let frame = document.createElement("div");

    frame.setAttribute("id", "frame-" + id_pon);

    frame.className = " panel";
    frame.className += " panel-default";
    frame.className += " col-md-12";

    frame.style.height = "100%";

    //postavi id ponude u data atribut
    frame.dataset.id_pon = id_pon;

    //************************************ */

    //1 red
    let row1 = document.createElement("div");

    row1.setAttribute("id", "row_1");
    row1.className = "row";

    //dodaj u izlaz
    frame.appendChild(row1);

    //2 red
    let row2 = document.createElement("div");

    row2.setAttribute("id", "row_2");
    row2.className = "row";

    frame.appendChild(row2);



    //levi panel 1 reda (slika)
    let d1 = document.createElement("div");

    d1.className = "panel-body";
    d1.className += " col-md-8";

    row1.appendChild(d1);


    //desni panel 1 reda
    let d2 = document.createElement("div");

    d2.className = "panel-body";
    d2.className += " col-md-4";

    row1.appendChild(d2);


    //levi el. 2 reda
    let d3 = document.createElement("div");

    d3.className = "col-md-8";

    row2.appendChild(d3);

    //desni panel 2 reda
    let d4 = document.createElement("div");

    d4.className = "col-md-4";

    row2.appendChild(d4);

    //************************************************* */

    //naslovna slika (link)

    let a1 = document.createElement("a");
    a1.href = "";
    d1.appendChild(a1);

    //slika
    let img = document.createElement("img");
    img.setAttribute("id", "img-" + id_pon);
    img.className = "img-responsive";
    img.className += " img-rounded";
    img.style.width = "100%";
    img.style.border = "1px solid #f3f3f3";
    img.src = "/app1/app/ivb/resources/no_img.jpg";

    if (img.src) {
        a1.href = `javascript:ShowImageGallery(${id_pon});`;
    }

    //postavljanje ispravne visine elementa sa opisom
    img.onload = ev => {

        //nađi frame element i njegov dataset
        let id_pon = ev.target.id.substring(4);

        //ako postoji
        if (id_pon) {

            //nađi element sa opisom
            let descr = document.getElementById("descr-" + id_pon);

            //ako postoji
            if (descr) {

                let frame = document.getElementById("frame-" + id_pon);

                try {

                    let positionInfo = frame.getBoundingClientRect();
                    let height = positionInfo.height;

                    let h = height - 178;

                    descr.style.height = `${h}px`;

                } catch (error) {

                }
            }
        }
    };

    //dodavanje slike u link
    a1.appendChild(img);

    //tip vozila
    let ponuda_tip = (ponuda.tip == null || ponuda.tip == '') ? '' : ' ' + ponuda.tip;

    //info o vozilu
    let info_txt = `${ponuda.brand} ${ponuda.model} ${ponuda_tip}, ${ponuda.god_proizv} god, ${ponuda.a006_km} km`;


    let info_html = document.createElement("h4");

    info_html.style.marginTop = "0px";

    info_html.style.alignItems = "center";

    info_html.innerText = info_txt;

    d3.appendChild(info_html);


    //dugmad za editovanje ponude

    //edit link
    //ako je admin ulogovan dodaj dugmad
    if (admin_logged_in) {
        let a1 = document.createElement("a");
        a1.href = `/app1/public/edit?action=update&id_pon=${ponuda.id_pon}`;
        a1.className = "btn";
        a1.className += " btn-xs";
        a1.className += " btn-info";
        a1.className += " pull-right";
        a1.innerHTML = `&nbsp;&nbsp;Uredi...&nbsp;&nbsp;`;

        let a2 = document.createElement("a");
        a2.href = `/app1/public/delete?id_pon=${ponuda.id_pon}`;
        a2.className = "btn";
        a2.className += " btn-xs";
        a2.className += " btn-warning";
        a2.className += " pull-right";
        a2.innerHTML = `&nbsp;&nbsp;Obriši...&nbsp;&nbsp;`;

        info_html.appendChild(a1);
        info_html.appendChild(a2);
    }


    //broj ponude
    let broj_ponude_html = document.createElement("h6");
    broj_ponude_html.style.marginTop = "5px";
    broj_ponude_html.style.marginBottom = "0px";
    broj_ponude_html.style.fontStyle = "italic";
    broj_ponude_html.style.color = "blue";
    broj_ponude_html.innerText = `Broj: ${ponuda.brpon}`;

    //dodatj u parent
    d2.appendChild(broj_ponude_html);

    //cena
    let cena_html = document.createElement("h3");
    cena_html.style.marginTop = "10px";
    cena_html.style.marginBottom = "10px";

    if (ponuda.vat_ddct == false) {
        cena_html.innerText = numeral(parseFloat(ponuda.bruto_cena_val)).format("0,0.00") + ' EUR';
    } else {
        //navedi neto cenu
        cena_html.innerText = numeral(parseFloat(ponuda.neto_cena_val)).format("0,0.00") + ' EUR - bez PDV-a';
    }

    //dodaj cenu
    d2.appendChild(cena_html);


    //komentar
    //slika - link
    let a2 = document.createElement("a");
    d2.appendChild(a2);

    let img2 = document.createElement("img");

    img2.dataset.id_pon = id_pon;

    img2.style.width = "40px";
    img2.style.marginBottom = "12px";
    img2.src = "/app1/app/ivb/resources/comment.png";

    //kursor
    img2.onmouseover = ev => {
        img2.style.cursor = "pointer";
    };

    img2.onmouseleave = ev => {
        img2.style.cursor = "default";
    };


    //klik na komentar
    //prikazivanje modalne forme sa komentarima
    img2.onclick = async ev => {
        //proveri da li je korisnik ulogovan
        if (auth_user != null) {

            //postavi id ponude na formu
            document.getElementById("comments_form").dataset.id_pon = ev.target.dataset.id_pon;

            //osveži komentare
            await RefreshComments();

            //prikaži formu
            $("#comments_form").modal();

            let objDiv = document.getElementById("comment_modal_body");
            objDiv.scrollTop = objDiv.scrollHeight;

        } else {
            //inače obaveštenje
            alert("Samo ulogovani korisnici mogu pisati komentare..!");
        }
    };

    //dodaj sliku u link
    a2.appendChild(img2);


    //opis vozila
    let opis_html = document.createElement("div");
    opis_html.setAttribute("id", "descr-" + id_pon);
    opis_html.className = "panel-body";
    opis_html.className += " col-md-4";
    opis_html.style.height = "300px";
    opis_html.style.width = "100%";
    opis_html.style.overflowX = "auto";
    opis_html.style.border = "1px solid #f3f3f3";

    //ako postoji tekst
    //zameni sve krajeve linija sa <br> elementom
    //zbog ispravnog ispisa kraja reda
    if (ponuda.opis !== undefined) {
        opis_html.innerHTML = (ponuda.opis.replace(/\n/g, '<br>'));
    }

    //dodaj u parent element
    d2.appendChild(opis_html);

    //vrati element
    return frame;
}


//osveži koemntare
async function RefreshComments() {

    let id_pon = document.getElementById("comments_form").dataset.id_pon;

    //generiši elemente
    let list_el = await GetCommentList(id_pon);

    //element sa listom komentara
    let l = document.getElementById("lista_komentara");

    //ako ima elemenata, obriši ih
    while (l.children.length > 0) {
        l.firstChild.remove();
    }

    //dodaj novogenerisane elemente
    l.append(list_el);

}

//lista elemenata sa komentarima
async function GetCommentList(id_pon) {

    //lista komentara za ponudu
    let komentari = await GetKomentariByIdPon(id_pon);

    //izlaz
    let fr = document.createDocumentFragment();

    //prođi kroz sve komentare
    komentari.forEach(element => {

        //novi element
        let frame = document.createElement("div");

        frame.setAttribute("id", "frame_comment-" + element.id_kom);

        frame.className = " panel-default";

        frame.style.minHeight = "40px";
        frame.style.marginBottom = "7px";


        fr.appendChild(frame);

        //naslov
        let x = document.createElement("h5");
        x.style.marginBottom = "0px";

        let p1 = document.createElement("h5");

        p1.style.fontWeight = "bold";
        p1.style.display = "inline";

        p1.innerHTML = element.username + "&nbsp;&nbsp;&nbsp;";

        x.appendChild(p1);

        p1 = document.createElement("h6");

        p1.style.display = "inline";

        let d = new Date(element.dat_kom.date);

        p1.innerText = moment(d).format("DD.MM.YYYY - HH:mm:ss");
        p1.style.fontStyle = "italic";
        p1.style.color = "blue";

        x.appendChild(p1);

        frame.appendChild(x);

        let p2 = document.createElement("div");
        p2.className = " panel";
        p2.className += " panel-default";
        p2.style.minHeight = "40px";
        p2.style.maxHeight = "170px";
        p2.style.marginBottom = "0px";
        p2.style.overflowX = "auto";
        p2.style.backgroundColor = "#e6e6ff";

        s = element.komentar.replace(/\n/g, '<br>');

        p2.innerHTML = s;

        frame.appendChild(p2);

    });

    return fr;
}