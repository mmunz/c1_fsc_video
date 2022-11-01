/* js for c1_fsc_video */
let yTLoaded = false;
let vimeoLoaded = false;

function youtubeLoaded() {
    let scripts = document.getElementsByTagName("script");
    for (let i = 0, n = scripts.length; i < n; i++) {
        if (scripts[i]['src'] === 'https://www.youtube.com/iframe_api') {
            yTLoaded = true;
            return true;
        }
    }
    return false;
}

function vimeoApiLoaded() {
    let scripts = document.getElementsByTagName("script");
    for (let i = 0, n = scripts.length; i < n; i++) {
        if (scripts[i]['src'] === 'https://player.vimeo.com/api/player.js') {
            vimeoLoaded = true;
            return true;
        }
    }
    return false;
}

function youtubePlayer(wrapper) {

    if (!youtubeLoaded()) {
        let scripttag = document.createElement("script");
        scripttag.src = "https://www.youtube.com/iframe_api";
        let firstScriptTag = document.getElementsByTagName("script")[0];
        firstScriptTag.parentNode.insertBefore(scripttag, firstScriptTag);
    }

    let player;
    let wrapperId = wrapper.getAttribute('id');
    let videoId = wrapper.getAttribute('data-videoid');
    let legalInfo = wrapper.querySelector('.c1-fsc-video__legal');
    let playerOptions = {
        height: wrapper.clientHeight,
        width: wrapper.clientWidth,
        videoId: videoId,
        events: {
            'onReady': onPlayerReady
        }
    };

    if (yTLoaded) {
        player = new YT.Player(wrapperId + '-player', playerOptions);
    } else {
        window.onYouTubeIframeAPIReady = function () {
            player = new YT.Player(wrapperId + '-player', playerOptions);
        };
    }

    // player is ready
    function onPlayerReady(event) {
        document.getElementById(wrapperId).classList.remove("c1-fsc-video--loading");
        document.getElementById(wrapperId).classList.add("c1-fsc-video--initialized");
        legalInfo.classList.add("c1-fsc-video__legal--hidden");
        event.target.playVideo();
    }
}

function vimeoPlayer(wrapper) {

    if (!vimeoApiLoaded()) {
        let scripttag = document.createElement("script");
        scripttag.src = "https://player.vimeo.com/api/player.js";
        let firstScriptTag = document.getElementsByTagName("script")[0];
        firstScriptTag.parentNode.insertBefore(scripttag, firstScriptTag);
        scripttag.addEventListener("load", function (event) {
            let load_event = new Event('onVimeoIframeAPIReady');
            document.dispatchEvent(load_event, {bubbles: true});
        });
    }

    let player;
    let playerId = wrapper.querySelector('.video-player').getAttribute('id');
    let videoId = wrapper.getAttribute('data-videoid');
    let legalInfo = wrapper.querySelector('.c1-fsc-video__legal');
    let iframeSrc = document.createElement("iframe");
    iframeSrc.setAttribute('id', playerId + '_iframe');
    iframeSrc.setAttribute('src', 'https://player.vimeo.com/video/' + videoId + '?autoplay=1&playerId=' + playerId + '_iframe');
    iframeSrc.setAttribute('frameborder', '0');
    iframeSrc.setAttribute('allow', 'autoplay; encrypted-media');
    iframeSrc.setAttribute('webkitAllowFullScreen', '1');
    iframeSrc.setAttribute('mozallowfullscreen', '1');
    iframeSrc.setAttribute('allowfullscreen', '1');
    iframeSrc.setAttribute('width', '100%');
    iframeSrc.setAttribute('height', '100%');

    wrapper.appendChild(iframeSrc);

    if (vimeoLoaded) {
        player = new Vimeo.Player(iframeSrc);
        initPlayer();
    } else {
        document.addEventListener("onVimeoIframeAPIReady", function (event) { // (1)
            player = new Vimeo.Player(iframeSrc);
            initPlayer();
        });
    }

    function initPlayer() {
        wrapper.classList.remove("c1-fsc-video--loading");
        wrapper.classList.add("c1-fsc-video--initialized");
        legalInfo.classList.add("c1-fsc-video__legal--hidden");
    }
}

document.addEventListener("DOMContentLoaded", function (event) {
    let playButtons = document.getElementsByClassName('c1-fsc-video__play');
    Array.prototype.forEach.call(playButtons, function (el) {
        el.onclick = function () {
            let wrapper = el.closest(".c1-fsc-video");
            let provider = wrapper.getAttribute('data-provider');
            wrapper.classList.add('c1-fsc-video--loading');
            if (provider === 'youtube') {
                youtubePlayer(wrapper);
            } else if (provider === 'vimeo') {
                vimeoPlayer(wrapper);
            }
        };
    });
});
