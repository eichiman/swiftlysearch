<div id="bms_search" {if $bms_settingsJS.viewFilter == "always"}class="bms_filter_always"{/if}>
  <div class="{$view_container_css} bms_container h-100">


    <div class="row p-1 p-md-5  align-items-center position-relative bms_hideContent" id="bms_header">

      <div id="bms_close_top" class="d-none d-md-flex bms_close">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
</svg>
      </div>

      {if $bms_banner|@count > 0}
        <div class="col-12 col-md-auto {if $bms_bannerPosition == 'right'} order-2 pl-5 pr-0 {else} pl-0 pr-5 {/if}" id="">
          <div id="bms_banner" class="cube visible-lg visible-md">
            {foreach $bms_banner item=banner}
              <div class="bms_bannerItem" style="display: none;">
                <a onclick="bms_clickBanner({$banner->link});">
                  <img class="bms_bannerImage" data-src="{$banner->file}">
                </a>
              </div>
            {/foreach}
          </div>
        </div>
      {/if}
      <div class="col-12 col-md py-2">

        <div class="row">
          <div class="col px-0">
            <div id="bms_inputContainer">
              <input type="input" enterkeyhint="search" id="bms_inputPopup" class="form-control" autocomplete="off" aria-label="Suchen" placeholder="{lang key='input_placeholder' section='bms'}" spellcheck="false" value="">
              <input type="input" enterkeyhint="search"  readonly="" id="bms_inputSuggest" class="bms_hide form-control" autocomplete="off" aria-label="Suchen" spellcheck="false" tabindex="-1" value="">
            </div>
            <div id="bms_suggestContainer" class="bms bms_hide">
              <div class="row m-0" id="bms_suggestList">
                <div class="bms_suggestItem col-12 p-2 bms_hide" id="bms_suggestItemTpl"></div>
              </div>
            </div>
          </div>
          <div class="col-auto p-0">
            <button type="submit" class="btn btn-secondary h-100" name="search" aria-label="Suchen">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
</svg>
            </button>
          </div>

          <div id="bms_close" class="d-md-none bms_close col-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
</svg>
          </div>
        </div>

      </div>

    </div>


    <div class="row align-self-center p-1 px-md-2 py-md-3 bg-white">
      <div class="col pl-0 pl-md-2">
        <div class="row align-items-center">
          <div class="col-auto py-1">
            
            <div class="btn btn-outline-secondary btn-sm  bms_changeFilter d-lg-none">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z"/>
</svg>
            </div>
            <div class="btn btn-outline-secondary btn-sm  bms_changeFilter d-none {if $bms_settingsJS.viewFilter != "always"}d-lg-inline-block {/if}">
            <svg class="toggle-off" xmlns="http://www.w3.org/2000/svg" width="16" height="21" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16">
  <path d="M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z"/>
</svg>
            <svg class="toggle-on" xmlns="http://www.w3.org/2000/svg" width="16" height="21" fill="currentColor" class="bi bi-toggle-on" viewBox="0 0 16 16">
  <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
