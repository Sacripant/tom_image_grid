/*
** JS for tom_image_grid plugin
** By Sacripant — Thomas Jund
** http://sacripant.fr
*/

$(function() {
    var imageDisplayOption = $("#tom_ig_options")
    ,   radios = $('input', imageDisplayOption)
    ,   listtables = $('.txp-listtables')
    ,   imagesTr = $('tr', listtables)
    ,   cookie = 'tom_image_grid'
    ,   cookieValue = getCookie(cookie)
    ;

    // Add radios in tab
    imageDisplayOption.prependTo(listtables);


    // Change Layout when radio checked
    radios.change(function(event) 
    {
        if ( this.id === "tom_ig_option--grid")
        {
            $('body').addClass('tom_ig');
            setCookie(cookie, 'tom_ig_option--grid');
        }
        else
        {
            $('body').removeClass('tom_ig');
            setCookie(cookie, 'tom_ig_option--line')
        }

        radios.parent().removeClass('selected');
        $(this).parent().addClass('selected');
    });

    // Check & create cookie
    if ( cookieValue ) 
    {
        var targetRadio = document.getElementById(cookieValue);
    }
    else
    {
        var targetRadio = document.getElementById('tom_ig_option--line');
        setCookie(cookie, 'tom_ig_option--line')
    };

    $(targetRadio).prop('checked', true).trigger("change");
});