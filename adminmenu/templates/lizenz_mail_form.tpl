<form class="" method="post" action="{$adminURL}">
    {$jtl_token}
    <input type="hidden" name="adminURL" value="{$adminURL}">
    <h1>Plugin inaktiv</h1>
    <div class="row">
        <div class="col-12">Bitte klicke den Link in der Aktivierunsmail an, um das Plugin zu (re-)aktivieren.
        </div>
        <div class="col-12 mt-4">
            <hr>
        </div>
        <div class="col-12 mt-4">
            <div class="form-group form-row align-items-center">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 mb-4">
                            Solltest Du die Aktivierungsmail nicht mehr oder noch nicht in deinem Postfach haben, bitte einfach nochmal anfragen:
                        </div>
                        <div class="col-auto">
                            <input class="form-control" id="bms_lizenz_mail" name="bms_lizenz_mail" type="email" required value="" placeholder="Ihre Mailadresse">
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
            bei Fragen bitte mail an <a href="mailto:info@swiftlysearch.de">info@swiftlysearch.de</a>
        </div>
    </div>
</form>