</svg>
              {lang key='filter_header' section='bms'}
            </div>
            <div class="bms_clearSearch bms_hide btn btn-outline-secondary btn-sm mr-2 d-none d-md-inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg>{lang key='header_search_for' section='bms'} &quot;<span class="bms_currentSearch"></span>&quot;</div>
          </div>
          <div class="col">
            <div class="row bms_hide d-none d-md-flex" id="bms_active_filter">
            <div class="bms_af bms_af_sonderpreis bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span>{lang key='active_filter_sonderpreis' section='bms'}</span></div>
            <div class="bms_af bms_af_bestseller bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span>{lang key='active_filter_bestseller' section='bms'}</span></div>
            <div class="bms_af bms_af_topartikel bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span>{lang key='active_filter_topartikel' section='bms'}</span></div>
            <div class="bms_af bms_af_stock bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span>{lang key='active_filter_stock' section='bms'}</span></div>
            <div class="bms_af bms_af_minPrice bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg>&gt;<span class="value"></span></div>
            <div class="bms_af bms_af_maxPrice bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg>&lt;<span class="value"></span></div>
            <div class="bms_af bms_af_category bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span class="value"></span></div>
            <div class="bms_af bms_af_manufacturerTpl bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span class="value"></span></div>
            <div class="bms_af bms_af_warengruppeTpl bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span class="value"></span></div>
            <div class="bms_af bms_af_merkmaleTpl bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span class="value"></span></div>
            <div class="bms_af bms_af_merkmal bms_hide btn btn-outline-secondary btn-sm mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3 mr-1" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg><span class="value"></span></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-auto pr-0 pr-md-2">
        <div class="row mr-0">
          {if $bms_showViewSelector == 'true' }
            {if $bms_showViewSelectorGallery == 'true' }
            <div class="col-auto p-1"><div class="btn btn-outline-secondary btn-sm bms_view bms_viewIconGallery">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
              <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
              <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
            </svg>
            </div>
            </div>
            {/if}
            {if $bms_showViewSelectorList == 'true' }
            <div class="col-auto p-1"><div class="btn btn-outline-secondary btn-sm bms_view bms_viewIconList" data-view="list"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-view-list" viewBox="0 0 16 16">
              <path d="M3 4.5h10a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1H3zM1 2a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 2zm0 12a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 14z"/>
            </svg></div></div>
            {/if}
            {if $bms_showViewSelectorTable == 'true' }
            <div class="col-auto p-1"><div class="btn btn-outline-secondary btn-sm bms_view bms_viewIconTable" data-view="table"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
              </svg></div></div>
            {/if}
          {/if}
        </div>
      </div>
    </div>
    <div class="row bms_content h-100">
      <div class="col-auto p-0" id="bms_filter">

        <div class="panel panel-default p-0" style="border-radius: 0;">
          <div class="panel-heading container">

        <div class="row align-items-center py-2">
          <div class="col">{lang key='filter_header' section='bms'}</div>
          <div class="col-auto bms_closeFilter {if $bms_settingsJS.viewFilter == "always"}d-lg-none{/if}" style="cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
