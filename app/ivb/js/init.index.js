/*jshint esversion: 8 */

function InitPaginationControls() {

    //prva stranica
    $(".first_page").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();

        await ShowFirstPage();

    });

    //prethodna stranica
    $(".prev_page").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();

        await ShowPreviousPage();

    });

    //sledeća stranica
    $(".next_page").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();

        await ShowNextPage();

    });

    $(".last_page").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();

        await ShowLastPage();

    });



    //virtuelne stranice 1-5
    $(".p1").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();


        await ShowVPage($("#p1").text());
    });

    $(".p2").on("click", async (event) => {

        window.scrollTo(0, 500);

         event.stopPropagation();
        event.preventDefault();

        await ShowVPage($("#p2").text());
    });

    $(".p3").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();

        await ShowVPage($("#p3").text());

    });

    $(".p4").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();

        await ShowVPage($("#p4").text());

    });

    $(".p5").on("click", async (event) => {

        window.scrollTo(0, 500);

        event.stopPropagation();
        event.preventDefault();

        await ShowVPage($("#p5").text());
    });




    //page size
    $("#page_size_2").on("click", async (event) => {

        event.stopPropagation();
        event.preventDefault();

        await ChangePageSize(2);

    });

    $("#page_size_3").on("click", async (event) => {

        event.stopPropagation();
        event.preventDefault();

        await ChangePageSize(3);

    });

    $("#page_size_5").on("click", async (event) => {

        event.stopPropagation();
        event.preventDefault();

        await ChangePageSize(5);

    });



}