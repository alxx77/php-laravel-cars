/*jshint esversion: 8 */


//prikaži prvu stranicu
async function ShowFirstPage() {
    //prikaz prve stranice
    if (GetCurrentPage() > 1) {

        SetCurrentPage(1);

        //osveži prikaz liste
        await RenderListView();
    }
}


//prikaži prethodnu stranicu
async function ShowPreviousPage() {

    //ako je tekuća stranica veća od jedan
    if (GetCurrentPage() > 1) {

        //umanji tekuću stranicu za 1
        SetCurrentPage( GetCurrentPage() - 1);

        //osveži prikaz
        await RenderListView();
    }
}

//prikaži sledeću stranicu
async function ShowNextPage() {

    //ako tekuća stranica manja od poslednje
    if (GetCurrentPage() < GetPageCount()) {


        //uvećaj tekuću stranicu za 1
        SetCurrentPage(GetCurrentPage() + 1);

        //osveži prikaz
        await RenderListView();
    }

}

//prikaži poslednju stranicu
async function ShowLastPage() {

    //ako je tekuća strana manja od broja strana
    if (GetCurrentPage() < GetPageCount()) {

        //postavi poslednju za tekuću
        SetCurrentPage(GetPageCount());

        //osveži prikaz liste
        await RenderListView();
    }

}

//prikaži virtuelnu stranicu
async function ShowVPage(vpage) {

    //prikaži stranicu sa date pozicije
    //ukoliko je trenutna stranica različita od zahtevane

    //proveri da li je ulaz definisan
    if (vpage != undefined) {

        let req_page = parseInt(vpage);

        let c = GetCurrentPage();

        if (c != req_page) {

            //postavi željenu stranicu
            SetCurrentPage(req_page);

            //osveži prikaz
            await RenderListView();
        }
    }

}

//promena veličine stranice
//čuva se i na serveru i na klijentu
async function ChangePageSize(page_size){

    try {
        let x=await SetListViewPageSize(page_size);
    } catch (error) {
        throw new Error("Greška u setovanju veličine stranice...!");
    }
   
    //ako nema greške izmeni i na klijentu
    document.getElementById("data-page_size").dataset.page_size=page_size;

    //osveži prikaz
    await RenderListView();

}




//postavljanje vrednosti kontrola paginacije

async function RenderPaginationValues() {

    //segmenti strana po 5 strana
    let ret_data = await GetPageSegments();


    let segments=ret_data.segments;

    let page_count=ret_data.page_count;

    let current_page=ret_data.current_page;

    let current_segment=ret_data.current_segment;

    let page_size=ret_data.page_size;
    
    //postavi tekući segment od 5 stranica u ispis stranice
    for (let index = 1; index <= 5; index++) {
        //upis vrednosti u virtualne stranice
        let p=".p"+index.toString();

        //goranji i donji ispis paginacije
        let pg1=$(p)[0];
        let pg2=$(p)[1];

        $(pg1).text((segments[current_segment][index - 1] != undefined) ? segments[current_segment][index - 1] : '');
        $(pg2).text((segments[current_segment][index - 1] != undefined) ? segments[current_segment][index - 1] : '');

        //bold tekuće strane
        if (segments[current_segment][index - 1] == current_page) {
            $(p).css("fontWeight", "bold");
        } else {
            $(p).css("fontWeight", "normal");
        }
    }

    //označavanje veličine stranice
    [2, 3, 5].forEach(element => {
        if (page_size == element) {
            $("#page_size_" + element.toString()).css("fontWeight", "bold");
        } else {
            $("#page_size_" + element.toString()).css("fontWeight", "normal");
        }
    });



    //prikaz ukoliko postoji raspon između najmanje prikazane stranice i 1 stranice (simbol tri tačke ...) - isto tako i kod završetka segmenta

    if (parseInt($("#p1").text()) > 1) {
        $(".pagegapmin").css("display", "inline");
    } else {
        $(".pagegapmin").css("display", "none");
    }

    if (parseInt($("#p5").text()) < page_count) {
        $(".pagegapmax").css("display", "inline");
    } else {
        $(".pagegapmax").css("display", "none");
    }


}

//vraća segmente strana za prikaz u paginaciji
async function GetPageSegments(){
    //učitati broj slogova za prikaz
    let items_count = await GetPonudaCount(GetBrandFilterValue(),GetShowActiveElementsOnly());



    //broj elemenata po stranici
    let page_size = GetPageSize();

    //napravi podnizove od 5 stranica koje će se prikazivati u izboru

    //broj strana potrebnih za prikaz svih elemenata liste vozila
    let page_count = Math.ceil((items_count / page_size));

    //sačuvaj kao HTML data atribut
    SetPageCount(page_count);

    //ukupan broj potrebnih segmenata od 5 stranica
    let segment_nr = Math.ceil(page_count / 5);

    //da li je poslednji segment nepotpun (ukoliko broj strana nije deljiv sa 5 bez ostatka)
    let last_segment_inc = false;

    if ((page_count % 5) != 0) {
        last_segment_inc = true;
    }

    //tekuća stranica
    let current_page = GetCurrentPage();

    //ako je od zahteva za ispis možda došlo do izmene broja slogova
    //proveriti opseg

    //ukoliko je aktuelni broj stranica manji od tekuće stranice
    if (current_page > page_count) {

        //postavi poslednju kao tekuću stranicu
        SetCurrentPage(page_count);
        current_page = page_count;
    }


    //tekući segment
    let current_segment = 0;



    //ako nema elemenata
    if(items_count===0){
        let x = {
            "segments": [0],
            "current_page": 1,
            "page_count": 0,
            "current_segment": 0,
            "page_size":page_size
        };

        x.segments[0]=[];

        return x;
    }


    //niz za smeštanje indeksa segmenata
    let segments = [];


    //generiši segmente za ispis
    for (let i = 0; i < segment_nr; i++) {

        //novi niz
        let n = [];

        //ako je poslednji segment i nekompletan je
        if (last_segment_inc == true && i == segment_nr - 1) {

            //broj strana u poslednjem segmentu
            //jednak je ukupnom broju strana umanjenom za (ukupan broj segmenata -1)*5
            let m = page_count - (i * 5);

            //u segment se dodaju samo strane (kojih u ovom slučaju ima manje od 5 u segmentu) iz poslednjeg segmenta
            for (let j = 1; j <= m; j++) {

                let s = j + (i * 5);

                //dodaj u listu
                n.push(s);

                //ako je broj stranice jednak broju tekuće stranice (onoj koju treba prikazati)
                if (s == current_page) {
                    //sačuvaj segment
                    current_segment = i;
                }
            }


        } else {

            //stranice (5 - maksimum koji se prikazuje)
            for (let j = 1; j <= 5; j++) {

                let s = j + (i * 5);

                //dodaj u listu
                n.push(s);

                //ako je broj stranice jednak broju tekuće stranice (onoj koju treba prikazati)
                if (s == current_page) {
                    //sačuvaj segment
                    current_segment = i;
                }
            }
        }
        //dodaj niz u glavnu listu segmenata
        segments.push(n);
        }

        //prosleđivanje svih potrebnih informacija
        let x = {
            "segments": segments,
            "current_page": current_page,
            "page_count": page_count,
            "current_segment": current_segment,
            "page_size":page_size
        };

        return x;

}