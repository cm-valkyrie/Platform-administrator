$( document ).ready(function ()
{
    $.app.editorTinymce()
    $.app.uploadImagePreview()

    $('form[name="update_article"]').ajaxSubmit({
        callback: function( response )
        {
            swal({
                title: 'Se actualizó el artículo.',
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
