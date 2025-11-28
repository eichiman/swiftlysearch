<a id="bms_article_list_Tpl" class="col product-wrapper col-sm- col-md-12 col-xl- col-6 {if $view_container_css == 'container-fluid'}col-xl-6 {/if} bms_article_item bms_hide p-2">
    <div class="btn btn-outline-secondary p-2 bms_listContainer w-100 h-100 text-left text-primary">
        <div class="row h-100 p-0 ">
            <div class="col-auto">
                <img class="bms_image" src="{$noImagePath}" alt="{$noImageAlt}">
            </div>
            <div class="col">
                <div class="d-flex flex-column h-100">
                    <div class="row bms_badge_container">
                        <div class="col-12 bms_manufacturer_container bms_title_container font-weight-bold mb-1">
                            {if $bms_listItems['view_list_manufacturer'] > 0}<span class="bms_manufacturer {if $bms_listItems['view_list_manufacturer'] == '2'}bms_hideEmpty {/if}"></span>{/if}
                            <span class="bms_title "></span>
                        </div>
                        <div class="col-12 bms_badges">
                            {if $bms_listItems['view_list_artnr'] > 0} 
                                <div class="badge p-1 mr-1 bms_artnr_container {if $bms_listItems['view_list_artnr'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_artnr')}<span class="bms_artnr"></span></div>
                            {/if}
                            {if $bms_listItems['view_list_han'] > 0} 
                                <div class="badge p-1 mr-1 bms_han_container {if $bms_listItems['view_list_han'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_han')}<span class="bms_han"></span></div>
                            {/if}
                            {if $bms_listItems['view_list_ean'] > 0} 
                                <div class="badge p-1 mr-1 bms_ean_container {if $bms_listItems['view_list_ean'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_ean')}<span class="bms_ean"></span></div>
                            {/if}


                            <div class="bms_badge_price bms_hide badge p-1 mr-1 overlay-label sonderangebote">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_price')}</div>
                            <div class="bms_badge_topartikel bms_hide badge p-1 mr-1 overlay-label topangebote">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_topartikel')}</div>
                            <div class="bms_badge_bestseller bms_hide badge p-1 mr-1 overlay-label bestseller">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_bestseller')}</div>
                            <div class="bms_badge_stock bms_hide badge p-1 mr-1 overlay-label auflager">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_stock')}</div>
                            
                            
                        </div>

                        <div class="col-12 bms_cat_container bms_hide">
                            <div class="bms_cat_elems"><!-- filled by JS if category shown --></div>
                        </div>
                        {if $bms_listItems['view_list_description'] > 0} 
                            <div class="col-12 bms_desc_container text-gray my-2 bms_hide">
                                {if $bms_plugin->getLocalization()->getTranslation('bms_vlis_title_desc') != '-'}
                                <span class="mr-2">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_desc')}</span>
                                {/if}
                                <span class="bms_desc"><!-- filled by JS if category shown --></span>
                            </div>
                        {/if}

                    </div>

                    {if 
                        $bms_listItems['view_list_price'] > 0
                        || $bms_listItems['view_list_cart'] > 0
                        || $bms_listItems['view_list_gotooffer'] > 0
                    
                    } 
                        <div class="row mt-auto">
                            <div class="col-12">
                                <div class="row  align-items-end">
                                {if $bms_listItems['view_list_price'] > 0} 
                                    <div class="col-auto bms_price_container">
                                        <div class="btn btn-outline-primary btn-sm font-weight-bold">
                                            {if $bms_plugin->getLocalization()->getTranslation('bms_vlis_title_price') != ''}<span class="bms_price_pre">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_price')}</span>{/if}
                                            {if $bms_plugin->getLocalization()->getTranslation('bms_parent_price_from') != '-'}<span class="d-none bms_price_is_parent">{$bms_plugin->getLocalization()->getTranslation('bms_price_parent_from')}</span>{/if}
                                            <span class="bms_price"><!-- filled by JS if category shown --></span>
                                            <span class="bms_price_after">*</span>
                                            <div class="bms_ppu bms_hide"><span class="bms_ppu_price"></span><span>/</span><span class="bms_ppu_unit"></span></div>
                                        </div>
                                    </div>
                                {/if}
                                <div class="col"></div>
                                <div class="col-auto bms_cart_container">
                                    {if $bms_listItems['view_list_cart'] > 0} 
                                        <span class="btn btn-primary bms_article_toCart btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
  <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
</svg></span>
                                    {/if}
                                    {if $bms_listItems['view_list_gotooffer'] > 0} 
                                        <div class="btn btn-primary f_bme_article_link btn-sm">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_gotooffer')}<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                                        <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                            </div>
                            
                        </div>
                    {/if}                
                </div>
            </div>
        </div>
    </div>
</a>