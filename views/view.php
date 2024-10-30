<?php

defined('ABSPATH') or die('Unauthorized Access');

$root_dir = get_home_path();

if(is_writable("$root_dir/.htaccess")) {

    $auth = true;

    if(isset($_POST['backup'])) {
        $backup = fopen("$root_dir/.htaccess.bak", 'wb');
        $htaccess = file_get_contents("$root_dir/.htaccess");
        $checker = fopen("$root_dir/.htaccess", "r");
        $first_line = fgets($checker);
        if(trim($first_line) !== '#BACKED UP by .htaccess editor WP') {
            fwrite($backup, '#BACKED UP by .htaccess editor WP' . PHP_EOL);
        }
        fwrite($backup, $htaccess);
        fclose($backup);
        echo '<script>    window.addEventListener("DOMContentLoaded", () => {
        document.getElementById("hewp-warning").innerHTML = "FILE HAS BEEN BACKED UP!";
        document.getElementById("hewp-warning").style.color = "green";
    });</script>';

    } elseif(isset($_POST['restore'])) {
        $backup_file = file_get_contents("$root_dir/.htaccess.bak");
        $htaccess = fopen("$root_dir/.htaccess", 'w');
        fwrite($htaccess, $backup_file);
        fclose($htaccess);
        echo '<script>    window.addEventListener("DOMContentLoaded", () => {
        document.getElementById("hewp-warning").innerHTML = "FILE HAS BEEN RESTORED!";
        document.getElementById("hewp-warning").style.color = "green";
    });</script>';

    } elseif(isset($_POST['save'])) {

        $custom_htaccess = sanitize_textarea_field(stripslashes($_POST['htaccess-file']));
        $htaccess = file_get_contents("$root_dir/.htaccess");
        $backup = fopen("$root_dir/.htaccess.bak", 'wb');
        fwrite($backup, $htaccess);
        fclose($backup);
        $new_htaccess = fopen("$root_dir/.htaccess", 'w');
        fwrite($new_htaccess, $custom_htaccess);
        fclose($new_htaccess);

        $response = wp_remote_get(get_site_url());
        $http_code = wp_remote_retrieve_response_code( $response );

        if($http_code == 500) {

            $backup_file = file_get_contents("$root_dir/.htaccess.bak");
            $htaccess = fopen("$root_dir/.htaccess", 'w');
            fwrite($htaccess, $backup_file);
            fclose($htaccess);

            echo '<script>    window.addEventListener("DOMContentLoaded", () => {
            document.getElementById("hewp-warning").innerHTML = "CAN\'T SAVE. ERROR ON YOU CODE";
            document.getElementById("hewp-warning").style.color = "red";
        });</script>';
        } else {
            echo '<script>    window.addEventListener("DOMContentLoaded", () => {
            document.getElementById("hewp-warning").innerHTML = "FILE HAS BEEN SAVED!";
            document.getElementById("hewp-warning").style.color = "green";
        });</script>';
        }

    }
} else {
    echo '<h1 style="text-align: center;color: #21759b;margin-top: 50px">Need write permission on htaccess file. Can\'t perform the action</h1>';
}

if($auth):
?>

<div id="htaccess-editor-wp">
    <h1 style="text-align: center;margin: 40px 0 30px 0"><span class="htaccess-editor-wp-title">.htaccess Editor WP</span><sub style="font-size: 12px">by
            <a href="https://imransagor.codes" class="hewp-author" target="_blank">Imran Hossain Sagor</a></sub></h1>
    <hr style="width:50%;margin-left:0 auto">
    <h2 id="hewp-warning" style="margin-top: 35px;color: #21759;"></h2>
    <div id="hewp-form">
        <form action="" METHOD="post">
            <textarea name="htaccess-file" id="htaccess-editor-file"><?php
                $files = scandir($root_dir);
                if(in_array('.htaccess', $files)) {
                    echo file_get_contents("$root_dir/.htaccess");
                }
                ?></textarea><br>
            <button name="save" id="hewp-htaccess-save">Save</button>
            <button name="backup" id="hewp-htaccess-backup">Backup</button>
            <?php if(file_exists("$root_dir/.htaccess.bak")) : ?>
            <button name="restore" id="hewp-htaccess-restore">Restore</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php

endif;

$this->hewp_thankyou();
