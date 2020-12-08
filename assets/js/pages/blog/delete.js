$( document ).ready(function ()
{
    $('[data-ajax-delete-article]').on('click', function()
    {
        let self = $(this);
        let message = '';
        let xhr_status = '';

        swal({
            title: 'Se eliminará el artículo.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#54cc96',
            cancelButtonColor: '#ff5560',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            preConfirm: function ()
            {
                return new Promise(function (resolve)
                {
                    $.post('index.php?c=blog&m=delete_article', { id: self.data('ajax-delete-article') }, function(data, status, jqXHR)
                    {
                        if ( data.status == 'OK' )
                        {
                            xhr_status = 'OK';
                        }
                        else
                        {
                            xhr_status = 'error';
                            message = ( !data.message ) ? 'Error' : data.message;
                        }

                        setTimeout(function ()
                        {
                            resolve();
                        }, 500);
                    });
                });
            }
        }).then(function ()
        {
            if ( xhr_status == 'OK' )
            {
                swal({
                    type: 'success',
                    title: 'Eliminado',
                    html: 'El artículo se eliminó.',
                    preConfirm: function ()
                    {
                        return new Promise(function (resolve)
                        {
                            location.reload();

                            setTimeout(function ()
                            {
                                resolve();
                            }, 5000);
                        });
                    }
                });
            }
            else
            {
                swal({
                    type: 'error',
                    title: 'Error',
                    html: message
                });
            }

        });
    });

    $('[data-ajax-delete-category]').on('click', function()
    {
        let self = $(this);
        let message = '';
        let xhr_status = '';

        swal({
            title: 'Se eliminará la categoría.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#54cc96',
            cancelButtonColor: '#ff5560',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            preConfirm: function ()
            {
                return new Promise(function (resolve)
                {
                    $.post('index.php?c=blog&m=delete_category', { id: self.data('ajax-delete-category') }, function(data, status, jqXHR)
                    {
                        if ( data.status == 'OK' )
                        {
                            xhr_status = 'OK';
                        }
                        else
                        {
                            xhr_status = 'error';
                            message = ( !data.message ) ? 'Error' : data.message;
                        }

                        setTimeout(function ()
                        {
                            resolve();
                        }, 500);
                    });
                });
            }
        }).then(function ()
        {
            if ( xhr_status == 'OK' )
            {
                swal({
                    type: 'success',
                    title: 'Se eliminó la categoría.',
                    preConfirm: function ()
                    {
                        return new Promise(function (resolve)
                        {
                            location.reload();

                            setTimeout(function ()
                            {
                                resolve();
                            }, 5000);
                        });
                    }
                });
            }
            else
            {
                swal({
                    type: 'error',
                    title: 'Error',
                    html: message
                });
            }

        });
    });
});
