/*jshint esversion: 8 */

//kad god je stranica učitana
async function SetupPage() {

    //učitaj listu korisnika i stavi ih u listu    
    RefreshDataGrid();


    //submit event forme
    //**************************************************************
    $("#frm_edit_user").on("submit", function (e) {

        //zaustavi sve...
        e.stopPropagation();
        e.preventDefault();

        //kontejner za podatke
        var formData = new FormData();

        //podaci iz forme

        //id korisnika
        formData.append('user_id', $("#user_id").val());

        //lozinka
        formData.append('password', $("#new_pwd").val());

        //email
        formData.append('user_email', $("#user_email").val());

        //user name
        formData.append('username', $("#username").val());

        //provera tipa
        var x = '';

        if ($('#optradio_admin').prop('checked') == true) {
            x = "2"; //'Admin';
        }

        if ($('#optradio_user').prop('checked') == true) {
            x ="1"; //'StandardUser';
        }

        if ($('#optradio_guest').prop('checked') == true) {
            x ="0"; //'Guest';
        }

        //dodata informacija o tipu korisnika
        formData.append('user_type', x);

        //da li se korinik briše
        formData.append('delete_user', $('#chk_delete_user').prop('checked'));


        $.ajax({
            method: "POST",
            url: "./api/users/update",
            data: formData,
            cache: false,
            async: true,
            processData: false,
            contentType: false,

            success: function (data) {
                //sačuvaj rezultat

                let rec_no =parseInt(data.result);

                let err = data.error;

                //prikaži poruku
                if (!err && rec_no>0) {
                    $("#modal_pwd_set").modal('hide');
                    RefreshDataGrid();
                } else {
                    alert("Upis neuspešan..!");
                }


            }
        });


    });

}

//izmene podataka kroz modalnu formu
async function EditUser(user_id) {

    //učitaj podatke o korisniku
    user=await GetUser(user_id);


    //upiši podatke korisnika u formu


    //email
    $('#user_email').val(user.email);

    //korisničko ime
    $('#username').val(user.username);

    //id
    $('#user_id').val(user.user_id);


    switch (user.user_type_name) {
        case 'Admin':
            $('#optradio_admin').prop('checked', true);
            break;
        case 'StandardUser':
            $('#optradio_user').prop('checked', true);
            break;
        case 'Guest':
            $('#optradio_guest').prop('checked', true);
            break;

        default:
            break;
    }


    $("#modal_pwd_set").modal();
}

//osvežava data grid
function RefreshDataGrid() {
    $.ajax({
        method: "POST",
        url: "./api/users/get_list",
        cache: false,
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        success: function (data) {
            var items = '';

            items += '<table class="table table-bordered">';
            items += '<thead><tr><th>E-mail</th><th>Korisničko ime</th><th>Tip korisnika</th><th>&nbsp;</th></tr></thead>';

            //prolazak kroz JSON objekt
            $.each(data, function (index) {

                //1 red

                items += '<tbody>';
                items += '<td>' + data[index].email + '</td>';
                items += '<td>' + data[index].username + '</td>';
                items += '<td>' + data[index].user_type_name + '</td>';
                items += '<td><button type="button" onclick="EditUser(' + data[index].user_id + ')" class="btn btn-default">Uredi...</button></td>';

            });

            items += '</tbody></table>';

            //postavi opcije u stranicu
            $("#users").html(items);

        }
    });
}


