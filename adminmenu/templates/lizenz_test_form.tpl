<form class="" method="post" action="{$adminURL}">
    {$jtl_token}
    <input type="hidden" name="adminURL" value="{$adminURL}">
    <h1>kostenlose Testlizenz, bzw. Lizenz anfragen</h1>
    <div class="row">
        <div class="col-12">Bitte gib eine Mailadresse ein, an die wir eine Best&auml;tigungsmail senden k&ouml;nnen.<br>
            Du erh&auml;lst eine Mail mit einem Best&auml;tigungslink.<br>
            Bitte klicken Sie dort auf den Link, um die Testlizenz freizuschalten. <br>
            Die Testlizenz ist komplett kostenlos und ist f&uuml;r 1 Monat g&uuml;ltig.<br>
            DANKE!
        </div>
        <div class="col-12 mt-4">
            <div class="form-group form-row align-items-center">
                <div class="col-12">
                    <div class="row">
                        <div class="col-auto">
                            <input class="form-control" id="bms_lizenz_mail" name="bms_lizenz_mail" type="email" required value="" placeholder="Deine Mailadresse">
                        </div>
                    
                        <div class="col-auto">
                                <button name="speichern" type="submit" value="kostenlose Testlizenz anfragen" class="btn btn-primary btn-block">
                                <i class="fal fa-save"></i> kostenlose Testlizenz anfragen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            Probleme bei der Installation? <br>
            bei Fragen bitte mail an <a href="mailto:info@swiftlysearch.de">info@swiftlysearch.de</a>
            <br><br>
            Alternativ biete ich auch einen pauschalen Installationsservice an. Bitte einfach anfragen!
        </div>
    </div>
</form>