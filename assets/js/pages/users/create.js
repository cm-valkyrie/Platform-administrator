"use strict";

$( document ).ready(function ()
{
    $('#users_create [button-submit]').ajaxSubmit({
        url: 'index.php?c=users&m=create_user',
        typeSend: 'click',
        formSubmit: $('form[name="users_create"]'),
        callback: function( response )
        {
            swal({
                title: 'Se agregó el usuario.',
                type: 'success',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                preConfirm: function ()
                {
                    return new Promise(function (resolve)
                    {
                        window.location.href = response.redirect;

                        setTimeout(function ()
                        {
                            resolve();
                        }, 5000);
                    });
                }
            });
        }
    });

    $('form[name="create_permission"]').ajaxSubmit({
        url: 'index.php?c=users&m=create_permission',
        callback: function( response )
        {
            swal({
                title: 'Se agregó el permiso.',
                type: 'success',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                preConfirm: function ()
                {
                    return new Promise(function (resolve)
                    {
                        window.location.href = response.redirect;

                        setTimeout(function ()
                        {
                            resolve();
                        }, 5000);
                    });
                }
            });
        }
    });
});
