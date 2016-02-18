$(document).ready(function(){
    var domSetting  = global.admin_template == 'RDash'
                    ? "<'row'<'col-md-12'<'widget'<'widget-title'<'row'<'col-md-5'<'#icon-wrapper'>><'col-md-3 hidden-xs'l><'col-md-4 hidden-xs'f>><'clearfix'>><'widget-body flow no-padding'tr><'widget-title'<'col-sm-5'i><'col-sm-7'p><'clearfix'>>>>"
                    : "<'row'<'col-md-12'<'box box-primary'<'box-header with-border'<'row'<'col-md-6'<'#icon-wrapper'>><'col-md-3 hidden-xs'l><'col-md-3 hidden-xs'f>>> <'box-body no-padding'tr><'box-footer clearfix'<'col-sm-5'i><'col-sm-7'p>>>>>";
    var $datatable = $('#company_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "dom" : domSetting,
        /** column definition for action column */
        "columnDefs": [
            {
                "targets": -1,
                "className": 'text-center',
                "orderable": false,
                "searchable": false
            }
        ],
        "language": {
            "paginate": {
                "previous": "&laquo;",
                "next": "&raquo;"
            }
        },
        "ajax": {
            "url": datatableUrl,
            "type": "POST",
            "error": function(resp){
                if(resp.status == 401){
                    alert('Your session is expired!\n\nYou will be redirected to the login page shortly.');
                    window.location.reload();
                }
            }
        }
    }).on('click', '.btn-company-edit', function(e){
        e.preventDefault();

        var btn = $(this);
        var url = btn.attr('href');

        btn.prop('disabled', true).text('loading...');

        $('#company_form').attr('action', url);
        $('#company_form')[0].reset();

        $.ajax({
            'method' : 'GET',
            'url' : url,
            'success' : function(fields){
                var elem;

                for(var a in fields){
                    elem = $('#'+a+'_field');
                    elem.val(fields[a]);
                }

                $('#logo_pic_preview').attr('src', fields.logo ? global.base_url+ 'assets/img/logo/' + fields.logo : '');
                $('#_method_field').val('PUT');
                $('#company_form_modal').modal('show');

                btn.prop('disabled', false).text('edit');
            },
            'error' : function(resp){
                if(resp.status == 401){
                    alert('Your session is expired!\n\nYou will be redirected to the login page shortly.');
                    window.location.reload();
                } else {
                    alert('Error occured while trying to get data!');
                    btn.prop('disabled', false).text('edit');
                }
            }
        });

    }).on('click', '.btn-company-show', function(e){
        e.preventDefault();

        var btn = $(this);
        var url = btn.attr('href');

        btn.prop('disabled', true).text('loading...');
        $.ajax({
            'method' : 'GET',
            'url' : url,
            'success' : function(fields){
                var elem;

                for(var a in fields){
                    elem = $('#'+a+'_show_field');
                    elem.html(fields[a]);
                }

                $('#company_logo_field').attr('src', fields.logo ? global.base_url+ 'assets/img/logo/' + fields.logo : '');
                $('#company_show_modal').modal('show');

                btn.prop('disabled', false).text('show');
            },
            'error' : function(resp){
                if(resp.status == 401){
                    alert('Your session is expired!\n\nYou will be redirected to the login page shortly.');
                    window.location.reload();
                } else {
                    alert('Error occured while trying to get data!');
                    btn.prop('disabled', false).text('edit');
                }
            }
        });

    }).on('click', '.btn-company-delete', function(e){
        e.preventDefault();

        if(confirm('Are you sure want to delete this data?')){
            var btn = $(this);
            var url = btn.attr('href');

            btn.prop('disabled', true).text('deleting...');

            $.ajax({
                'method' : 'DELETE',
                'url' : url,
                'success' : function(){
                    $datatable.ajax.reload(null, false);
                },
                'error' : function(resp){
                    if(resp.status == 401){
                        alert('Your session is expired!\n\nYou will be redirected to the login page shortly.');
                        window.location.reload();
                    } else {
                        alert('Error occured when deleting data');
                        btn.prop('disabled', false).text('delete');
                    }
                }
            });
        }
    });

    $('#logo_pic_container').click(function(e){
        $('#logo_pic').click();
    });

    $('#logo_pic').change(function(){
        var reader  = new FileReader();
        var preview = $('#logo_pic_preview');
        var file    = this.files[0];

        reader.onloadend = function () {
            preview.attr('src', reader.result);
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.attr('src', '');
        }
    });

    $('#btn-company-save').click(function(e) {
        e.preventDefault();

        var btn = $(this);
        var formData = new FormData($('#company_form')[0]);

        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            'method' : 'POST',
            'url' : $('#company_form').attr('action'),
            'data' : formData,
            'cache' : false,
            'contentType' : false,
            'processData' : false,
            'success' : function(resp){
                alert(resp.content);

                btn.prop('disabled', false).text('Save');

                $('#company_form_modal').modal('hide');
                $datatable.ajax.reload(null, false);
            },
            'error': function(resp){
                btn.prop('disabled', false).text('Save');

                if(resp.status == 401){
                    alert('Your session is expired!\n\nYou will be redirected to the login page shortly.');
                    window.location.reload();
                } else {
                    alert(resp.responseJSON.content + '\n\nDetailed error:\n' + resp.responseJSON.errors[0].message);
                }
            }
        });
    });

    $('#btn-company-create')
    .appendTo('#icon-wrapper')
    .click(function(e){
        e.preventDefault();
        $('#company_form')[0].reset();

        $('#company_form').attr('action', actionUrl);
        $('#_method_field').val('POST');
        $('#logo_pic_preview').attr('src', '');
        $('#id').val('');

        $('#company_form_modal').modal('show');
    });
});