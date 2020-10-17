/*jshint esversion: 8 */

async function SetupPage(){

        let id = GetURLParameter("id_page");

        let x = await
        $.ajax({
            url: "./api/cms/get_cms_page_data",
            type: "POST",
            data: {
                "id_page": id
            },
            cache: false,
            async: true,
            contentType: "application/x-www-form-urlencoded;charset=UTF-8"
        });

        if(x[0].page_data==null){
            x[0].page_data='';
        }

        let html1 = he.decode(x[0].page_data);

        CKEDITOR.instances.editor1.setData(html1);


}

function resizeEditor() {

    var defaultHeight = 300;
    var newHeight = window.innerHeight-100; 
    var height = defaultHeight > newHeight ? defaultHeight: newHeight;
  
    CKEDITOR.instances.editor1.resize('100%',height);
  }