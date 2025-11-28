{if $errorcode == "140"}
    {include file='./confirm_mail_form.tpl'}
{elseif $errorcode == "236"}
    <div class="row">
        <div class="col-12">
            <div class="widget card mb-4" role="option" aria-grabbed="false" style="border: 1px solid #ccc;">

                <div class="widget-head card-header bg-success">
                    <span class="widget-title">Mail versendet</span>
                    <hr class="mb-n3">
                </div>
                <div class="widget-content p-2">
                    <div class="row">
                        <div class="col-12">Bitte pr&uuml;fe Deinen Posteingang</div>
                        <div class="col-12 mt-5">Bitte das Suchplugin &uuml;ber den Link im Mail aktivieren!</b></div>
                        <div class="col-12 mt-5">Bei Problemen bitte am besten per Mail an: <a href="mailto:info@bm-suche.de?subject=Lizenzserver BM-Suche down">info@bm-suche.de</a> wenden</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}
    <div class="row">
        <div class="col-12">
            <div class="widget card mb-4" role="option" aria-grabbed="false" style="border: 1px solid #ccc;">

                <div class="widget-head card-header bg-danger">
                    <span class="widget-title">{$errorcode}  - Lizenzserver down oder nicht erreichbar!</span>
                    <hr class="mb-n3">
                </div>
                <div class="widget-content p-2">
                    <div class="row">
                        <div class="col-12">Leider ist der Lizenzserver der Suche aktuell nicht erreichbar oder gibt einen Fehler aus!</div>
                        <div class="col-12">Fehlercode: {$errorcode}</div>
                        <div class="col-12">Bitte die Seite aktualisieren (F5).</div>
                        <div class="col-12 mt-5">Sollte dies nicht ausreichen, oder weitere Probleme auftreten, bitte das <b>Suchplugin deaktivieren!</b></div>
                        <div class="col-12 mt-5">Wende Dich bitte am besten per Mail an: <a href="mailto:info@bm-suche.de?subject=Lizenzserver BM-Suche down">info@bm-suche.de</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}