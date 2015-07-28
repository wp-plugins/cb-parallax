/**
 * The script for the meta box functionality.

 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @param AdminMeta
 * @param cbParallaxFrame
 */

jQuery(document).ready(function ( $ ){

    $(".cb-parallax-wp-color-picker").wpColorPicker();
    $('label[for="cb-parallax-background-color"]').hide();

    if ($(".cb-parallax-background-image-url").attr("src")){

        $(".cb-parallax-background-image-url").show();

        if (AdminMeta.parallax_possible){

            $('input#cb-parallax-enabled').show();
            $('label[for="cb-parallax-enabled"]').show();
        }
    } else{

        $(".cb-parallax-background-image-url").hide();
        $('input#cb-parallax-enabled').hide();
        $('label[for="cb-parallax-enabled"]').hide();
    }

    if ($("input#cb-parallax-background-image").val()){

        $(".cb-parallax-add-media-text").hide();
        $(".cb-parallax-background-image-url").show();

        if (AdminMeta.parallax_possible){

            $('input#cb-parallax-enabled').show();
            $('label[for="cb-parallax-enabled"]').show();
        } else {

            // Responsible for hiding these elements on page load (if necessary)
            $('input#cb-parallax-enabled').hide();
            $('label[for="cb-parallax-enabled"]').hide();
        }

        $(".cb-parallax-remove-media, .cb-parallax-background-image-options, .cb-parallax-background-image-url").show();
    } else{

        $(".cb-parallax-add-media-text").show();
        $('input#cb-parallax-enabled').hide();
        $('label[for="cb-parallax-enabled"]').hide();
        $(".cb-parallax-remove-media, .cb-parallax-background-image-options, .cb-parallax-background-image-url").hide();
    }

    $(".cb-parallax-remove-media").click(function ( j ){

        j.preventDefault();
        $("#cb-parallax-background-image").val("");
        $(".cb-parallax-add-media-text").show();
        $('input#cb-parallax-enabled').hide();
        $('label[for="cb-parallax-enabled"]').hide();
        $(".cb-parallax-remove-media, .cb-parallax-background-image-url, .cb-parallax-background-image-options").hide();
    });

    var cb_parallax_frame;

    $(".cb-parallax-add-media").click(function ( j ){

        j.preventDefault();
        if (cb_parallax_frame){
            cb_parallax_frame.open();
            return;
        }
        cb_parallax_frame = wp.media.frames.cb_parallax_frame = wp.media({

            className: "media-frame cb-parallax-frame",
            frame: "select",
            multiple: false,
            title: cbParallaxFrame.title,
            library: {type: "image"},
            button: {text: cbParallaxFrame.button}
        });

        // @todo: ajax implementieren
        cb_parallax_frame.on("select", function (){

            var media_attachment = cb_parallax_frame.state().get("selection").first().toJSON();

            $("#cb-parallax-background-image").val(media_attachment.id);
            $(".cb-parallax-background-image-url").attr("src", media_attachment.url);
            $(".cb-parallax-add-media-text").hide();

            // Checks if the image dimensions suffise for the parallax effect to work
            if(media_attachment.width >= 1920 && media_attachment.height >= 1200) {

                $('input#cb-parallax-enabled').show();
                $('label[for="cb-parallax-enabled"]').show();
            } else {

                $('input#cb-parallax-enabled').hide();
                $('label[for="cb-parallax-enabled"]').hide();
            }

            $(".cb-parallax-background-image-url, .cb-parallax-remove-media, .cb-parallax-background-image-options").show();
        });

        cb_parallax_frame.open();
    });
});
