<a id="bms_article_gallery_Tpl"
    class="col-12 col-sm-6 col-md-4 col-lg-3 p-1 text-center bms_article_item bms_hide">
    <div class="btn btn-outline-secondary bms_galleryContainer h-100 w-100 p-2 text-primary">
        <div class="d-flex flex-column h-100">
            <div class="row">
                
                <div class="bms_badge_container productbox-images list-gallery">
                    <div class="bms_badge_stock bms_hide ribbon ribbon-8 productbox-ribbon overlay-label auflager"><span class="cnt"></span>{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_stock')}</div>
                    <div class="bms_badge_price bms_hide ribbon ribbon-2 productbox-ribbon overlay-label sonderangebote">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_price')}</div>
                    <div class="bms_badge_topartikel bms_hide ribbon ribbon-4 productbox-ribbon overlay-label topangebote">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_topartikel')}</div>
                    <div class="bms_badge_bestseller bms_hide ribbon ribbon-1 productbox-ribbon overlay-label bestseller">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_bestseller')}</div>
                </div>

                {foreach from=$bms_galleryItems item=itemValue key=galleryItem}                
                    {if $itemValue == null}
                    {elseif $galleryItem == 'view_gallery_image'}
                        <div class="col-12 text-center" style="min-height:200px;"><img
                            class="bms_image" src="{$noImagePath}" alt="{$noImageAlt}"></div>
                    {elseif $galleryItem == 'view_gallery_title'}
                        <div class="bms_title_container col-12 my-2">
                            <span class="bms_title text-primary font-weight-bold"></span></div>
                    {elseif $galleryItem == 'view_gallery_artnr'}
                        <div class="col-12 bms_artnr_container">
                        {$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_artnr')}<span
                            class="bms_artnr"></span></div>
                    {elseif $galleryItem == 'view_gallery_manufacturer'}
                        <div class="col-12 bms_manufacturer_container">
                        {if $bms_plugin->getLocalization()->getTranslation('bms_vgly_title_manu') != '-'}{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_manu')}{/if}<span
                            class="bms_manufacturer py-1"></span></div>
                    {elseif $galleryItem == 'view_gallery_han'}
                        <div class="col-12 bms_han_container">
                        {$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_han')}<span
                            class="bms_han"></span></div>
                    {elseif $galleryItem == 'view_gallery_ean'}
                        <div class="col-12 bms_ean_container">
                        {$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_ean')}<span
                            class="bms_ean"></span></div>
                    {elseif $galleryItem == 'view_gallery_description'}
                        <div class="col-12 bms_desc_container text-gray my-2 ">
                        {if $bms_plugin->getLocalization()->getTranslation('bms_vgly_title_desc') != '-'}{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_desc')}{/if}<span
                            class="bms_desc"></span></div>
                    {elseif $galleryItem == 'view_gallery_availability'}
                        <div class="col-12 bms_availability_container">
                        {if $bms_plugin->getLocalization()->getTranslation('bms_vgly_title_avail') != '-'}{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_avail')}{/if}<span
                            class="bms_availability"></span></div>
                    {/if}
                {/foreach}
            </div>
            
            <div class="row mt-auto pt-2 align-items-end">
                {if $bms_galleryItems['view_gallery_price'] == 1}
                    <div class="col-auto bms_price_container mt-2 {if $bms_galleryItems['view_gallery_cart'] != 1 && $bms_galleryItems['view_gallery_gotooffer'] != 1}mx-auto{/if}">
                        <div class="btn btn-outline-primary btn-sm font-weight-bold">
                            {if $bms_plugin->getLocalization()->getTranslation('bms_vgly_title_price') != '-'}<span class="bms_price_pre">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_price')}</span>{/if}
                            {if $bms_plugin->getLocalization()->getTranslation('bms_parent_price_from') != '-'}<span class="d-none bms_price_is_parent">{$bms_plugin->getLocalization()->getTranslation('bms_price_parent_from')}</span>{/if}
                                <span class="bms_price"><!-- filled by JS if category shown --></span>
                            <span class="bms_price_after">*</span>
                            <div class="bms_ppu bms_hide"><span class="bms_ppu_price"></span><span>/</span><span class="bms_ppu_unit"></span></div>
                        </div>
                    </div>
                {/if}
                {if $bms_galleryItems['view_gallery_cart'] == 1 ||  $bms_galleryItems['view_gallery_gotooffer'] == 1}
                    <div class="col p-0"></div>
                    <div class="col-auto bms_cart_container mt-2">
                        {if $bms_galleryItems['view_gallery_cart'] == 1}
                            <span class="btn btn-primary bms_article_toCart btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                </svg>
                            </span>
                        {/if}
                        {if $bms_galleryItems['view_gallery_gotooffer'] == 1}
                            <div class="btn btn-primary f_bme_article_link btn-sm">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_gotooffer')}<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
  <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
</svg></div>
                        {/if}
                    </div>
                {/if}
            </div>
                        
        </div>
    </div>
</a>