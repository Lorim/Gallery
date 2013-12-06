$(document).ready(function() {

    $("[title]").tooltip();

    $('.datatable').dataTable({
        "sPaginationType": "bs_normal"
    });
    $('.datatable').each(function() {
        var datatable = $(this);
        // SEARCH - Add the placeholder for Search and Turn this into in-line form control
        var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
        search_input.attr('placeholder', 'Search');
        search_input.addClass('form-control input-sm');
        // LENGTH - Inline-Form control
        var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
        length_sel.addClass('form-control input-sm');
    });
    
    $('.editable.text').editable({
        type: 'text',
        url: '/admin/updatenews',
        placement: 'top',
        source: '/list'
    });
    $('.editable.textarea').editable({
        type: 'textarea',
        url: '/admin/updatenews',
        placement: 'top',
        source: '/list'
    });
    $('#links').click(function(event) {
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
});

