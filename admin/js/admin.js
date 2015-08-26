/**
 * The script for the meta box functionality.

 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/cbParallax/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * @param cbParallax
 * @param cbParallaxMediaFrame
 */
(function ( $ ){
    "use strict";

    $(document).ready(function (){

        //Parallax on/off-switch.
        var cbParallaxSwitch = $('.cbp-switch');

        // Parallax input field.
        var cbParallaxInput = $('.cbp-switch-input');

        // Media frame.
        var cb_parallax_frame;

        // Localisation of the texts on the switch, if that language is included.
        if (cbParallax.locale == 'de_DE'){

            $('<style>.cbp-switch-label.cbp_parallax_enabled:before{content:"' + cbParallax.switchesText.Off + '";}</style>').appendTo('head');
            $('<style>.cbp-switch-label.cbp_parallax_enabled:after{content:"' + cbParallax.switchesText.On + '";}</style>').appendTo('head');
        }

        /*--------------------------------------------------
         * Color picker
         *------------------------------------------------*/
        $('#cbp_background_color').wpColorPicker();

        // Hide the label for the color picker.
        $('label[for="cbp_background_color"]').hide();

        // Localizes the text on the color picker.
        $('div#cb-parallax-meta-box a.wp-color-result').prop('title', cbParallax.backgroundColorText);

        /*--------------------------------------------------
         * Fancy Select
         *------------------------------------------------*/
        $('.cbp-fancy-select').fancySelect();

        /*--------------------------------------------------
         * Parallax options helper functions.
         *------------------------------------------------*/
        // Toggles between the static image options and the ones for the parallax effect.
        function toggleParallaxSwitch(){

            if (cbParallaxInput.prop('checked')){

                $('.cbp-background-image-options-container').hide();
                $('.cbp-parallax-options-container').show();

                return true;
            } else{

                $('.cbp-background-image-options-container').show();
                $('.cbp-parallax-options-container').hide();

                return true;
            }
        }

        // Toggles the parallax direction mode (vertical / horizontal).
        function toggleDirectionMode (){

            if ($('#cbp_direction').val() === cbParallax.verticalDirection){

                $('#cpb_horizontal_scroll_direction_container').hide();
                $('#cbp_vertical_alignment_container').hide();

                $('#cpb_vertical_scroll_direction_container').show();
                $('#cbp_horizontal_alignment_container').show();
            } else if($('#cbp_direction').val() === cbParallax.horizontalDirection) {

                $('#cpb_vertical_scroll_direction_container').hide();
                $('#cbp_horizontal_alignment_container').hide();

                $('#cpb_horizontal_scroll_direction_container').show();
                $('#cbp_vertical_alignment_container').show();
            }
        }

        // Sets the initial view for the meta box and the options.
        function setInitialView (){

            // If there is an attachment...
            if(typeof cbParallax.attachmentUrl != 'undefined') {

                // If parallax is not possible with this attachment...
                if (!cbParallax.parallaxPossible){

                    $(".cbp-parallax-options").hide();
                    $('.cbp-parallax-enabled-container').hide();
                    $(".cbp-background-image-options-container").show();

                    // else if it is possible AND set...
                } else if (cbParallax.parallaxPossible && cbParallax.parallaxEnabled){

                    cbParallaxInput.prop('checked', true);
                    $(".cbp-background-image-options-container").hide();
                    $('.cbp-parallax-enabled-container').show();
                    $(".cbp-parallax-options-container").show();
                } else{

                    cbParallaxInput.prop('checked', false);
                    $(".cbp-parallax-options-container").hide();
                    $(".cbp-background-image-options-container").show();
                }

                // If there is an attachment...
                if ($("#cbp_background_image").val()){

                    $(".cbp-add-media-button").hide();
                    $(".cbp_background_image_url").show();
                    $(".cbp-remove-media-button, .cbp_background_image_url").show();
                } else{

                    $('.cbp-parallax-enabled-container').hide();
                    $(".cbp-remove-media-button, .cbp_background_image_url").hide();
                    $(".cbp-background-image-options-container").hide();
                    $(".cbp-parallax-options-container").hide();
                    $(".cbp-add-media-button").show();
                }

                // ...else if there is no attachment...
            } else {

                $('.cbp-parallax-enabled-container').hide();
                $(".cbp-remove-media-button, .cbp_background_image_url").hide();
                $(".cbp-background-image-options-container").hide();
                $(".cbp-parallax-options-container").hide();
                $(".cbp-add-media-button").show();
            }

            // Toggle the options that depend on the direction (mode).
            toggleParallaxSwitch();
            toggleDirectionMode();
        }

        // Kick off.
        setInitialView();

        /*--------------------------------------------------
         * Listeners for the helper functions.
         *------------------------------------------------*/
        // "Parallax on/off" toggle.
        cbParallaxSwitch.bind('click', function ( event ){
            event = event || window.event;
            event.preventDefault();

            cbParallaxInput.attr("checked", !cbParallaxInput.attr("checked"));
            toggleParallaxSwitch();
        });

        // "Toggle parallax direction" listener ( vertical / horizontal).
        var parallaxEnabledSelect = $('select#cbp_direction').parent();
        parallaxEnabledSelect.fancySelect().on('change.fs', function (){
            $(this).trigger('change.$');
            toggleDirectionMode();
        });

        /*--------------------------------------------------
         * Teh buttonz.
         *------------------------------------------------*/
        // Remove media button.
        $(".cbp-remove-media-button").click(function ( event ){

            event.preventDefault();
            $("#cbp_background_image").val("");
            $(".cbp-add-media-button").show();
            $('.cbp-parallax-enabled-container').hide();
            $(".cbp-remove-media-button, .cbp_background_image_url, .cbp-background-image-options-container, .cbp-parallax-options").hide();
        });

        // Add media button.
        $(".cbp-add-media-button").click(function ( event ){

            event.preventDefault();
            if (cb_parallax_frame){
                cb_parallax_frame.open();
                return;
            }
            cb_parallax_frame = wp.media.frames.cb_parallax_frame = wp.media({

                className: "media-frame cb-parallax-frame",
                frame: "select",
                multiple: false,
                title: cbParallaxMediaFrame.title,
                library: {type: "image"},
                button: {text: cbParallaxMediaFrame.button}
            });

            cb_parallax_frame.on("select", function (){

                var media_attachment = cb_parallax_frame.state().get("selection").first().toJSON();

                $("#cbp_background_image").val(media_attachment.id);
                $(".cbp_background_image_url").attr("src", media_attachment.url);
                $(".cbp-add-media-button").hide();

                $('.cbp-background-image-options-container').show();
                $('.cbp-parallax-options-container').hide();
                cbParallaxInput.prop('checked', false);

                // Checks if the image dimensions suffise for the parallax effect to work and displays the checkbox and its label - or not.
                if (media_attachment.width >= 1920 && media_attachment.height >= 1200){

                    $('.cbp-parallax-enabled-container').show();
                } else{

                    $('.cbp-parallax-enabled-container').hide();
                }

                $(".cbp_background_image_url, .cbp-remove-media-button, .cbp-background-image-options-container").show();
            });

            // Opens the media frame.
            cb_parallax_frame.open();
        });
    });

})(jQuery);
