/* js for c1_fsc_video */

function youtubePlayer(wrapper) {
    /* youtube iframe api */
    var player;
    var wrapperId = wrapper.attr('id');
    var videoId = wrapper.data('videoid');

    player = new YT.Player(wrapperId + '-player', {
        height: wrapper.outerHeight(),
        width: wrapper.width(),
        videoId: videoId,
        events: {
            'onReady': onPlayerReady
        }
    });

    // player is ready
    function onPlayerReady(event) {
        $('#' + wrapperId).removeClass("loading").addClass("initialized");
        // make sure video autoplay detection is included in modernizr
        // start play if the browser supports it. If not, the user has to click
        // twice.
        if ($("html").hasClass('videoautoplay')) {
            console.log("starting autoplay");
            event.target.playVideo();
        }
    }
}

function vimeoPlayer(wrapper) {
    /* add player using the cimeo api and froogaloop */
    var wrapperId = wrapper.attr('id');
    var playerId = wrapper.children('.video-player').attr('id');
    var videoId = wrapper.data('videoid');
    var player;
    var iframeSrc = $('<iframe/>', {
        id: playerId + '_iframe',
        src: 'https://player.vimeo.com/video/' + videoId + '?api=1&autoplay=1&playerId=' + playerId + '_iframe',
        frameborder: 0,
        webkitAllowFullScreen: 1,
        mozallowfullscreen: 1,
        allowFullScreen: 1
    });
    iframeSrc.attr('width', '100%').attr('height', '100%');
    wrapper.append(iframeSrc).ready(function() {
        player = $f(iframeSrc);
    });
    
    // When the player is ready, add listeners
    player.addEvent('ready', function () {
        wrapper.removeClass("loading").addClass("initialized");
        // make sure video autoplay detection is included in modernizr
        if ($("html").hasClass('videoautoplay')) {
            player.api("play");
        };
        // player.addEvent('pause', onPause);
        // player.addEvent('finish', onFinish);
        // player.addEvent('playProgress', onPlayProgress);
    });
}

$(function () {
    $('.fsc-video-play').each(function () {
        $(this).click(function () {
            var wrapper = $(this).parent('.c1-fsc-video');
            var provider = wrapper.data('provider');
            wrapper.addClass('loading');
            if (provider === 'youtube') {
                console.log("starting youtube play for wrapper id " + wrapper.attr('id'));
                youtubePlayer(wrapper);
            } else if (provider === 'vimeo') {
                console.log("starting vimeo play for wrapper id " + wrapper.attr('id'));
                vimeoPlayer(wrapper);
            }
        });
    });
});