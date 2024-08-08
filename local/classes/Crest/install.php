<?php
require_once(__DIR__ . '/crest.php');

$result = CRest::installApp();

$events = [
    "ONAPPUNINSTALL",
    "ONAPPINSTALL",
    "ONAPPUPDATE",
    "ONAPPPAYMENT",
    "ONSUBSCRIPTIONRENEW",
    "ONAPPTEST",
    "ONAPPMETHODCONFIRM",
    "ONOFFLINEEVENT",
    "ONIMCONNECTORLINEDELETE",
    "ONIMCONNECTORMESSAGEADD",
    "ONIMCONNECTORMESSAGEUPDATE",
    "ONIMCONNECTORMESSAGEDELETE",
    "ONIMCONNECTORSTATUSDELETE",
    "ONCRMINVOICEADD",
    "ONCRMINVOICEUPDATE",
    "ONCRMINVOICEDELETE",
    "ONCRMINVOICESETSTATUS",
    "ONCRMLEADADD",
    "ONCRMLEADUPDATE",
    "ONCRMLEADDELETE",
    "ONCRMLEADUSERFIELDADD",
    "ONCRMLEADUSERFIELDUPDATE",
    "ONCRMLEADUSERFIELDDELETE",
    "ONCRMLEADUSERFIELDSETENUMVALUES",
    "ONCRMDEALADD",
    "ONCRMDEALUPDATE",
    "ONCRMDEALDELETE",
    "ONCRMDEALMOVETOCATEGORY",
    "ONCRMDEALUSERFIELDADD",
    "ONCRMDEALUSERFIELDUPDATE",
    "ONCRMDEALUSERFIELDDELETE",
    "ONCRMDEALUSERFIELDSETENUMVALUES",
    "ONCRMCOMPANYADD",
    "ONCRMCOMPANYUPDATE",
    "ONCRMCOMPANYDELETE",
    "ONCRMCOMPANYUSERFIELDADD",
    "ONCRMCOMPANYUSERFIELDUPDATE",
    "ONCRMCOMPANYUSERFIELDDELETE",
    "ONCRMCOMPANYUSERFIELDSETENUMVALUES",
    "ONCRMCONTACTADD",
    "ONCRMCONTACTUPDATE",
    "ONCRMCONTACTDELETE",
    "ONCRMCONTACTUSERFIELDADD",
    "ONCRMCONTACTUSERFIELDUPDATE",
    "ONCRMCONTACTUSERFIELDDELETE",
    "ONCRMCONTACTUSERFIELDSETENUMVALUES",
    "ONCRMQUOTEADD",
    "ONCRMQUOTEUPDATE",
    "ONCRMQUOTEDELETE",
    "ONCRMQUOTEUSERFIELDADD",
    "ONCRMQUOTEUSERFIELDUPDATE",
    "ONCRMQUOTEUSERFIELDDELETE",
    "ONCRMQUOTEUSERFIELDSETENUMVALUES",
    "ONCRMINVOICEUSERFIELDADD",
    "ONCRMINVOICEUSERFIELDUPDATE",
    "ONCRMINVOICEUSERFIELDDELETE",
    "ONCRMINVOICEUSERFIELDSETENUMVALUES",
    "ONCRMCURRENCYADD",
    "ONCRMCURRENCYUPDATE",
    "ONCRMCURRENCYDELETE",
    "ONCRMPRODUCTADD",
    "ONCRMPRODUCTUPDATE",
    "ONCRMPRODUCTDELETE",
    "ONCRMPRODUCTPROPERTYADD",
    "ONCRMPRODUCTPROPERTYUPDATE",
    "ONCRMPRODUCTPROPERTYDELETE",
    "ONCRMPRODUCTSECTIONADD",
    "ONCRMPRODUCTSECTIONUPDATE",
    "ONCRMPRODUCTSECTIONDELETE",
    "ONCRMACTIVITYADD",
    "ONCRMACTIVITYUPDATE",
    "ONCRMACTIVITYDELETE",
    "ONCRMREQUISITEADD",
    "ONCRMREQUISITEUPDATE",
    "ONCRMREQUISITEDELETE",
    "ONCRMREQUISITEUSERFIELDADD",
    "ONCRMREQUISITEUSERFIELDUPDATE",
    "ONCRMREQUISITEUSERFIELDDELETE",
    "ONCRMREQUISITEUSERFIELDSETENUMVALUES",
    "ONCRMBANKDETAILADD",
    "ONCRMBANKDETAILUPDATE",
    "ONCRMBANKDETAILDELETE",
    "ONCRMADDRESSREGISTER",
    "ONCRMADDRESSUNREGISTER",
    "ONCRMMEASUREADD",
    "ONCRMMEASUREUPDATE",
    "ONCRMMEASUREDELETE",
    "ONCRMDEALRECURRINGADD",
    "ONCRMDEALRECURRINGUPDATE",
    "ONCRMDEALRECURRINGDELETE",
    "ONCRMDEALRECURRINGEXPOSE",
    "ONCRMINVOICERECURRINGADD",
    "ONCRMINVOICERECURRINGUPDATE",
    "ONCRMINVOICERECURRINGDELETE",
    "ONCRMINVOICERECURRINGEXPOSE",
    "ONCRMTIMELINECOMMENTADD",
    "ONCRMTIMELINECOMMENTUPDATE",
    "ONCRMTIMELINECOMMENTDELETE",
    "ONCRMDYNAMICITEMADD",
    "ONCRMDYNAMICITEMUPDATE",
    "ONCRMDYNAMICITEMDELETE",
    "ONCRMDYNAMICITEMADD_31",
    "ONCRMDYNAMICITEMUPDATE_31",
    "ONCRMDYNAMICITEMDELETE_31",
    "ONCRMTYPEADD",
    "ONCRMTYPEUPDATE",
    "ONCRMTYPEDELETE",
    "ONCRMDOCUMENTGENERATORDOCUMENTADD",
    "ONCRMDOCUMENTGENERATORDOCUMENTUPDATE",
    "ONCRMDOCUMENTGENERATORDOCUMENTDELETE",
    "ONCRMTYPEUSERFIELDADD",
    "ONCRMTYPEUSERFIELDUPDATE",
    "ONCRMTYPEUSERFIELDDELETE",
    "ONCRMTYPEUSERFIELDSETENUMVALUES",
    "ONCRMTIMELINEITEMACTION"
];


/*foreach ($events as $event) {
    $result = CRest::call('event.bind', [
        'EVENT' => $event,
        'HANDLER' => 'https://otus-dev.qwelp.ru/otus/rest/dz25/handler.php'
    ]);
}*/

$result = CRest::call('event.bind', [
    'EVENT' => 'ONIMCONNECTORMESSAGEADD',
    'HANDLER' => 'https://otus-dev.qwelp.ru/otus/rest/dz25/handler.php'
]);

echo "<pre> ONIMCONNECTORMESSAGEADD";
print_r($result);
echo "</pre>";

if ($result['rest_only'] === false):?>
    <head>
        <script src="//api.bitrix24.com/api/v1/"></script>
        <?php if ($result['install'] == true): ?>
            <script>
                BX24.init(function () {
                    BX24.installFinish();
                });
            </script>
        <?php endif; ?>
    </head>
    <body>
    <?php if ($result['install'] == true): ?>
        installation has been finished
    <?php else: ?>
        installation error
    <?php endif; ?>
    </body>
<?php endif;