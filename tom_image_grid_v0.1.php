<?php

// This is a PLUGIN TEMPLATE for Textpattern CMS.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'tom_image_grid';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.1';
$plugin['author'] = 'Thomas Jund';
$plugin['author_uri'] = 'http://sacripant.fr';
$plugin['description'] = 'An optionnal grid display for images tab';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public              : only on the public side of the website (default)
// 1 = public+admin        : on both the public and admin side
// 2 = library             : only when include_plugin() or require_plugin() is called
// 3 = admin               : only on the admin side (no AJAX)
// 4 = admin+ajax          : only on the admin side (AJAX supported)
// 5 = public+admin+ajax   : on both the public and admin side (AJAX supported)
$plugin['type'] = '3';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '';

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
// Syntax:
// ## arbitrary comment
// #@event
// #@language ISO-LANGUAGE-CODE
// abc_string_name => Localized String

/** Uncomment me, if you need a textpack
$plugin['textpack'] = <<< EOT
#@admin
#@language en-gb
abc_sample_string => Sample String
abc_one_more => One more
#@language de-de
abc_sample_string => Beispieltext
abc_one_more => Noch einer
EOT;
**/
// End of textpack

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
if (@txpinterface == 'admin') {
	register_callback( 'tom_image_grid_markup', "image");
	register_callback( 'tom_image_grid_css', "image");
	register_callback( 'tom_image_grid_js', "image");
}

function tom_image_grid_markup()
{
        $out = <<<HTML

<form id="imageDisplayOptions">
    <label><input type="radio" name="imageDisplayOption" id="grid-display" /><span class="icon-grid">Grid</span></label>
    <label><input type="radio" name="imageDisplayOption" id="line-display" /><span class="icon-line">Line</span></label>
</form>
HTML;
	echo $out;
}

function tom_image_grid_css()
{
        $sortby = gTxt('sort_by');
        $out = <<<CSS
<style type="text/css">
/*
** CSS for grid in textpattern image tab
** By Sacripant — Thomas Jund
** http://sacripant.fr
*/

.grid-display .txp-list * {
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.grid-display .txp-listtables {
    display: block;
}

.grid-display#page-image .txp-list thead th {
    /*display: none;*/
    display: inline-block;
    position: relative;
    width: auto;

}
.grid-display#page-image .txp-list thead th.images_detail {
    display: none !important;
}
.grid-display#page-image .txp-list thead th.multi-edit:after {
    content: attr(title);
}

.grid-display#page-image .txp-list thead th.id {
    margin-left: 1em;
}
.grid-display#page-image .txp-list thead th.id:before {
    content: "{$sortby}";
    display: block;
    position: absolute;
    bottom: 100%;
    left: 0;
    line-height: 2;
    white-space: pre;
}


.grid-display#page-image .txp-list tbody tr {
    display: inline-block;
    /*vertical-align: mid;*/
    width: 15em;
    margin: .66em 1em 0 0
}

.grid-display#page-image .txp-list td {
    display: block;
    padding: 0.16em 0.33em;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

.grid-display#page-image .txp-list td.multi-edit {
    float: right;
    width: auto;
}
.grid-display#page-image .txp-list td.multi-edit input {
    margin-bottom: 0;
}

.grid-display#page-image .txp-list td.thumbnail {
    height: 12em;
}

.grid-display#page-image .txp-list td.thumbnail img {
    max-width: 100%;
    max-height: 100%;
    height: auto;
    width: auto;
}

@media only screen and (max-width: 480px) {
    .grid-display#page-image .txp-list tbody tr {
        width: 140px;
        margin: .66em .33em 0 0;
    }


}

/*buttons
----------*/

#imageDisplayOptions input {
    display: none;
}

