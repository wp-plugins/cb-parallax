/**
 * The parallax script.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

(function ( $ ){
    "use strict";

    $(document).ready(function (){

        // Default values
        var imageDefault = [];
        imageDefault['repeat']       = 'no-repeat';
        imageDefault['position_x']   = 'left';
        imageDefault['position_y']   = 'top';
        imageDefault['attachment']   = 'fixed';
        imageDefault['parallax']     = 'false';
        imageDefault['image_src']    = null;
        imageDefault['image_width']  = window.innerWidth;
        imageDefault['image_height'] = window.innerHeight;

        // The custom background post meta data / default values
        var postMeta = [];
        postMeta['repeat']           = (PostMetaData.repeat != 'undefined') ? PostMetaData.repeat : imageDefault['repeat'];
        postMeta['position_x']       = (PostMetaData.position_x != 'undefined') ? PostMetaData.position_x : imageDefault['position_x'];
        postMeta['position_y']       = (PostMetaData.position_y != 'undefined') ? PostMetaData.position_y : imageDefault['position_y'];
        postMeta['bg_attachment']    = (PostMetaData.bg_attachment != 'undefined') ? PostMetaData.bg_attachment : imageDefault['attachment'];
        postMeta['parallax_enabled'] = (PostMetaData.parallax_enabled != 'undefined') ? PostMetaData.parallax_enabled : imageDefault['parallax'];
        postMeta['image_src']        = (PostMetaData.image_src != 'undefined') ? PostMetaData.image_src : imageDefault['image_src'];
        postMeta['image_width']      = (PostMetaData.image_width != 'undefined') ? PostMetaData.image_width : imageDefault['image_width'];
        postMeta['image_height']     = (PostMetaData.image_height != 'undefined') ? PostMetaData.image_height : imageDefault['image_height'];

        // The array holding the parallax container related data
        var parallax = [];
        parallax['scrollRatio'] = null;

        // Helper variable
        var overflow = [];

        // Viewport dimensions
        var viewPortWidth;
        var viewPortHeight;

        var requestAnimationFrame = window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.requestAnimationFrame;

        var bodyElement       = $("body.custom-background");
        var parallaxContainer = null;

        var scrolling         = false;
        var mouseWheelActive  = false;

        var count             = 0;
        var mouseDelta        = 0;

        // Retrieve the document height
        function getDocumentHeight (){
            var D = document;
            return Math.max(
                D.body.scrollHeight, D.documentElement.scrollHeight,
                D.body.offsetHeight, D.documentElement.offsetHeight,
                D.body.clientHeight, D.documentElement.clientHeight
            );
        }

        // Cross browser method to get the viewport
        function getViewportHeight(){

            // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
            if (typeof window.innerWidth != 'undefined'){
                viewPortWidth = window.innerWidth;
                viewPortHeight = window.innerHeight;
            }

            // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
            else if (typeof document.documentElement != 'undefined'
                && typeof document.documentElement.clientWidth != 'undefined'
                && document.documentElement.clientWidth != 0){

                viewPortWidth = document.documentElement.clientWidth;
                viewPortHeight = document.documentElement.clientHeight;
            }

            // older versions of IE
            else{

                viewPortWidth = document.getElementsByTagName('body')[0].clientWidth;
                viewPortHeight = document.getElementsByTagName('body')[0].clientHeight;
            }

            return viewPortHeight;
        }

        // Normalize the scroll behaviour
        function mouseScroll ( e ){
            mouseWheelActive = true;

            // cancel the default scroll behavior
            if (e.preventDefault){
                e.preventDefault();
            }

            // deal with different browsers calculating the delta differently
            if (e.wheelDelta){
                mouseDelta = e.wheelDelta / 120;
            } else if (e.detail){
                mouseDelta = -e.detail / 3;
            }
        }

        // Calculates the scroll ratio
        function getScrollRatio (imageHeight, viewportHeight){

            // Calculates the "overflow" of the document
            overflow['document'] = getDocumentHeight() - viewportHeight;

            // Calculates the "overflow" of the image
            overflow['image'] = imageHeight - viewportHeight;

            // "Sanitizes" the value
            overflow['image'] = Math.abs(overflow['image']);

            // Calculates the scroll ratio
            var scrollRatio = (  overflow['document'] / overflow['image'] );

            // Shortens the value to two decimals and sets it
            parallax['scrollRatio'] = Math.abs(scrollRatio);

            return parallax['scrollRatio'];
        }

        // Retrieve the x-position of the parallax container -> prepared for future use with patterns as background-image
        function getParallaxPositionX() {

            if (postMeta['position_x'] == 'left'){

                postMeta['position_x'] = '0%';
            } else if (postMeta['position_x'] == 'center'){

                postMeta['position_x'] = '0%';
            } else if (postMeta['position_x'] == 'right'){

                postMeta['position_x'] = '0%';
            }

            return postMeta['position_x'];
        }

        // Retrieve the current y-position of the parallax container
        function getParallaxPositionY() {

            var value  = (-0.77 * getScrollPosition() / getScrollRatio(postMeta['image_height'], getViewportHeight() ) );

            return value.toFixed(2) + "px";
        }

        // Cross-browser way to get the current scroll position
        function getScrollPosition (){

            return pageYOffset || (document.documentElement.clientHeight ? document.documentElement.scrollTop : document.body.scrollTop);
        }

        // Called when a scroll is detected
        function setScrolling (){

            scrolling = true;
        }

        // Create the container that will hold the image to shift
        function createParallaxContainer (){

            bodyElement.prepend('<div id="cb-parallax-container"><img src="' + postMeta['image_src'] + '" id="cb-parallax-image" /><div>');

            parallaxContainer = $('#cb-parallax-container');
            parallaxContainer.addClass('custom-background');

            bodyElement.removeClass('custom-background');
            bodyElement.removeProp('background-image');
        }

        // The transform function fired during the scroll event
        function setTranslate3DTransform( element, xPosition, yPosition ) {


            element.css({
                '-moz-transform': "translate3d(" + xPosition + ", " + yPosition + ", 0)",
                '-ms-transform': "translate3d(" + xPosition + ", " + yPosition + ", 0)",
                '-o-transform': "translate3d(" + xPosition + ", " + yPosition + ", 0)",
                'transform': "translate3d(" + xPosition + ", " + yPosition + ", 0)",
                '-webkit-transform': "translate3d(" + xPosition + ", " + yPosition + ", 0)"
            });
        }

        // The controller for the scroll event and mouse scroll normalisation
        function animationLoop (){

            // adjust the image container's position during a scroll event
            if (scrolling){
                setTranslate3DTransform(parallaxContainer, getParallaxPositionX(), getParallaxPositionY());

                scrolling = false;
            }

            // scroll up or down by 10 pixels when the mousewheel is used
            if (mouseWheelActive){

                window.scrollBy(0, -mouseDelta * 6);
                count++;

                // stop the scrolling after a few moments
                if (count > 20){
                    count = 0;
                    mouseWheelActive = false;
                    mouseDelta = 0;
                }

            }

            requestAnimationFrame(animationLoop);
        }

        // Checks if an image is set and if the parallax option is set too - else we won't do anything
        function init (){

            if (postMeta['image_src'] != 'undefined' && postMeta['parallax_enabled']){

                // Handles the scroll events
                window.addEventListener("scroll", setScrolling, false);
                window.addEventListener("mousewheel", mouseScroll, false);
                window.addEventListener("DOMMouseScroll", mouseScroll, false);

                createParallaxContainer();
                animationLoop();

            } else if(postMeta['image_src'] != 'undefined' && !postMeta['parallax_enabled']) {

                setCustomBackgroundStyle();
            }
        }

        // Kick off
        init();

        // Set custom background style
        function setCustomBackgroundStyle (){

            bodyElement.css('background-repeat', postMeta['repeat']);
            bodyElement.css('background-position-x', postMeta['position_x']);
            bodyElement.css('background-position-y', postMeta['position_y']);
            bodyElement.css('background-attachment', postMeta['bg_attachment']);
        }

    });

})(jQuery);
