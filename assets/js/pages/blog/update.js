$( document ).ready(function ()
{
    $.app.editorTinymce()
    $.app.uploadImagePreview()

    $( document ).on('imageIsValid', 'input[type="file"]', function ( event )
    {
        let self = event.detail.self;
        let container = event.detail.container;

        container.find('> figure').remove();
        container.prepend('<figure class="m-0"><img class="img-fluid" src="'+ event.detail.image +'"></figure>');
    });

    $('form[name="update_article"]').ajaxSubmit({
        callback: function( response )
        {
            if ( response.status == 'fatal_error' )
                alertify.error(response.message);

            if ( response.status == 'OK' )
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
        }
    });
});
