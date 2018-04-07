{config_load file='default.conf'}{strip}
</div>
    <footer class="footer">
        <div class="container-fluid">
          <p class="copyright pull-right">
                &copy; 2018 Company </p>
        </div>
    </footer>
</div>

    {if isset($_layoutParams.jsLayout) && count($_layoutParams.jsLayout)}
        {foreach $_layoutParams.jsLayout as $jsLayout}
            <script src="{$jsLayout}" type="text/javascript"></script>
        {/foreach}
    {/if}

    {if isset($varViewToJs)}
        <script type="text/javascript">
            {$varViewToJs|regex_replace:'/[\r\t\n]/':' '}
        </script>
    {/if}

    {if isset($_layoutParams.js) && count($_layoutParams.js)}
        {foreach $_layoutParams.js as $js}
            <script src="{$js}" type="text/javascript"></script>
        {/foreach}
    {/if}

    {if isset($_layoutParams.extraTplJS) && !empty($_layoutParams.extraTplJS)}
        {include file={$_layoutParams.extraTplJS}}
    {/if}


    <div id="errorIE" style="display: none;"></div>
    <noscript>
    <div id="errorJS" style="display: block;">Para utilizar las funcionalidades completas de este sitio es necesario tener
     JavaScript habilitado. Aquí están las <a class="linkFFF" rel="nofollow" href="http://www.enable-javascript.com/es/"
     target="_blank"> instrucciones para habilitar JavaScript en tu navegador web</a>.</div>
    </noscript>

  </body>
</html>
{/strip}