#imageDisplayOptions label {
    padding: .16em;
    display: inline-block;
    border: 1px solid transparent;
    width: 20px;
    height: 20px;
    overflow: hidden;
}
#imageDisplayOptions label.selected {
    border-color: #aaa;
}
.icon-grid:before {
    content: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIKCSBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjAgMjAiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8ZyBpZD0iZ3JpZCI+Cgk8Zz4KCQk8cmVjdCB4PSIzIiB5PSIzIiB3aWR0aD0iNS42MDEiIGhlaWdodD0iNS42Ii8+CgkJPHJlY3QgeD0iMyIgeT0iMTEuMzk5IiB3aWR0aD0iNS42MDEiIGhlaWdodD0iNS42MDEiLz4KCQk8cmVjdCB4PSIxMS4zOTkiIHk9IjExLjM5OSIgd2lkdGg9IjUuNjAxIiBoZWlnaHQ9IjUuNjAxIi8+CgkJPHJlY3QgeD0iMTEuMzk5IiB5PSIzIiB3aWR0aD0iNS42MDEiIGhlaWdodD0iNS42Ii8+Cgk8L2c+CjwvZz4KPC9zdmc+Cg==");
}
.icon-line:before {
    content: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIKCSBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjAgMjAiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8ZyBpZD0ibGluZSI+Cgk8cGF0aCBkPSJNMy44MTEsMTQuMTA3djIuODg5SDAuODQ0di0yLjg4OUgzLjgxMXogTTAuODQ0LDguNTU1djIuODg2aDIuOTY3VjguNTU1SDAuODQ0eiBNMTkuMTU1LDExLjQ0MVY4LjU1NUg2LjE2OXYyLjg4NkgxOS4xNTV6CgkJIE0xOS4xNTUsMTYuOTk2di0yLjg4OUg2LjE2OXYyLjg4OUgxOS4xNTV6IE0wLjg0NCwzdjIuODg5aDIuOTY3VjNIMC44NDR6IE0xOS4xNTUsNS44ODlWM0g2LjE2OXYyLjg4OUgxOS4xNTV6Ii8+CjwvZz4KPC9zdmc+Cg==");
}
</style>
CSS;
	echo $out;
}

function tom_image_grid_js()
{
    $out = <<<JS

<script type="text/javascript">
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
</script>

JS;
    echo $out;
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. tom_image_grid

p. _tom_image_grid_ is a plugin for "Textpattern 4.5.* CMS":textpattern. It allows a more compact display (as a grid) of the images list.

p. The plugin adds two buttons 
!data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIKCSBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjAgMjAiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8ZyBpZD0iZ3JpZCI+Cgk8Zz4KCQk8cmVjdCB4PSIzIiB5PSIzIiB3aWR0aD0iNS42MDEiIGhlaWdodD0iNS42Ii8+CgkJPHJlY3QgeD0iMyIgeT0iMTEuMzk5IiB3aWR0aD0iNS42MDEiIGhlaWdodD0iNS42MDEiLz4KCQk8cmVjdCB4PSIxMS4zOTkiIHk9IjExLjM5OSIgd2lkdGg9IjUuNjAxIiBoZWlnaHQ9IjUuNjAxIi8+CgkJPHJlY3QgeD0iMTEuMzk5IiB5PSIzIiB3aWR0aD0iNS42MDEiIGhlaWdodD0iNS42Ii8+Cgk8L2c+CjwvZz4KPC9zdmc+Cg==! !data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjBweCIKCSBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjAgMjAiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8ZyBpZD0ibGluZSI+Cgk8cGF0aCBkPSJNMy44MTEsMTQuMTA3djIuODg5SDAuODQ0di0yLjg4OUgzLjgxMXogTTAuODQ0LDguNTU1djIuODg2aDIuOTY3VjguNTU1SDAuODQ0eiBNMTkuMTU1LDExLjQ0MVY4LjU1NUg2LjE2OXYyLjg4NkgxOS4xNTV6CgkJIE0xOS4xNTUsMTYuOTk2di0yLjg4OUg2LjE2OXYyLjg4OUgxOS4xNTV6IE0wLjg0NCwzdjIuODg5aDIuOTY3VjNIMC44NDR6IE0xOS4xNTUsNS44ODlWM0g2LjE2OXYyLjg4OUgxOS4xNTV6Ii8+CjwvZz4KPC9zdmc+Cg==!
in the Image Tab of your back-office allowing you to switch from the standard display to a more compact grid display.
# --- END PLUGIN HELP ---
-->
<?php
}
?>