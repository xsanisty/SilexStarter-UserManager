$(document).ready(function(){
    var domSetting  = global.admin_template == 'RDash'
                    ? "<'row'<'col-md-12'<'widget'<'widget-title'<'row'<'col-md-5'<'#icon-wrapper'>><'col-md-3 hidden-xs'l><'col-md-4 hidden-xs'f>><'clearfix'>><'widget-body flow no-padding'tr><'widget-title'<'col-sm-5'i><'col-sm-7'p><'clearfix'>>>>"
                    : "<'row'<'col-md-12'<'box box-primary'<'box-header'<'row'<'col-md-6'<'#icon-wrapper'>><'col-md-3 hidden-xs'l><'col-md-3 hidden-xs'f>><> <'box-body no-padding'tr><'box-footer clearfix'<'col-sm-5'i><'col-sm-7'p>>>>>";
    var $datatable = $(datatableId).DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "dom" : domSetting,
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
    }).on('click', '.btn-edit', function(e){
        e.preventDefault();

        var btn = $(this);
        var url = btn.attr('href');

        btn.prop('disabled', true).text('loading...');

        $(formId)[0].reset();

        $.ajax({
            'method' : 'GET',
            'url' : url,
            'success' : function(fields){
                var elem;

                btn.prop('disabled', false).text('edit');

                for(var a in fields){
                    elem = $('#'+a);

                    if( elem.length > 0 && elem.prop('tagName').toLowerCase() == 'select') {
                        if (a == 'activated') {
                            elem.val(fields[a] ? 1 : 0);
                        }

                        if (a == 'permissions') {
                            var perms = [];

                            for(var perm in fields[a]){
                                perms.push(perm);
                            }

                            elem.val(perms);
                        }

                        if (a == 'groups') {
                            var groups = [];

                            for(var group in fields[a]){
                                groups.push(fields[a][group].id);
                            }

                            elem.val(groups);
                        }

                    } else if('profile_pic' == a){
                        $('#profile-pic-preview').attr('src', fields[a] ? global.base_url+ 'assets/img/profile/' + fields[a] : '');
                    } else {
                        elem.val(fields[a]);
                    }
                }

                $('#_method').val('PUT');
                $('.selectpicker').selectpicker('render');

                $(modalId).modal('show');
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

    }).on('click', '.btn-delete', function(e){
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

    $('#profile-pic-container').click(function(e){
        $('#profile_pic').click();
    });

    $('#profile_pic').change(function(){
        var reader  = new FileReader();
        var preview = $('#profile-pic-preview');
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

    $('#btn-save').click(function(e) {
        e.preventDefault();

        var btn = $(this);
        var formData = new FormData($(formId)[0]);

        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            'method' : 'POST',
            'data' : $(formId).serialize(),
            'url' : $('#_method').val() == 'PUT' ? $(formId).attr('action') + $('#id').val() : $(formId).attr('action'),
            'data' : formData,
            'cache' : false,
            'contentType' : false,
            'processData' : false,
            'success' : function(resp){
                alert(resp.content);

                btn.prop('disabled', false).text('Save');

                $(modalId).modal('hide');
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
});