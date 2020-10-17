<?php
namespace App\Ivb\App\Views;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CKEditor</title>
    <script src="/app1/app/ivb/jquery/2.2.4/jquery.min.js"></script>
    <script src="/app1/app/ivb/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/app1/app/ivb/bootstrap/css/bootstrap.min.css">
    <script src="https://cdn.ckeditor.com/4.15.0/full/ckeditor.js"></script>
    <script src="/app1/app/ivb/js/common.js"></script>
    <script src="/app1/app/ivb/he/he.js"></script>
    <script src="/app1/app/ivb/js/editor.js"></script>
</head>

<body>
    <div class="container">
        <div class="panel panel-default col-md-12">
            <form id="frm_main" class="form-horizontal" action="/app1/public/api/cms/update_cms_page_data" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="id_page" name="id_page" value="<?php echo $id_page; ?>">
                <div class="row">
                    <textarea name="editor1" id="editor1" rows="20" cols="80"></textarea>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
    $(function() {
        //postavi editor

        //postavljanje XSRF tokena na svaki ajax poziv
        $.ajaxSetup({
            headers: {
                'X-XSRF-TOKEN': getCookie("XSRF-TOKEN")
            }
        });

        window.onresize = function() {
            resizeEditor();
        }

        CKEDITOR.replace('editor1', {
            on: {
                instanceReady: function(ev) {
                    setTimeout(resizeEditor, 500);
                }
            }
        });

        SetupPage();

    });
</script>

</html> 