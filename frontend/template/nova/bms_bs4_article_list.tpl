<div id="bms_article_list_Tpl" class="col product-wrapper col-sm- col-md-12 col-xl- col-6 {if $view_container_css == 'container-fluid'}col-xl-6 {/if} bms_article_item bms_hide">
    <div class="productbox productbox-row productbox-show-variations  productbox-hover">
        <div class="productbox-inner">
            <div class="row">
                <div class="col col-md-4 col-lg-6 col-xl-3 col-12">
                    <div class="productbox-image bms_badge_container">
                        <div class="bms_badge_stock bms_hide ribbon ribbon-8 productbox-ribbon"><span class="cnt"></span>{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_stock')}</div>
                        <div class="bms_badge_price bms_hide ribbon ribbon-2 productbox-ribbon">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_price')}</div>
                        <div class="bms_badge_topartikel bms_hide ribbon ribbon-4 productbox-ribbon">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_topartikel')}</div>
                        <div class="bms_badge_bestseller bms_hide ribbon ribbon-1 productbox-ribbon">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_badge_bestseller')}</div>
                        <div class="productbox-images">
                            <a class="bms_href">
                                <div class="list-gallery">
                                    <div class="productbox-image square square-image{* first-wrapper*}">
                                        <div class="inner">
                                            <img class="bms_image" src="{$noImagePath}" alt="{$noImageAlt}">
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col col-md-">
                    <div class="productbox-title bms_manufacturer_container bms_title_container">
                        <a class="bms_href bms_title">
                            <span class="bms_title "></span>
                        </a>
                    </div>
                    <form class="form form-basket jtl-validate" target="_self" {*id="buy_form_283541"*} method="POST" data-toggle="basket-add">
                        {*<input type="hidden" class="jtl_token" name="jtl_token" value="f2a224af1b7f7176deb85507816fa09577fd26b5e5cbac47863ad278f157d993">*}
                        <div class="row ">
                            <div class="col productbox-details col-xl-8 col-12" style="display: block;">
                                <dl class="form-row productlist-item-detail text-nowrap-util ">
                                    {if $bms_listItems['view_list_artnr'] > 0} 
                                        <dt class="col  col-6 bms_artnr_container {if $bms_listItems['view_list_artnr'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_artnr')}</dt>
                                        <dd class="col  col-6 bms_artnr_container {if $bms_listItems['view_list_artnr'] == '2'}bms_hideEmpty {/if}"><span class="bms_artnr"></span></dd>
                                    {/if}
                                    {if $bms_listItems['view_list_han'] > 0} 
                                        <dt class="col  col-6 bms_han_container {if $bms_listItems['view_list_han'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_han')}</dt>
                                        <dd class="col  col-6 bms_han_container {if $bms_listItems['view_list_han'] == '2'}bms_hideEmpty {/if}"><span class="bms_han"></span></dd>
                                    {/if}
                                    {if $bms_listItems['view_list_ean'] > 0} 
                                        <dt class="col col-6 bms_ean_container {if $bms_listItems['view_list_ean'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_ean')}</dt>
                                        <dd class="col  col-6 bms_ean_container {if $bms_listItems['view_list_ean'] == '2'}bms_hideEmpty {/if}"><span class="bms_ean"></span></dd>
                                    {/if}                                    
                                    {if $bms_listItems['view_list_manufacturer'] > 0}
                                        <dt class="col col-6 bms_manufacturer_container {if $bms_listItems['view_list_manufacturer'] == '2'}bms_hideEmpty {/if}">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_manufacturer')}</dt>
                                        <dd class="col  col-6 bms_manufacturer_container {if $bms_listItems['view_list_manufacturer'] == '2'}bms_hideEmpty {/if}"><span class="bms_manufacturer"></span></dd>
                                    {/if}
                                </dl>
                                <div class="bms_cat_container bms_hide">
                                    <div class="bms_cat_elems"><!-- filled by JS if category shown --></div>
                                </div>
                                    
                                {if $bms_listItems['view_list_description'] > 0} 
                                    <div class="bms_desc_container text-gray my-2 bms_hide">
                                        {if $bms_plugin->getLocalization()->getTranslation('bms_vlis_title_desc') != '-'}
                                        <span class="mr-2">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_desc')}</span>
                                        {/if}
                                        <span class="bms_desc"><!-- filled by JS if category shown --></span>
                                    </div>
                                {/if}
                                
                            </div>
                            {*<div class="col productbox-variations col-xl-4 col-12"></div>*}
                            <div class="col productbox-options col-xl-4 col-12">
                                <div class="item-list-price">
                                    <div class="price_wrapper">
                                        <div class="price productbox-price ">
                                            {if $bms_listItems['view_list_price'] > 0} 
                                                <div class="col-auto bms_price_container">
                                                    {if $bms_plugin->getLocalization()->getTranslation('bms_vlis_title_price') != '-'}<span class="bms_price_pre">{$bms_plugin->getLocalization()->getTranslation('bms_vlis_title_price')}</span>{/if}
                                                    {if $bms_plugin->getLocalization()->getTranslation('bms_parent_price_from') != '-'}<span class="d-none bms_price_is_parent">{$bms_plugin->getLocalization()->getTranslation('bms_price_parent_from')}</span>{/if}
                                                    <span class="bms_price"><!-- filled by JS if category shown --></span>
                                                    <span class="bms_price_after">*</span>
                                                    <div class="bms_ppu bms_hide"><span class="bms_ppu_price"></span><span>/</span><span class="bms_ppu_unit"></span></div>
                                                </div>
                                            {/if}
                                        </div>
                                                        
                                        {if 
                                            $bms_listItems['view_list_cart'] > 0
                                            || $bms_listItems['view_list_gotooffer'] > 0
                                        
                                        } 
                                            <div class="row mt-auto">
                                                <div class="col-12">
                                                    <div class="row  align-items-end">
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
                                {*
                                    <div class="item-delivery-status delivery-status">
                                        <div class="signal_image status-0">nicht lagernd/auf Bestellung</div>
                                        <div class="estimated_delivery">Lieferzeit: 1 - 3 Werktage</div>
                                    </div>
                                    {if $bms_listItems['view_list_cart'] > 0}
                                        <div class="form-row productbox-onhover productbox-actions item-list-basket-details">
                                            <div class="col  col-12">
                                                <div class="input-group form-counter" role="group" data-bulk="">
                                                    <div class="input-group-prepend ">
                                                        <button type="button" class="btn  btn-" aria-label="Menge verringern" data-count-down=""><span class="fas fa-minus"></span></button>
                                                    </div>
                                                    <input type="number" class="form-control quantity" id="quantity283541" value="1" min="0" step="1" name="anzahl" autocomplete="off" aria-label="Menge" data-decimals="0">
                                                    <div class="input-group-append ">
                                                        <button type="button" class="btn  btn-" aria-label="Menge erhöhen" data-count-up="">
                                                            <span class="fas fa-plus"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="col  col-12">
                                            <button type="submit" class="btn basket-details-add-to-cart btn-primary btn-block" id="submit283541" title="In den Warenkorb" aria-label="In den Warenkorb">In den Warenkorb</button>
                                        </div>
                                    {/if}
                                *}
                            </div>
                            {*
                            <input type="hidden" class="form-control " value="283541" name="a">
                            <input type="hidden" class="form-control " value="1" name="wke">
                            <input type="hidden" class="form-control " value="1" name="overview">
                            <input type="hidden" class="form-control " value="" name="Sortierung">
                            <input type="hidden" class="form-control " value="8025" name="k">
                            *}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>