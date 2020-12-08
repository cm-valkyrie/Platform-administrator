"use strict";

$( document ).ready(function ()
{
    $('#users_update [button-submit]').ajaxSubmit({
        url: 'index.php?c=users&m=update_data_user',
        typeSend: 'click',
        formSubmit: $('form[name="users_update"]'),
        callback: function( response )
        {
            swal({
                title: 'Se actualizó el usuario.',
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
