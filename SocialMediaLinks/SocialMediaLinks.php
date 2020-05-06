<?php
/*
Plugin Name:Social Media Links
Plugin URI:
Description: Put your social media links in footer
Version: 1.0
Author: Ali YAKAR
Author URI: http://pluscrea.net
License: GPLv2
*/

register_activation_hook(__FILE__, 'set_default_sml_options');

function set_default_sml_options()
{

    $fields = array(
        array(
            'id' => '0',
            'enable' => ''
        ),
        array(
            'id' => '1',
            'name' => 'Facebook',
            'link' => 'www.facebook.com',
            'image' => 'facebook.png',
            'enable' => 'checked'
        ),
        array(
            'id' => '2',
            'name' => 'Twitter',
            'link' => 'www.twitter.com',
            'image' => 'twitter.png',
            'enable' => 'checked'
        ),
        array(
            'id' => '3',
            'name' => 'Instagram',
            'link' => 'www.instagram.com',
            'image' => 'instagram.png',
            'enable' => 'checked'
        ),
        array(
            'id' => '4',
            'name' => 'YouTube',
            'link' => 'www.youtube.com',
            'image' => 'youtube.png',
            'enable' => 'checked'
        ),
        array(
            'id' => '5',
            'name' => 'Linkedin',
            'link' => 'www.linkedin.com',
            'image' => 'linkedin.png',
            'enable' => ''
        ),
        array(
            'id' => '6',
            'name' => 'Whatsup',
            'link' => 'www.whatsup.com',
            'image' => 'whatsup.png',
            'enable' => ''
        ),
        array(
            'id' => '7',
            'name' => 'Pinterest',
            'link' => 'www.pinterest.com',
            'image' => 'pinterest.png',
            'enable' => ''
        ),
        array(
            'id' => '8',
            'name' => 'Rss',
            'link' => 'www.rss.com',
            'image' => 'rss.png',
            'enable' => ''
        )
    );

    foreach ($fields as $value) {
        $option = "sml_options" . $value['id'];

        if (get_option($option) === false) {
            add_option($option, $value);
        } else {
            update_option($option, $value);
        }

        //$opt = get_option($option);
        //print_r($opt);
    }

    /*
    if (get_option('sml_options') === false) {
        $sml_options = new stdClass();
        $sml_options->name = "sml_options";
        $sml_options->params->smname = "Facebook";
        $sml_options->params->link = 'www.facebook.com';
        $sml_options->params->status = 0;
        add_option('sml_options', $sml_options);
    }
    */
}

