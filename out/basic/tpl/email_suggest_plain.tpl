[{ oxmultilang ident="EMAIL_SUGGEST_HTML_PRODUCTPOSTCARDFROM" }] [{ $shop->oxshops__oxname->getRawValue() }]

[{ oxmultilang ident="EMAIL_SUGGEST_HTML_FROM" }] [{$userinfo->send_name}]
[{ oxmultilang ident="EMAIL_SUGGEST_HTML_EMAIL" }] [{$userinfo->send_email}]

[{ oxmultilang ident="EMAIL_SUGGEST_HTML_TO" }] [{$userinfo->rec_name}]
[{ oxmultilang ident="EMAIL_SUGGEST_HTML_EMAIL2" }] [{$userinfo->rec_email}]

[{$userinfo->send_message}]

[{ oxmultilang ident="EMAIL_SUGGEST_HTML_MENYGREETINGS" }] [{$userinfo->send_name}]

[{ oxmultilang ident="EMAIL_SUGGEST_PLAIN_RECOMMENDED" }]

[{ $product->oxarticles__oxtitle->value|strip_tags }]
[{ $product->oxarticles__oxshortdesc->value }]

[{ oxmultilang ident="EMAIL_SUGGEST_PLAIN_CHECK" }] [{ $oViewConf->getBaseDir()  }]index.php?cl=details&anid=[{ $product->sOXID}]&shp=[{ $shop->oxshops__oxid->value }]

[{ oxcontent ident="oxemailfooterplain" }]