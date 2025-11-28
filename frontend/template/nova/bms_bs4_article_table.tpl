<div class="row bg-white bms_hide" id="bms_tableContainer">
  <div class="col-12 table-responsive">
    <table class="table table-hover table-sm">
      <thead class="bms_tableHeader" id="bms_article_table_header_Tpl">
        <tr>
          {foreach from=$bms_tableItems item=itemValue key=tableItem}                
            {if $itemValue == null}
            {else if $tableItem == 'view_table_artnr'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_title_artnr')}</td>
            {elseif $tableItem == 'view_table_title'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_title_title')}</td>
            {elseif $tableItem == 'view_table_han'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_title_han')}</td>
            {elseif $tableItem == 'view_table_availability'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_title_avail')}</td>
            {elseif $tableItem == 'view_table_price'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_title_price')}</td>
            {elseif $tableItem == 'view_table_manufacturer'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_title_manu')}</td>
            {elseif $tableItem == 'view_table_cart'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_cart_title')}</td>
            {elseif $tableItem == 'view_table_gotooffer'}
              <td>
                {$bms_plugin->getLocalization()->getTranslation('bms_vtbl_gotooffer_title')}</td>
            {/if}
          {/foreach}
        </tr>
      </thead>
      <tbody id="bms_resultListTable"></tbody>
    </table>
  </div>

  <div class="row bms_hide">
    <div class="col-12">
      <table>
        <tr class="bms_article_item bms_hide" id="bms_article_table_Tpl">
          {foreach from=$bms_tableItems item=itemValue key=tableItem}                
            {if $itemValue == null}
            {else if $tableItem == 'view_table_artnr'}
              <td class="bms_artnr_container">
                <span class="bms_artnr"></span>
              </td>
            {elseif $tableItem == 'view_table_title'}
              <td class="bms_title_container">
                <span class="bms_title"></span>
              </td>
            {elseif $tableItem == 'view_table_han'}
              <td class="bms_han_container">
                <span class="bms_han"></span>
              </td>
            {elseif $tableItem == 'view_table_availability'}
              <td class="p-2 bms_availability_container">
                <span class="bms_availability"></span>
              </td>
            {elseif $tableItem == 'view_table_price'}
              <td class="bms_price_container">
                {if $bms_plugin->getLocalization()->getTranslation('bms_parent_price_from') != '-'}<span class="d-none bms_price_is_parent">{$bms_plugin->getLocalization()->getTranslation('bms_price_parent_from')}</span>{/if}
                <span class="bms_price"></span><span>*</span>
                <div class="bms_ppu bms_hide"><span class="bms_ppu_price"></span><span>/</span><span class="bms_ppu_unit"></span></div>
              </td>
            {elseif $tableItem == 'view_table_manufacturer'}
              <td class="bms_manufacturer_container">
                <span class="bms_manufacturer"></span>
              </td>
            {elseif $tableItem == 'view_table_cart'}
              <td class="bms_cart_container">
                <span class="bms_cart bms_article_toCart"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
  <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
</svg></span>
              </td>
            {elseif $tableItem == 'view_table_gotooffer'}
              <td class="bms_gotooffer_container">
                <span class="bms_gotooffer"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
  <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
</svg></span>
              </td>
            {/if}
          {/foreach}

        </tr>
      </table>
    </div>
  </div>
</div>