</svg></div>
        </div>
          </div>
          <div class="panel-body container px-2">


            <div class="row">
              <div class="col-12 text-center p-2">
                <span class="bms_hits mr-2"></span>{lang key='filter_count_hits' section='bms'}
              </div>
            </div>            
            
            <div class="row">
              <div class="col-12 pb-2">
                <select name="bms_sort" id="bms_select_sort">
                  <option value="rel">{lang key='filter_sort_by_rel' section='bms'}</option>
                  <option value="09">{lang key='filter_sort_by_price_up' section='bms'}</option>
                  <option value="90">{lang key='filter_sort_by_price_down' section='bms'}</option>
                  <option value="az">{lang key='filter_sort_by_az_up' section='bms'}</option>
                  <option value="za">{lang key='filter_sort_by_az_down' section='bms'}</option>
                </select>
              </div>

              {if 
                $bms_filter_showStockFilter != 'deactivated'
                && $bms_filter_showStockFilter != 'onlyStock-noFilter'
              }
                  <div id="bms_filter_stock" class="col-12 py-1 {$bms_filter_showStockFilter}">
                    <label class="text-left btn btn-sm btn-outline-secondary w-100 px-2" for="bms_filter_stockInp">
                      <input type="checkbox" id="bms_filter_stockInp" aria-label="Lagerfilter"{if $bms_filter_stockFilterChecked} checked{/if}>
                        <span>{lang key='filter_special_stock_btn' section='bms'}</span>&nbsp;(<span class="count"></span>)
                      </input>
                    </label>
                  </div>
              {/if}
              {if $bms_filter_showTopartikelFilter != 'deactivated'}
                  <div id="bms_filter_topartikel" class="col-12 py-1 {$bms_filter_showTopartikelFilter}" for="bms_filter_topartikelInp">
                    <label class="col-12 text-left btn btn-sm btn-outline-secondary w-100 px-2" for="bms_filter_topartikelInp">
                      <input type="checkbox" id="bms_filter_topartikelInp" aria-label="Topartikelfilter">
                        <span>{lang key='filter_special_topartikel_btn' section='bms'}</span>&nbsp;(<span class="count"></span>)
                      </input>
                    </label>
                  </div>
              {/if}
              {if $bms_filter_showBestsellerFilter != 'deactivated'}
                  <div id="bms_filter_bestseller" class="col-12 py-1 {$bms_filter_showBestsellerFilter}" for="bms_filter_bestsellerInp">
                    <label class="col-12 text-left btn btn-sm btn-outline-secondary w-100 px-2" for="bms_filter_bestsellerInp">
                      <input type="checkbox" id="bms_filter_bestsellerInp" aria-label="Bestsellerfilter">
                        <span>{lang key='filter_special_bestseller_btn' section='bms'}</span>&nbsp;(<span class="count"></span>)
                      </input>
                    </label>
                  </div>
              {/if}
              {if $bms_filter_showSpecialPriceFilter != 'deactivated'}
                  <div class="col-12 py-1 {$bms_filter_showSpecialPriceFilter}" for="bms_filter_sonderpreisInp">
                    <label id="bms_filter_sonderpreis" class="text-left btn btn-sm btn-outline-secondary w-100 px-2" for="bms_filter_sonderpreisInp">
                      <input type="checkbox" id="bms_filter_sonderpreisInp" aria-label="Sonerpreisfilter">
                        <span>{lang key='filter_special_price_btn' section='bms'}</span>&nbsp;(<span class="count"></span>)
                      </input>
                    </label>
                  </div>
              {/if}




              {if $bms_filter_showPriceFilter != 'deactivated'}
                <div class="col-12 py-0 pb-2 {$bms_filter_showPriceFilter}">
                  <div class="row form-inline">
                    <div class="col-6 pt-0 ">
                      <input id="bms_minPrice" type="number" aria-label="min. Preis Filter" class="bms_minPrice form-control form-control-sm" value="" type="text"
                        style="width:100%" placeholder="{lang key='filter_price_placeholder_min' section='bms'}">
                    </div>
                    <div class="col-6 pt-0 ">
                      <input id="bms_maxPrice" type="number" aria-label="max. Preis Filter" class="bms_maxPrice form-control form-control-sm" value="" type="text"
                        style="width:100%" placeholder="{lang key='filter_price_placeholder_max' section='bms'}">
                    </div>
                  </div>
                </div>
              {/if}

              {if $bms_filter_showWarengruppenFilter != 'deactivated'}
                <div class="col-12 pb-2 {$bms_filter_showWarengruppenFilter}">
                  <select name="" multiple data-width='auto' 
                  title="{lang key='filter_warengruppe_select_title' section='bms'}" 
                  data-placeholder="{lang key='filter_warengruppe_select_title' section='bms'}" 
                  id="bms_warengruppeSelect"></select>
                </div>
              {/if}

              {if $bms_filter_showManuFilter != 'deactivated'}
                <div class="col-12 pb-2 {$bms_filter_showManuFilter}">
                  <select name="" multiple data-width='auto' 
                  title="{lang key='filter_manufacturer_select_title' section='bms'}" 
                  data-placeholder="{lang key='filter_manufacturer_select_title' section='bms'}" 
                  id="bms_manuSelect"></select>
                </div>
              {/if}

              {if $bms_filter_showMerkmalFilter != 'deactivated'}
                <div class="col-12 pb-2 bms_hide bms_merkmalContainer {$bms_filter_showMerkmalFilter}" id ="bms_filterMerkmalTmpl">
                  <select class="bms_merkmalSelect" name="" multiple data-width='auto' aria-label="Merkmalfilter" title="" data-placeholder=""></select>
                </div>
              {/if}

            </div>
          </div>
        </div>


        {if $bms_filter_showCategoryFilter != 'deactivated'}
          <div class="panel panel-default p-0 bms_hide" style="border-radius: 0;" id="bms_catFilterContainer">
            <div class="panel-heading p-2 mb-0">
              <div>{lang key='filter_category_title' section='bms'}</div>
            </div>
            <div class="panel-body container px-0">
              <div id="bms_catFilterContent">
              </div>
            </div>
          </div>
        {/if}



      </div>
      <div class="col p-0 bms_contentContainer ml-auto">
        <div class="row p-2 m-1 bms_hide" id="bms_errorList">
          <div class="bms_noHits bms_errorText bms_hide btn btn-outline-secondary">{lang key='bms_textNoHits' section='bms'}</div>
          <div class="bms_noExactHits bms_errorText bms_hide btn btn-outline-secondary">{lang key='bms_textNoExactHits' section='bms'}</div>
          <div class="bms_noServerResponse bms_errorText bms_hide btn btn-outline-secondary">{lang key='bms_textNoServerResponse' section='bms'}</div>
        </div>
        <div class="row m-2 bms_hide" id="bms_resultList"></div>
        {if $bms_showViewSelectorTable == 'true' }
          {include file='./bms_bs4_article_table.tpl'}
        {/if}
        <div class="text-center" id="bms_spinner">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

      </div>
      <div class="col-12 p-3 text-right"><div class="btn btn-primary bms_loadMore btn-sm">{lang key='bms_loadMore' section='bms'}</div></div>
    </div>
  </div>
</div>