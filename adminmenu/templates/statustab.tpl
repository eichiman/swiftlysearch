<style type="text/css">
    #bms_status .card {
        border: 1px solid #ccc;

    }
</style>
<div class="row" id="bms_status">
    <div class="col-4">
        <div class="widget card h-100" role="option" aria-grabbed="false">

            <div class="widget-head card-header {if $search_status == "deactivated" }bg-danger{/if}">
                <span class="widget-title">Pluginstatus</span>
                <hr class="mb-n3">
            </div>
            <div class="widget-content p-2">
                <ul>
                    <li>Sichtbarkeit: {if $search_status == "admin" }nur Adminuser{elseif $search_status== "active"}f&uuml;r alle sichtbar{else}DEAKTIVIERT{/if}
                    {if $licence}
                        <li>Lizenz: {{$licence}}</li>
                    {/if}
                    {if $endTestlic}
                        <li>Testlizenz g&uuml;ltig bis: {{$endTestlic}}</li>
                    {/if}
                    <li>installierte Version: {$bmsVersion}</li>
                    <li>aktuellste Version: {$bmsCurrentVersion}</li>
                    {if $bmsVersion != $bmsCurrentVersion}
                    <li class="text-danger">Achtung, nicht die aktuelle Version, bitte im Admin die neueste Version laden!!</li>
                    {/if}
                </ul>


                <div class="row">
                    <div class="col-12">
                        <form method="POST" target="_blank" action="{{$bmsAdminAction}}">
                            <input type="hidden" name="syncPass" value="{{$bmsAdminSyncPW}}">
                            <input type="hidden" name="showAdmin" value="true">
                            <input type="submit" value="zur Suchadministration" class="btn btn-secondary w-100">
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="col-4">
        <div class="widget card h-100" role="option" aria-grabbed="false">

            <div class="widget-head card-header">
                <span class="widget-title">Indexstatus</span>
                <hr class="mb-n3">
            </div>
            <div class="widget-content container-fluid">
                <div class="row">
                    <div class="col-12">
                        Aktuell im Index:
                    </div>
                    <div class="col-12">
                        <ul>
                            {if $bmsStats->stats}
                                {foreach key=schluessel item=wert from=$bmsStats->stats->index}
                                    <li>{$schluessel}: {$wert->count}</li>
                                {foreachelse}
                                    <li>Index aktuell leer</li>
                                {/foreach}
                            {else}
                                <li>Index aktuell leer</li>
                            {/if}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="widget card h-100" role="option" aria-grabbed="false">

            <div class="widget-head card-header">
                <span class="widget-title">FAQ / Anleitung / Doku</span>
                <hr class="mb-n3">
            </div>
            <div class="widget-content container-fluid">
                <div class="row">
                    <div class="col-12">
                        Infos rund um das Plugin findest Du unter:<br>
                        <a href="https://www.bm-suche.de/faq" target="_blank">https://www.bm-suche.de/faq</a><br><br>
                        bei Problemen, bitte mail an:<br><a href="mailto:info@bm-suche.de">info@bm-suche.de</a>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>