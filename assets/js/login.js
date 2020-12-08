"use strict";

$( document ).ready(function ()
{
    $('form[name="login"]').ajaxSubmit({
        callback: function ()
        {
            location.reload()
        }
    });
});
