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

(function ($) {
    "use strict";

    /**
     * requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel
     http://paulirish.com/2011/requestanimationframe-for-smart-animating/
     http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating
     MIT license
     */
    (function () {
        var lastTime = 0;
        var vendors = ['ms', 'moz', 'webkit', 'o'];
        for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
            window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
            window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
                || window[vendors[x] + 'CancelRequestAnimationFrame'];
        }

        if (!window.requestAnimationFrame)
            window.requestAnimationFrame = function (callback, element) {
                var currTime = new Date().getTime();
                var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                var id = window.setTimeout(function () {
                        callback(currTime + timeToCall);
                    },
                    timeToCall);
                lastTime = currTime + timeToCall;
                return id;
            };

        if (!window.cancelAnimationFrame)
            window.cancelAnimationFrame = function (id) {
                clearTimeout(id);
            };
    }());

    var defaultOptions = {
        imageSrc: '',
        backgroundColor: '',
        positionX: 'center',
        positionY: 'center',
        backgroundAttachment: 'fixed',

        parallaxPossible: false,
        parallaxEnabled: false,
        direction: 'vertical',
        verticalScrollDirection: 'top',
        horizontalScrollDirection: 'left',
        horizontalAlignment: 'center',
        verticalAlignment: 'center',
        overlayImage: 'none',
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

    var scrolling = {preserved: cbParallax.preserveScrolling != 'undefined' ? cbParallax.preserveScrolling : false};

    var image = {
        src: cbParallax.imageSrc != 'undefined' ? cbParallax.imageSrc : defaultOptions.imageSrc,
        backgroundColor: cbParallax.backgroundColor != 'undefined' ? cbParallax.backgroundColor : defaultOptions.backgroundColor,
        positionX: cbParallax.position_x != 'undefined' ? cbParallax.position_x : defaultOptions.positionX,
        positionY: cbParallax.position_y != 'undefined' ? cbParallax.position_y : defaultOptions.positionX,
        backgroundAttachment: cbParallax.backgroundAttachment != 'undefined' ? cbParallax.backgroundAttachment : defaultOptions.backgroundAttachment,

        width: cbParallax.imageWidth != 'undefined' ? cbParallax.imageWidth : defaultOptions.imageWidth,
        height: cbParallax.imageHeight != 'undefined' ? cbParallax.imageHeight : defaultOptions.imageHeight,
    };

    var overlay = {
        path: cbParallax.overlayPath != 'undefined' ? cbParallax.overlayPath : defaultOptions.overlayPath,
        image: cbParallax.overlayImage != 'undefined' ? cbParallax.overlayImage : defaultOptions.overlayImage,
        opacity: cbParallax.overlayOpacity != 'undefined' ? cbParallax.overlayOpacity : defaultOptions.overlayOpacity
    };

    var body = $('body');

    var html = $('html');

    var imageContainer = null;

    var overlayContainer = null;

    var requestAnimationFrame = window.mozRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        window.requestAnimationFrame;

    // The id of the image container.
    var id = 'cbp_image_container';

    var isScrolling = false;

    var isResizing = false;

    var niceScrollConfig = {
        zindex: -9999,
        scrollspeed: 120,
        mousescrollstep: 8 * 3,
        preservenativescrolling: true,
        horizrailenabled: false,
        cursordragspeed: 1.2
    };

    // Converts alignments such as left, center and right into pixel values.
    function getHorizontalAlignAsPosition() {

        var horizontalPosition = null;

        switch (parallax.horizontalAlignment) {

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
    function getVerticalAlignAsPosition() {

        var verticalPosition = null;

        switch (parallax.verticalAlignment) {

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
    function getScrollRatio() {

        var verticalDocumentOffset = null;
        var verticalImageOffset = null;
        var horizontalImageOffset = null;
        var ratio = null;

        if (parallax.direction == 'vertical') {
            verticalDocumentOffset = body.innerHeight() - $(window).innerHeight();
            verticalImageOffset = image.height - $(window).innerHeight();

            ratio = (verticalImageOffset / verticalDocumentOffset);

        } else if (parallax.direction == 'horizontal') {
            verticalDocumentOffset = body.innerHeight() - $(window).innerHeight();
            horizontalImageOffset = image.width - $(window).innerWidth();

            ratio = (horizontalImageOffset / verticalDocumentOffset);
        }

        return ratio;
    }

    // Calculates and returns the x and y values for the transformation.
    function getTransform() {

        var transform = {
            x: null,
            y: null
        };
        var ratio = getScrollRatio();
        var scrolling = $(window).scrollTop();

        // Determines the values for the transformation.
        if (parallax.direction == 'vertical') {

            if (parallax.verticalScrollDirection == 'to top') {
                transform.x = 0;
                transform.y = -scrolling * ratio;

            } else if (parallax.verticalScrollDirection == 'to bottom') {
                transform.x = 0;
                transform.y = scrolling * ratio;
            }
        } else if (parallax.direction == 'horizontal') {

            if (parallax.horizontalScrollDirection == 'to the left') {
                transform.x = -scrolling * ratio;
                transform.y = 0;

            } else if (parallax.horizontalScrollDirection == 'to the right') {
                transform.x = scrolling * ratio;
                transform.y = 0;
            }
        }

        return transform;
    }

    // Creates a container and assigns the overlay image and its opacity, if an overlay pattern is set.
    function setOverlay() {

        if (overlay.image != "none") {

            body.prepend('<div id="cbp_overlay"></div>');
            overlayContainer = $('#cbp_overlay');

            overlayContainer.css({
                'background-image': 'url(' + overlay.path + overlay.image + ')',
                'opacity': overlay.opacity
            });
        }
    }

    // Set style.
    function setStyle() {

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

    // Sets the initial background position.
    function setInitialPosition() {

        if (parallax.direction == 'vertical') {
            imageContainer.css({
                'left': getHorizontalAlignAsPosition()
            });

            if (parallax.verticalScrollDirection == 'to top') {

                imageContainer.css({
                    'top': 0
                });

            } else if (parallax.verticalScrollDirection == 'to bottom') {

                imageContainer.css({
                    'bottom': 0
                });
            }

        } else if (parallax.direction == 'horizontal') {

            imageContainer.css({
                'top': getVerticalAlignAsPosition()
            });

            if (parallax.horizontalScrollDirection == 'to the left') {

                imageContainer.css({
                    'left': 0
                });

            } else if (parallax.horizontalScrollDirection == 'to the right') {

                imageContainer.css({
                    'right': 0
                });
            }
        }
    }

    // While scrolling.
    function animationLoop() {

        if (isScrolling) {

            var transform = getTransform();
            setTranslate3DTransform(transform);
        }

        isScrolling = false;
        requestAnimationFrame(animationLoop);
    }

    // Keeps the image centered and positioned on window resize.
    function keepImageCentered() {

        if (isResizing) {

            var horizontal_align = getHorizontalAlignAsPosition();
            var vertical_align = getVerticalAlignAsPosition();

            if (parallax.direction == 'vertical' && parallax.horizontalAlignment == 'center') {

                imageContainer.css({
                    'left': horizontal_align
                });
            } else if (parallax.direction == 'horizontal' && parallax.verticalAlignment == 'center') {

                imageContainer.css({
                    'top': vertical_align
                });
            }
        }
    }

    // Animation loop on window resize.
    function animationLoopOnResize() {

        if (isResizing) {

            keepImageCentered();
            var transform = getTransform();
            setTranslate3DTransform(transform);
        }

        isResizing = false;
        requestAnimationFrame(animationLoopOnResize);
    }

    // Transformation function for repositioning the canvas.
    function setTranslate3DTransform(transform) {

        /*imageContainer*/
        $('#' + id).css({
            '-webkit-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
            '-moz-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
            '-ms-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
            '-o-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
            'transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)'
        });
    }


    $(document).ready(function () {

        // Creates the image container.
        function createImageContainer(id) {

            body.prepend('<canvas id="' + id + '" class="custom-background" width="' + image.width + '" height="' + image.height + '"><canvas>');
            imageContainer = $('#' + id);

            return imageContainer;
        }

        // Caches the image container object.
        imageContainer = createImageContainer(id);

        function preserveScrolling() {

            // Instanciates Nicescroll.
            var nice = html.niceScroll(niceScrollConfig);

            // Hides the rail and the scrollbar.
            nice.hide();

            // Forces display of the default scrollbar in IE.
            body.css('-ms-overflow-style', 'scrollbar');
        }

        // If parallax is possible and enabled, this function fires after the background image has been drawn onto the canvas.
        function init() {

            // Since we use a modified version of Nicescroll, we perform this check here: If there is no "default" Nicescroll library detected, we load the modified version.
            if ($('#ascrail2000').length == 0) {

                preserveScrolling();
            }

            setOverlay();
            setStyle();
            setInitialPosition();

            // We call this one once from here so everything is Radiohead, Everything in its right place.
            var transform = getTransform();
            setTranslate3DTransform(transform);
        }

        // Kicks off the script if...
        if (parallax.possible && parallax.enabled) {

            // This is the routine that sets the context, creates the new image and then draws the assigned background image onto the canvas.
            window.onload = function () {

                var canvas = document.getElementById(id);
                var context = canvas.getContext('2d');
                var img = new Image();

                img.onload = function () {

                    context.drawImage(img, 0, 0, image.width, image.height);

                    init();
                };

                img.src = image.src;
            }
            // ...else if scrolling is preserved, we load the modified Nicescroll library.
        } else if (scrolling.preserved) {

            preserveScrolling();
        }

    });


    $(window).on('scroll', function () {

        if (parallax.possible && parallax.enabled) {

            isScrolling = true;
            requestAnimationFrame(animationLoop);
        }
    });


    $(window).on('resize', function () {

        if (parallax.possible && parallax.enabled) {

            isResizing = true;
            requestAnimationFrame(animationLoopOnResize);
        }

    });

})(jQuery);
