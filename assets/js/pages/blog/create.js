$( document ).ready(function ()
{
    $.app.createUrl()
    $.app.editorTinymce()
    $.app.uploadImagePreview()

    $('form[name="create_article"]').ajaxSubmit({
        callback: function( response )
        {
            swal({
                title: 'Se creó el artículo.',
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
