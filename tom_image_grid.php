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

$plugin['version'] = '0.4';
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
    register_callback( 'tom_image_grid_markup', 'image_ui', 'extend_controls');
    register_callback( 'tom_image_grid_css', "image");
    register_callback( 'tom_image_grid_js', "image");
}

function tom_image_grid_markup()
{
    $out = <<<HTML

<form id="tom_ig_options">
    <label><input type="radio" name="tom_ig_option" id="tom_ig_option--grid" /><span class="tom_ig_icon-grid">Grid</span></label>
    <label><input type="radio" name="tom_ig_option" id="tom_ig_option--line" /><span class="tom_ig_icon-line">Line</span></label>
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
** CSS for tom_image_grid plugin
** By Sacripant — Thomas Jund
** http://sacripant.fr
*/

.tom_ig .txp-list * {
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.tom_ig .txp-listtables {
    display: block;
}

.tom_ig#page-image .txp-list thead th {
    /*display: none;*/
    display: inline-block;
    position: relative;
    width: auto;
    
}
.tom_ig#page-image .txp-list thead th.images_detail {
    display: none !important;
}
.tom_ig#page-image .txp-list thead th.txp-list-col-multi-edit {
    margin-right: 1em;
}
.tom_ig#page-image .txp-list thead th.txp-list-col-multi-edit:after {
    content: attr(title);
    padding-left: .33em;
}

.tom_ig#page-image .txp-list thead th.txp-list-col-id {
    margin-left: 3.6em;
}
.tom_ig#page-image .txp-list thead th.txp-list-col-id:before {
    content: "Sort by";
    display: block;
    padding: inherit;
    padding-left: 0;
    padding-right: .33em;

    position: absolute;
    width: 3.6em;
    right: 100%;
    top: 0;

    text-align: right;
    font-weight: normal;
}


.tom_ig#page-image .txp-list tbody tr {
    display: inline-block;
    /*vertical-align: mid;*/
    width: 15em;
    margin: .66em 1em 0 0
}

.tom_ig#page-image .txp-list tbody td,
.tom_ig#page-image .txp-list tbody th {
    display: block;
    padding: 0.16em 0.33em;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

.tom_ig#page-image .txp-list td.txp-list-col-multi-edit {
    float: right;
    width: auto;
}
.tom_ig#page-image .txp-list td.txp-list-col-multi-edit input {
    margin-bottom: 0;
}

.tom_ig#page-image .txp-list td.txp-list-col-thumbnail {
    height: 12em;
}

.tom_ig#page-image .txp-list td.txp-list-col-thumbnail img {
    max-width: 100%;
    max-height: 100%;
    height: auto;
    width: auto;
}

/* No thumbnail display */
.tom_ig#page-image .txp-list td.txp-list-col-thumbnail:not(.has-thumbnail) {
    display: block;
    background-color: #eee;
    text-align: center;

    height: 100%;
    width: 100%;
    border-width: 5.25em 5.5em;
    border-style: solid;
    border-top-color: #eee;
    border-bottom-color: #eee;
    border-left-color: #ddd;
    border-right-color: #ddd;
}



@media only screen and (max-width: 480px) {
    .tom_ig#page-image .txp-list tbody tr {
        width: 140px;
        margin: .66em .33em 0 0;
    }

    .tom_ig#page-image .txp-list td.thumbnail {
        height: 112px;
    }

    .tom_ig .tom_ig--noThumb {
        border-width: 45px 40px;        
    }


}

/*buttons
----------*/

#tom_ig_options input {
    display: none;
}

