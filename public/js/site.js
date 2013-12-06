$(document).ready(function() {

    $( "[title]" ).tooltip();

    document.getElementById('links').onclick = function(event) {
        event = event || window.event;
        var target = event.target || event.srcElement,
                link = target.src ? target.parentNode : target,
                options = {
                    index: link,
                    event: event,
                    fullScreen: true,
                    stretchImages: true,
                    slideshowInterval: 4000,
                },
                links = this.getElementsByTagName('a');
        blueimp.Gallery(links, options);
    };
});