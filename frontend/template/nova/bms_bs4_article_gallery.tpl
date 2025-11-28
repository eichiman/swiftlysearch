<div id="bms_article_gallery_Tpl" class="col product-wrapper col-sm-6 col-md-4 col-xl-4 col-6 bms_article_item bms_hide">
    <div class="productbox productbox-column  productbox-hover">
        <div class="productbox-inner">
            <div class="row">
                <div class="col col-12">
                    <div class="productbox-image">
                        <div class="bms_badge_container productbox-images list-gallery">
                            <div class="bms_badge_stock bms_hide ribbon ribbon-8 productbox-ribbon"><span class="cnt"></span>{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_stock')}</div>
                            <div class="bms_badge_price bms_hide ribbon ribbon-2 productbox-ribbon">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_price')}</div>
                            <div class="bms_badge_topartikel bms_hide ribbon ribbon-4 productbox-ribbon">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_topartikel')}</div>
                            <div class="bms_badge_bestseller bms_hide ribbon ribbon-1 productbox-ribbon">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_bestseller')}</div>

                            <a class="bms_href">
                                <div class="productbox-image square square-image {*first-wrapper*}">
                                    <div class="inner">
                                        <img class="bms_image {*first*} lazyautosizes lazyloaded" sizes="249px" src="{$noImagePath}" alt="{$noImageAlt}">
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col col-12">
                    <div class="productbox-title bms_manufacturer_container bms_title_container">
                        {if $bms_galleryItems['view_gallery_manufacturer'] > 0}<span class="bms_manufacturer {if $bms_galleryItems['view_gallery_manufacturer'] == '2'}bms_hideEmpty {/if}"></span>{/if}
                        <a class="bms_title bms_href text-clamp-2"></a>
                    </div>
            
                    <div class="bms_badges">
                        {if $bms_galleryItems['view_gallery_artnr'] > 0} 
                            <div class="badge p-1 mr-1 bms_artnr_container {if $bms_galleryItems['view_gallery_artnr'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_artnr')}<span class="bms_artnr {if $bms_galleryItems['view_gallery_artnr'] == '2'}bms_hideEmpty {/if}"></span></div>
                        {/if}
                        {if $bms_galleryItems['view_gallery_han'] > 0} 
                            <div class="badge p-1 mr-1 bms_han_container {if $bms_galleryItems['view_gallery_han'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_han')}<span class="bms_han  {if $bms_galleryItems['view_gallery_han'] == '2'}bms_hideEmpty {/if}"></span></div>
                        {/if}
                        {if $bms_galleryItems['view_gallery_ean'] > 0} 
                            <div class="badge p-1 mr-1 bms_ean_container {if $bms_galleryItems['view_gallery_ean'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_ean')}<span class="bms_ean {if $bms_galleryItems['view_gallery_ean'] == '2'}bms_hideEmpty {/if}"></span></div>
                        {/if}                        
                    </div>

                    {if $bms_galleryItems['view_gallery_price'] > 0} 
                        <div>
                            <div class="price_wrapper bms_price_container">
                                <div class="price productbox-price">
                                    {if $bms_plugin->getLocalization()->getTranslation('bms_vgly_title_price') != '-'}<span class="bms_price_pre">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_price')}</span>{/if}
                                    {if $bms_plugin->getLocalization()->getTranslation('bms_parent_price_from') != '-'}<span class="d-none bms_price_is_parent">{$bms_plugin->getLocalization()->getTranslation('bms_price_parent_from')}</span>{/if}
                                    <span class="bms_price"><!-- filled by JS if category shown --></span>
                                    <span class="bms_price_after">*</span>
                                </div>
                                    
                                <div class="price-note bms_ppu bms_hide">
                                    <div class="base_price">
                                        <span class="value bms_ppu_price"></span><span>/</span><span class="bms_ppu_unit"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}

                    
                </div>    
                <div class="col col-12 bms_cat_container bms_hide">
                    <div class="bms_cat_elems"><!-- filled by JS if category shown --></div>
                </div>
    
                {if $bms_galleryItems['view_gallery_description'] > 0} 
                    <div class="col col-12 bms_desc_container text-gray my-2 bms_hide">
                        {if $bms_plugin->getLocalization()->getTranslation('bms_vgly_title_desc') != '-'}
                        <span class="mr-2">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_desc')}</span>
                        {/if}
                        <span class="bms_desc"><!-- filled by JS if category shown --></span>
                    </div>
                {/if}
                
        
                {if 
                    $bms_galleryItems['view_gallery_price'] > 0
                    || $bms_galleryItems['view_gallery_cart'] > 0
                    || $bms_galleryItems['view_gallery_gotooffer'] > 0
                
                } 
                    <div class="col-12">
                        <div class="row  align-items-end">
                        <div class="col"></div>
                        <div class="col-auto bms_cart_container">
                            {if $bms_galleryItems['view_gallery_cart'] > 0} 
                                <span class="btn btn-primary bms_article_toCart btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
<path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
</svg></span>
                            {/if}
                            {if $bms_galleryItems['view_gallery_gotooffer'] > 0} 
                                <div class="btn btn-primary f_bme_article_link btn-sm">{$bms_plugin->getLocalization()->getTranslation('bms_vgly_title_gotooffer')}<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                                <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                                </div>
                            {/if}
                        </div>
                    </div>
                    </div>
                        
                {/if}         

                
            </div>
        </div>
    </div>
</div>