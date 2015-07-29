var site = (function($) {

    var $win = $(window);

    var width = function() {
        return $win.width();
    };

    var height = function() {
        return $win.height();
    };

    var debounce = function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;

            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };

            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    var debouncedResize = function() {
        width();
        height();
    };

    var resizeEvents = debounce(debouncedResize,250);

    if (window.addEventListener) {
        window.addEventListener('resize',resizeEvents);
    } else {
        window.attachEvent('resize',resizeEvents);
    }

    return {
        $win: $win,
        width: width,
        height: height
    };

})(jQuery);