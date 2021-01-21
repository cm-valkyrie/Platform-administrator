"use strict";

$( document ).ready(function ()
{
    $('#users_update [button-submit]').ajaxSubmit({
        url: 'index.php?c=users&m=update_data_user',
        typeSend: 'click',
        formSubmit: $('form[name="users_update"]'),
        callback: function( response )
        {
            if ( response.status == 'fatal_error' )
                alertify.error(response.message);

            if ( response.status == 'OK' )
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
        }
    });
});