#tom_ig_options label {
    padding: .16em;
    display: inline-block;
    border: 1px solid transparent;
    width: 20px;
    height: 20px;
    overflow: hidden;
}
#tom_ig_options label.selected {
    border-color: #aaa; 
}
#tom_ig_options label:not(.selected):hover {
    cursor: pointer;
    background-color: #eee;
}
.tom_ig_icon-grid:before {
    content: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyMHB4IiBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8ZyBpZD0iZ3JpZCIgc3R5bGU9ImJhY2tncm91bmQtY29sb3I6ICMzMzMiPg0KICAgIDxyZWN0IHg9IjMiICB5PSIzIiAgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KICAgIDxyZWN0IHg9IjMiICB5PSIxMiIgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KICAgIDxyZWN0IHg9IjEyIiB5PSIxMiIgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KICAgIDxyZWN0IHg9IjEyIiB5PSIzIiAgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KPC9nPg0KPC9zdmc+DQo=");
}
.tom_ig_icon-line:before {
    content: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyMHB4IiBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8ZyBpZD0ibGluZSI+DQogICAgPHJlY3QgeD0iMSIgeT0iMTUiIHdpZHRoPSIzIiBoZWlnaHQ9IjMiLz4NCiAgICA8cmVjdCB4PSIxIiB5PSI5IiAgd2lkdGg9IjMiIGhlaWdodD0iMyIvPg0KICAgIDxyZWN0IHg9IjEiIHk9IjMiICB3aWR0aD0iMyIgaGVpZ2h0PSIzIi8+DQogICAgPHJlY3QgeD0iNiIgeT0iMTUiIHdpZHRoPSIxMyIgaGVpZ2h0PSIzIi8+DQogICAgPHJlY3QgeD0iNiIgeT0iOSIgIHdpZHRoPSIxMyIgaGVpZ2h0PSIzIi8+DQogICAgPHJlY3QgeD0iNiIgeT0iMyIgIHdpZHRoPSIxMyIgaGVpZ2h0PSIzIi8+DQo8L2c+DQo8L3N2Zz4NCg==");
}

</style>
CSS;
    echo $out;
}

function tom_image_grid_js()
{
    $out = <<<JS

<script type="text/javascript">
/*
** JS for tom_image_grid plugin
** By Sacripant — Thomas Jund
** http://sacripant.fr
*/

$(function() {
    var imageDisplayOption = $("#tom_ig_options")
    ,   radios = $('input', imageDisplayOption)
    ,   listtables = $('#images_form .txp-listtables')
    /*,   imagesTr = $('tr', listtables)*/
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
</script>

JS;
    echo $out;
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. tom_image_grid 0.4

p. _tom_image_grid_ is a plugin for Textpattern 4.6.* CMS. It allows a more compact display (as a grid) of the images list.

p. The plugin adds two buttons 
!data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyMHB4IiBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8ZyBpZD0iZ3JpZCIgc3R5bGU9ImJhY2tncm91bmQtY29sb3I6ICMzMzMiPg0KICAgIDxyZWN0IHg9IjMiICB5PSIzIiAgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KICAgIDxyZWN0IHg9IjMiICB5PSIxMiIgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KICAgIDxyZWN0IHg9IjEyIiB5PSIxMiIgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KICAgIDxyZWN0IHg9IjEyIiB5PSIzIiAgd2lkdGg9IjYiIGhlaWdodD0iNiIvPg0KPC9nPg0KPC9zdmc+DQo=! !data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyMHB4IiBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8ZyBpZD0ibGluZSI+DQogICAgPHJlY3QgeD0iMSIgeT0iMTUiIHdpZHRoPSIzIiBoZWlnaHQ9IjMiLz4NCiAgICA8cmVjdCB4PSIxIiB5PSI5IiAgd2lkdGg9IjMiIGhlaWdodD0iMyIvPg0KICAgIDxyZWN0IHg9IjEiIHk9IjMiICB3aWR0aD0iMyIgaGVpZ2h0PSIzIi8+DQogICAgPHJlY3QgeD0iNiIgeT0iMTUiIHdpZHRoPSIxMyIgaGVpZ2h0PSIzIi8+DQogICAgPHJlY3QgeD0iNiIgeT0iOSIgIHdpZHRoPSIxMyIgaGVpZ2h0PSIzIi8+DQogICAgPHJlY3QgeD0iNiIgeT0iMyIgIHdpZHRoPSIxMyIgaGVpZ2h0PSIzIi8+DQo8L2c+DQo8L3N2Zz4NCg==!
in the Image Tab of your back-office allowing you to switch from the standard display to a more compact grid display.
# --- END PLUGIN HELP ---
-->
<?php
}
?>