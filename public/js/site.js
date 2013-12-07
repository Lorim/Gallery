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
    
    $('.editable').editable({
        type: $(this).data("type"),
        url: '/admin/updatenews',
        placement: 'auto',
        datepicker: {
            language : 'de'
        },
        source: '/list'
    });
    
    $('#save-btn').click(function() {
        $('.new').editable('submit', {
            url: '/admin/addnews',
            ajaxOptions: {
                dataType: 'json' //assuming json response
            },
            success: function(data, config) {
                if (data && data.id) {  //record created, response like {"id": 2}
                    //set pk
                    $(this).editable('option', 'pk', data.id);
                    //remove unsaved class
                    $(this).removeClass('editable-unsaved');
                    //show messages
                    var msg = 'New user created! Now editables submit individually.';
                    $('#msg').addClass('alert-success').removeClass('alert-error').html(msg).show();
                    $('#save-btn').hide();
                    $(this).off('save.newuser');
                } else if (data && data.errors) {
                    //server-side validation error, response like {"errors": {"username": "username already exist"} }
                    config.error.call(this, data.errors);
                }
            },
            error: function(errors) {
                var msg = '';
                if (errors && errors.responseText) { //ajax error, errors = xhr object
                    msg = errors.responseText;
                } else { //validation error (client-side or server-side)
                    $.each(errors, function(k, v) {
                        msg += k + ": " + v + "<br>";
                    });
                }
                $('#msg').removeClass('alert-success').addClass('alert-error').html(msg).show();
            }
        });
    });
    
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
});

