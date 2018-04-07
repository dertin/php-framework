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


                  <!--

                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-left">
                          <li>
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                  <span class="glyphicon glyphicon-camera"></span>
                              </a>
                          </li>
                          <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                      <span class="glyphicon glyphicon-bell"></span>
                                      <b class="caret"></b>
                                      <span class="notification">3</span>
                                </a>
                                <ul class="dropdown-menu">
                                  <li><a href="#">actualiza tus datos</a></li>
                                  <li><a href="#">cambia tu foto de perfil</a></li>
                                  <li><a href="#">califica un servicio</a></li>
                                </ul>
                          </li>
                          <li>
                             <a href="">
                                  <span class="glyphicon glyphicon-search"></span>
                              </a>
                          </li>
                      </ul>

                      <ul class="nav navbar-nav navbar-right">
                          <li>
                             <a href="">Area Personal</a>
                          </li>
                          <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    Lista Despelgable <b class="caret"></b> </a>
                                <ul class="dropdown-menu">
                                  <li><a href="#">Ir a la tienda</a></li>
                                  <li><a href="#">Consultar Servicio</a></li>
                                  <li><a href="#">Ver Ofertas</a></li>
                                  <li class="divider"></li>
                                  <li><a href="#">Algo separado</a></li>
                                </ul>
                          </li>
                          <li>
                              <a href="#">CerrarSesion</a>
                          </li>
                      </ul>
                    </div> -->




{/strip}
