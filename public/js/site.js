$(document).ready(function() {

    $("[title]").tooltip();

    $('.slide').click(function(event) {
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
    });
    $('.carousel').each(function() {
        $(this).owlCarousel({
            autoPlay : $(this).data('interval'),
            stopOnHover : true,
            navigation:false,
            lazyLoad : true,
            slideSpeed : 800,
            paginationSpeed : 2000,
            pagination: true,
            goToFirstSpeed : 2000,
            singleItem : true,
            autoHeight : $(this).data('autoheight'),
            transitionStyle:"fadeUp"
        });

    });
    
    $('.notifications').notify({
        fadeOut: {
            enabled: true,
            delay: 5000
        },
        type: 'bangTidy'
    }).show();
});

