{if $bms_show == "true" }
<template id="bms_template" data-test="test" {foreach $bms_settingsJS item=item key=key} data-bms-{$key}="{$item}"{/foreach}>
    <div id="bms" class="bms {if isset($bms_settingsJS.viewTarget)}{$bms_settingsJS.viewTarget} {if $bms_settingsJS.viewTarget == "content"}{$view_container_css}{/if}{/if}" style="{$bms_backgroundCSS|unescape: "html" nofilter}">
        {if $bs3CSSUpdateSRC != ''}
            {inline_script}
                <script type="text/javascript">                    
                    let style = document.createElement('link');
                    style.type = 'text/css';
                    style.rel = 'stylesheet';
                    style.href = '{$bs3CSSUpdateSRC}';    
                    document.head.appendChild(style);
                </script>
            {/inline_script}
        {/if}
        {include file='./bms_bs4.tpl'}
        {if $bms_showViewSelectorGallery == 'true' }
            {include file='./bms_bs4_article_gallery.tpl'}
        {/if}
        {if $bms_showViewSelectorList == 'true' }
            {include file='./bms_bs4_article_list.tpl'}
        {/if}
    </div>
</template>
    {inline_script}
        <script type="text/javascript">
            let suche;
            if (typeof bmsSuche === 'function') {
                try {
                    suche = new bmsSuche();
                    if(window.location.hash) {
                        suche.searchForLocationHash(window.location.hash);
                    }
                } catch (err) {
                    console.error('Fehler bei Instanziierung oder Aufruf von bmsSuche:', err);
                }
            } else {
                console.error('bmsSuche ist nicht definiert – bitte bms.js laden');
            }
        </script>
    {/inline_script}
    {if $specialCSS != ''}<style type="text/css">{$specialCSS}</style>{/if}
{/if}