// Update CSS within in Admin
function admin_style()
{
    //wp_enqueue_style('admin-styles', plugin_dir_path(__FILE__) . 'css/sml.css');
    wp_enqueue_style('admin-styles', plugins_url('css/sml.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'admin_style');





//Add sublink under the Settings
add_action("admin_menu", "sml_addsublink");

function sml_addsublink()
{
    add_submenu_page(
        'options-general.php',
        'Manage Social Media Links',
        'Social Media Links',
        'administrator',
        'Social-Media-Links',
        'sml_config_page'
    );
}

function get_images_from_media_library($Pid)
{
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'id' => $Pid
    );
    $query_images = new WP_Query($args);
    $images = array();
    foreach ($query_images->posts as $image) {
        $images[] = $image->guid;
    }

    return $images[0];
}


function sml_config_page()
{
    $message = "";

    if (($_POST["action"] == "formupdate")  || wp_verify_nonce($_POST['name_of_nonce_field'], 'name_of_my_action')) {


        if ($_POST["generalstatus"] == "on")
            $generalstatus = "checked";

        $sml_options = array(
            'id' =>  0,
            'enable' => $generalstatus
        );

        update_option("sml_options0", $sml_options);

        for ($i = 1; $i <= 8; $i++) {
            $option = "sml_options" . $i;

            $image = $_POST["oldimage" . $i];
            $status = "";
            if ($_POST["status" . $i] == "on")
                $status = "checked";

            //$dnm_img = media_handle_upload("image1", 0);
            //print_r($dnm_img);
            /*
            if (isset($_FILES)) {
                if (isset($_FILES["image" . $i]["name"])) {
                    //$dnm_img = media_handle_upload("image" . $i, 0);
                    //print_r($dnm_img);
                    echo "Geldi : " . $_FILES["image" . $i]["name"];
                    //print_r($_FILES);
                }
            }
            */
            if ($_FILES["image" . $i]["size"] > 0) {
                $image = media_handle_upload("image" . $i, 0);
            }

            $sml_options = array(
                'id' =>  $i,
                'name' => $_POST["oldname" . $i],
                'link' => $_POST["link" . $i],
                'image' => $image,
                'enable' => $status
            );

            update_option($option, $sml_options);
        }
        $message = "<div class=\"notice notice-success\">
            <p><strong>Settings saved.</strong></p>
        </div>";
    }

?>
    <div class="wrap">
        <?php echo $message; ?>
        <br>
        <h2>Manage Social Media Links</h2>
        <br>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="formupdate">
            <table class="tableSml">
                <tr>
                    <td>Enable : </td>
                    <?php
                    $generalstatus = get_option("sml_options0");
                    ?>
                    <td colspan="3"><input type="checkbox" name="generalstatus" <?php echo $generalstatus['enable'];  ?>></td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <th>Name </th>
                    <th>Link </th>
                    <th>Logo </th>
                    <th>Status </th>
                </tr>

                <?php
                wp_nonce_field('sml_action', 'sml_nonce_field');

                for ($i = 1; $i <= 8; $i++) {
                    $option = "sml_options" . $i;
                    $sml_opt = get_option($option);
                ?>
                    <tr>
                        <td><?php echo $sml_opt['name'];  ?>
                            <input type="hidden" name="oldname<?php echo $sml_opt["id"]; ?>" value="<?php echo $sml_opt['name'] ?>">
                        </td>
                        <td><input type="text" name="link<?php echo $sml_opt["id"];  ?>" value="<?php echo $sml_opt['link'];  ?>" placeholder="Link" /></td>
                        <td><img src="<?php
                                        if (is_numeric($sml_opt['image'])) {
                                            echo get_images_from_media_library($sml_opt['image']);
                                        } else {
                                            echo plugins_url("images/" . $sml_opt['image'] . "", __FILE__);
                                        }
                                        ?>">
                            <br>
                            <input type="hidden" name="oldimage<?php echo $sml_opt["id"]; ?>" value="<?php echo $sml_opt['image'] ?>">
                            <input type="file" name="image<?php echo $sml_opt["id"]; ?>" accept="image/*">
                        </td>
                        <td>Enable : <input type="checkbox" name="status<?php echo $sml_opt["id"];  ?>" <?php echo $sml_opt['enable'];  ?>> <br><br></td>
                    </tr>
                <?php }  ?>

                <tr>
                    <td></td>
                    <td></td>
                    <td> <input type="submit" value="Submit" class="button-primary" /></td>
                    <td></td>
                </tr>
            </table>
        </form>
    </div>
    <?php }

function xobamax_resources()
{
    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
}
add_action('wp_enqueue_scripts', 'xobamax_resources');

add_action('wp_footer', 'ch2lfa_footer_analytics_code');


function ch2lfa_footer_analytics_code()
{
    $generalstatus = get_option("sml_options0");
    if ($generalstatus['enable'] == "checked") {
    ?>
        <div class="container-sm">
            <div class="row">
                <div class="col-sm-8"></div>
                <?php

                for ($i = 1; $i <= 8; $i++) {
                    $option = "sml_options" . $i;
                    $sml_opt = get_option($option);
                    if ($sml_opt['enable'] == "checked") {
                ?>
                        <div class="col-sm">
                            <a href="<?php echo $sml_opt['link'];  ?>" class="stretched-link" target="_blank" alt="<?php echo $sml_opt['name'] ?>">
                                <img src="<?php
                                            if (is_numeric($sml_opt['image'])) {
                                                echo get_images_from_media_library($sml_opt['image']);
                                            } else {
                                                echo plugins_url("images/" . $sml_opt['image'] . "", __FILE__);
                                            }
                                            ?>" width="50px" height="50px"></a>
                        </div>
                <?php }
                } ?>
            </div>
        </div <?php }
        }
