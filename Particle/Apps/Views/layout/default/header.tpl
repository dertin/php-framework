{config_load file="default.conf"}{strip}
<!DOCTYPE html>
<html lang="{#LANG#}" dir="ltr" itemscope="itemscope" itemtype="http://schema.org/WebPage">
    <head>
        <meta charset="{#CHARSET#}">
        <base href="{#URLHOME#}">

        <title>{if isset($pageTitle)}{$pageTitle}{else}{#DEFAULT_TITLE#}{/if} - {#NAMESITE#}</title>

        <meta content="noindex,nofollow" name="robots">

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <meta name="format-detection" content="telephone=no">
        <meta name="format-detection" content="address=no">

        {if isset($_layoutParams.cssLayout) && count($_layoutParams.cssLayout)}
            {foreach $_layoutParams.cssLayout as $cssLayout}
                <link href="{$cssLayout}" type="text/css" rel="stylesheet">
            {/foreach}
        {/if}

        {if isset($_layoutParams.css) && count($_layoutParams.css)}
            {foreach item=css from=$_layoutParams.css}
                <link href="{$css}" type="text/css" rel="stylesheet">
            {/foreach}
        {/if}

        {if isset($varViewToCss)}
          <style>
            {$varViewToCss}
          </style>
        {/if}

        {if isset($varGlobalToJs)}
            <script type="text/javascript">

            {if isset($varGlobalToJs)}
                {$varGlobalToJs|regex_replace:'/[\r\t\n]/':' '}
            {/if}

            </script>
        {/if}

        {if isset($_layoutParams.jsHeadLayout) && count($_layoutParams.jsHeadLayout)}
            {foreach $_layoutParams.jsHeadLayout as $jsHeadLayout}
                <script src="{$jsHeadLayout}" type="text/javascript"></script>
            {/foreach}
        {/if}

        {if isset($_layoutParams.jsHead) && count($_layoutParams.jsHead)}
            {foreach $_layoutParams.jsHead as $jsHead}
                <script src="{$jsHead}" type="text/javascript"></script>
            {/foreach}
        {/if}

        {if isset($_layoutParams.extraTplJSTop) && !empty($_layoutParams.extraTplJSTop)}
            {include file={$_layoutParams.extraTplJSTop}}
        {/if}

        <link href="{$_layoutParams.path_layout}/ico/favicon.ico" type="image/x-icon" rel="shortcut icon" media="all">

    </head>

    <body>
      <div class="wrapper">
{/strip}
