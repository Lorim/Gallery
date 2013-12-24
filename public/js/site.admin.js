$(document).ready(function() {

    $('.datatable').dataTable({
        "sPaginationType": "bs_normal",
        "aaSorting": [[ 1, "desc" ]],
        "bStateSave": true,
        "fnCreatedRow": function( nRow, aData, iDataIndex ) {
            editable();
          }
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

    function editable() {
        $('.editable2').editable({
            type: $(this).data("type"),
            url: '/admin/updategallery',
            placement: 'auto',
            datepicker: {
                language: 'de'
            },
            source: '/list'
        });
        $('.editable').editable({
            type: $(this).data("type"),
            url: '/admin/updatenews',
            placement: 'auto',
            datepicker: {
                language: 'de'
            },
            source: '/list'
        });
    }
    editable();

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

    $('#save-btn2').click(function() {
        $('.new').editable('submit', {
            url: '/admin/addgallery',
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
                    location.reload();
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

    $(".editPreviewPicture")
            .click(function(event) {
                event.preventDefault();

                $.ajax({
                    type: "POST",
                    url: '/admin/getpreviewpic/pk/' + $(this).data('pk')
                }).done(function(msg) {
                    msg = 'foo';
                    $("#dialog-form").html(msg);
                })

                $("#dialog-form").dialog({
                    autoOpen: false,
                    height: 400,
                    width: 680,
                    modal: false,
                    buttons: {
                        "Status wechseln": function() {
                            window.location.href = $(this).find("a").attr("href");
                        },
                        "Daten übernehmen": function() {
                            $(this).find("form").submit();
                        },
                        "Schliessen": function() {
                            $(this).dialog("close");
                        }
                    }
                });
                $("#dialog-form").dialog("open");
            });
    $(document).on('hidden', "#myModal", function() {
        $("#modal").removeData("modal");
    });
    $(document).on('click', "a[data-toggle=modal]", function() {
        var target = $(this).attr("href");
        $.get(target, function(data) {
            $("#previewModal").html(data);
            $("#previewModal").modal("show");
            $('.selectpicker').selectpicker();
        })
        .always(function() {
            $('.selectpicker').selectpicker();
        });
        
        return false;
    });
    $('.selectpicker').selectpicker();
});

