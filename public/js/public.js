/**
 * The Public script.
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

    var defaultOptions = {
        parallaxPossible: false,
        parallaxEnabled: false,
        direction: 'vertical',
        verticalScrollDirection: 'top',
        horizontalScrollDirection: 'left',
        horizontalAlignment: 'center',
        verticalAlignment: 'center',
        overlayImage: 'none',
        imageSrc: '',
        imageWidth: $(window).innerWidth(),
        imageHeight: $(window).innerHeight(),
        overlayPath: '',
        overlayOpacity: '0.3'
    };

    var parallax = {
        possible: cbParallax.parallaxPossible != 'undefined' ? cbParallax.parallaxPossible : defaultOptions.parallaxPossible,
        enabled: cbParallax.parallaxEnabled != 'undefined' ? cbParallax.parallaxEnabled : defaultOptions.parallaxEnabled,
        direction: cbParallax.direction != 'undefined' ? cbParallax.direction : defaultOptions.direction,
        verticalScrollDirection: cbParallax.verticalScrollDirection != 'undefined' ? cbParallax.verticalScrollDirection : defaultOptions.verticalScrollDirection,
        horizontalScrollDirection: cbParallax.horizontalScrollDirection != 'undefined' ? cbParallax.horizontalScrollDirection : defaultOptions.horizontalScrollDirection,
        horizontalAlignment: cbParallax.horizontalAlignment != 'undefined' ? cbParallax.horizontalAlignment : defaultOptions.horizontalAlignment,
        verticalAlignment: cbParallax.verticalAlignment != 'undefined' ? cbParallax.verticalAlignment : defaultOptions.verticalAlignment
    };

    var image = {
        src: cbParallax.imageSrc != 'undefined' ? cbParallax.imageSrc : defaultOptions.imageSrc,
        width: cbParallax.imageWidth != 'undefined' ? cbParallax.imageWidth : defaultOptions.imageWidth,
        height: cbParallax.imageHeight != 'undefined' ? cbParallax.imageHeight : defaultOptions.imageHeight
    };

    var overlay = {
        path: cbParallax.overlayPath != 'undefined' ? cbParallax.overlayPath : defaultOptions.overlayPath,
        image: cbParallax.overlayImage != 'undefined' ? cbParallax.overlayImage : defaultOptions.overlayImage,
        opacity: cbParallax.overlayOpacity != 'undefined' ? cbParallax.overlayOpacity : defaultOptions.overlayOpacity
    };

    var body = $('body');

    var imageContainer = null;

    var overlayContainer = null;

    var requestAnimationFrame = window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.requestAnimationFrame;

    // Converts alignments such as left, center and right into pixel values.
    function getHorizontalAlignAsPosition(){

        var horizontalPosition = null;

        switch (parallax.horizontalAlignment){

            case 'left':
                horizontalPosition = '0';
                break;

            case 'center':
                horizontalPosition = ($(window).innerWidth() / 2) - (image.width / 2) + 'px';
                break;

            case 'right':
                horizontalPosition = $(window).innerWidth() - image.width + 'px';
                break;
        }

        return horizontalPosition;
    }

    // Converts alignments such as top, center and bottom into pixel values.
    function getVerticalAlignAsPosition(){

        var verticalPosition = null;

        switch (parallax.verticalAlignment){

            case 'top':
                verticalPosition = '0';
                break;

            case 'center':
                verticalPosition = ($(window).innerHeight() / 2) - (image.height / 2) + 'px';
                break;

            case 'bottom':
                verticalPosition = $(window).innerHeight() - image.height + 'px';
                break;
        }

        return verticalPosition;
    }

    // Calculates the scroll ratio depending on the viewport size, the image size, the shifting-direction and the scroll direction.
    function getScrollRatio (){

        var verticalDocumentOffset = null;
        var verticalImageOffset = null;
        var horizontalImageOffset = null;
        var ratio = null;

        if (parallax.direction == 'vertical'){

            verticalDocumentOffset = body.innerHeight() - $(window).innerHeight();
            verticalImageOffset = image.height - $(window).innerHeight();
            ratio = verticalDocumentOffset > verticalImageOffset ? (verticalImageOffset / verticalDocumentOffset) : (verticalDocumentOffset / verticalImageOffset);
        } else if (parallax.direction == 'horizontal'){

            verticalDocumentOffset = body.innerHeight() - $(window).innerHeight();
            horizontalImageOffset = image.width - $(window).innerWidth();
            ratio = verticalDocumentOffset > horizontalImageOffset ? (horizontalImageOffset / verticalDocumentOffset) : (verticalDocumentOffset / horizontalImageOffset);
        }

        return ratio;
    }

    // Sets style.
    function setStyle (){

        // Removes the custom background class and the background image.
        body.removeClass('custom-background');
        body.removeProp('background-image');

        // Sets the image dimensions.
        imageContainer.css({
            'width': image.width,
            'height': image.height + 'px',
            'background-size': image.width + 'px' + ' ' + image.height + 'px'
        });
    }

    // Transformation function.
    function setTranslate3DTransform (){

        var ratio = getScrollRatio();
        var scrolling = $(window).scrollTop();
        var transformX = null;
        var transformY = null;

        if (parallax.direction == 'vertical'){

            if (parallax.verticalScrollDirection == 'to top'){
                transformY = -scrolling * ratio;

            } else if (parallax.verticalScrollDirection == 'to bottom'){
                transformY = scrolling * ratio;
            }

            imageContainer.css({
                '-webkit-transform': 'translateY(' + transformY + 'px)',
                '-moz-transform': 'translateY(' + transformY + 'px)',
                '-ms-transform': 'translateY(' + transformY + 'px)',
                '-o-transform': 'translateY(' + transformY + 'px)',
                'transform': 'translateY(' + transformY + 'px)',

                'left': getHorizontalAlignAsPosition()
            });
        } else if (parallax.direction == 'horizontal'){

            if (parallax.horizontalScrollDirection == 'to the left'){
                transformX = -scrolling * ratio;

            } else if (parallax.horizontalScrollDirection == 'to the right'){
                transformX = scrolling * ratio;
            }

            imageContainer.css({
                '-webkit-transform': 'translateX(' + transformX + 'px)',
                '-moz-transform': 'translateX(' + transformX + 'px)',
                '-ms-transform': 'translateX(' + transformX + 'px)',
                '-o-transform': 'translateX(' + transformX + 'px)',
                'transform': 'translateX(' + transformX + 'px)',

                'top': getVerticalAlignAsPosition()
            });
        }
    }


    $(document).ready(function (){

        // Image container.
        function createImageContainer (){

            body.prepend('<canvas id="cbp_image_container" class="custom-background" width="' + image.width + '" height="' + image.height + '"><canvas>');
            imageContainer = $('#cbp_image_container');
        }

        // Overlay.
        function setOverlay (){

            if (overlay.image != "none"){

                body.prepend('<div id="cbp_overlay"></div>');
                overlayContainer = $('#cbp_overlay');

                overlayContainer.css({
                    'background-image': 'url(' + overlay.path + overlay.image + ')',
                    'opacity': overlay.opacity
                });
            }
        }

        // Sets the initial horizontalPosition.
        function setInitialPosition (){
            
            if (parallax.direction == 'vertical'){

                imageContainer.css({
                    'left': getHorizontalAlignAsPosition()
                });

                if (parallax.verticalScrollDirection == 'to top'){

                    imageContainer.css({
                        'top': 0
                    });
                    
                } else if (parallax.verticalScrollDirection == 'to bottom'){

                    imageContainer.css({
                        'bottom': 0
                    });
                }

            } else if (parallax.direction == 'horizontal'){

                imageContainer.css({
                    'top': getVerticalAlignAsPosition()
                });

                if (parallax.horizontalScrollDirection == 'to the left'){

                    imageContainer.css({
                        'left': 0
                    });
                    
                } else if (parallax.horizontalScrollDirection == 'to the right'){

                    imageContainer.css({
                        'right': 0
                    });
                }
            }
        }

        var isScrolling = false;

        // Assigns the appropriate element based on the user agent.
        function getBodyElement() {

            var element = null;

            if ($.browser.webkit){

                element = body;
            } else if ($.browser.mozilla){

                element = body;
            } else if ($.browser.msie){

                element = $('html');
            } else if ($.browser.opera){

                element = $('html');
            } else{

                element = body;
            }

            return element;
        }

        // Listeners for the scroll event.
        function onScroll (){
            isScrolling = true;
            requestAnimationFrame(animationLoop);
        }

        // Listener for the mousewheel.
        function onMouseWheel (){

            var top       = $(window).scrollTop(),
                step      = 160,
                viewport  = $(window).innerHeight(),
                wheel     = true;

            var element   = getBodyElement();

            if($('#ascrail2000').length == '0') {

                element.mousewheel(function ( event, delta ){
                    event.preventDefault();

                    wheel = true;

                    if (delta < 0){

                        top = (top + viewport) >= $(document).innerHeight() ? top : top += step;

                        element.stop().animate({scrollTop: top}, 400, function (){
                            wheel = false;
                        });
                    } else{

                        top = top <= 0 ? 0 : top -= step;

                        element.stop().animate({scrollTop: top}, 400, function (){
                            wheel = false;
                        });
                    }

                    return false;
                });

                $(window).on('resize', function ( event ){
                    event.preventDefault();
                    viewport = $(this).height();
                });

                $(window).on('scroll', function ( event ){
                    event.preventDefault();
                    if (!wheel)
                        top = $(this).scrollTop();
                });
            }

            isScrolling = true;
            requestAnimationFrame(animationLoop);
        }

        // Listener for the DOMMouseScroll event.
        function onDOMMouseScroll (){
            isScrolling = true;
            requestAnimationFrame(animationLoop);
        }

        // The controller for the scroll event and mouse scroll normalisation
        function animationLoop (){

            if (isScrolling){
                setTranslate3DTransform();
                isScrolling = false;
            }
            requestAnimationFrame(animationLoop);
        }

        // Initializes the rest of the script after the image has been drawn to the previously created canvas.
        function init() {

            setOverlay();
            setStyle();
            setInitialPosition();
            setTranslate3DTransform();

            document.addEventListener('scroll', onScroll, false);
            document.addEventListener('mousewheel', onMouseWheel, true);
            document.addEventListener('DOMMouseScroll', onDOMMouseScroll, false);
        }

        // Kicks off the script if the parallax is possible and the option is enabled.
        if (parallax.possible && parallax.enabled){

            createImageContainer();

            window.onload = function (){

                var dsCanvas = document.getElementById('cbp_image_container');
                var dsCanvasContext = dsCanvas.getContext('2d');
                var img = new Image();

                img.onload = function (){

                    dsCanvasContext.drawImage(img, 0, 0, image.width, image.height);

                    init();
                };

                img.src = image.src;
            }
        }

    });


    $(window).resize(function (){

        imageContainer = $('#cbp_image_container');

        var isResizing = true;

        // The controller for the scroll event and mouse scroll normalisation
        function animationLoop (){

            if (isResizing){
                setTranslate3DTransform();
                isResizing = false;
            }
            requestAnimationFrame(animationLoop);
        }

        animationLoop();
    });

})(jQuery);
