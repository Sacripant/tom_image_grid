$(function() {
    var imageDisplayOption = $("#imageDisplayOptions")
    ,   radios = $('input', imageDisplayOption)
    ,   listtables = $('.txp-listtables')
    ,   imagesTr = $('tr', listtables)
    ,   cookie = 'txp_ImageTabDisplay'
    ,   cookieValue = getCookie(cookie)
    ;

    // Add radios in tab
    imageDisplayOption.prependTo(listtables);


    // Change Layout when radio checked
    radios.change(function(event) 
    {
        if ( this.id === "grid-display")
        {
            $('body').addClass('grid-display');
            setCookie(cookie, 'grid-display')
        }
        else
        {
            $('body').removeClass('grid-display');
            setCookie(cookie, 'line-display')
        }

        // console.log($(this).parent());
        console.log( $(radios).filter(':checked') );

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
        var targetRadio = document.getElementById('line-display');
        setCookie(cookie, 'line-display')
    };

    $(targetRadio).prop('checked', true).trigger("change");

});