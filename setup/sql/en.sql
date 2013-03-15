#A quick way to set english as the default for eShop demodata

#Set English as default language
DELETE FROM `oxconfig` WHERE oxvarname = 'aLanguages';
DELETE FROM `oxconfig` WHERE oxvarname = 'sDefaultLang';
DELETE FROM `oxconfig` WHERE oxvarname = 'aLanguageParams';
INSERT INTO `oxconfig` VALUES ('226f45d61311e0810cebccc8a5035dc2', 'oxbaseshop', 'aLanguages', 'aarr', 0x4dba832f744c5786a371ca8c397de08dfae87deee3a990e86a0b949a1c1491119587773e5168856e000741b33f524d458252e992);
INSERT INTO `oxconfig` VALUES ('2264a1817870ecd67038e929b42ede2a', 'oxbaseshop', 'aLanguageParams', 'aarr', 0x4dba832f744c5786a371ca8c397d859f64f905bbe2b18fd3713157ee3461a76287f66569a2a53eb9389ac7dcf68296847dc5e404801da7ecb34b3af7a9070c2709e9578711d01627ced7588bf6bbc35986fb1e0f00347b12eb6b26a42b233f6c65fce7d0b39fd3abcfa3a10e7779cbe82026d9ac33e2df16f12df15bf4784793595cbe225432febd18d5555371a8818c95ec5b12bc4b31dffcf54acf93ed5a7d14080ff0d0bf67cc63eb18633c716561822c0ebb029771aca4fd9e8c27dc);
INSERT INTO `oxconfig` VALUES ('226bb90adbc31fa02ae79ebfa302c656', 'oxbaseshop', 'sDefaultLang', 'str', 0xde);

#insert USD and set it as default currency
DELETE FROM `oxconfig` WHERE oxvarname = 'aCurrencies';
INSERT INTO `oxconfig` VALUES ('35fde3c68173705037dbebb32b06ac1d', 'oxbaseshop', 'aCurrencies', 'arr', 0x4dbace2972e14bf2cbd3a9a4e65502affef12a8b1770deee941ecabd1e9db4f031b4833877ea92decd25ad81b3c1d7b7fd7c9fb94be704c39da7cd91acedcf97b35abfee324973ba16c3ed9ad94305e9369f9ffe76a4c7f4998950c93dbb488bf1e9f45e26a98096b5b4e17a661fe6f94fb9dd4781c6a9747e2e8326061b46388974b3e352d90d0c853f71c0ae24db911fec255faa6a9135aca573);

#USA as default country
DELETE FROM `oxconfig` WHERE oxvarname = 'aHomeCountry';
INSERT INTO `oxconfig` VALUES ('3c49f66927e19a9b7bba1cf12c6db64e', 'oxbaseshop', 'aHomeCountry', 'arr', 0x4dba322c77e44ef7ced6aca1f35700f1faf1449d20b668839639fa0a2e80391cf6d752f91cff81d994785485);

#swap SEO URLs
UPDATE oxseo SET oxlang = -1 WHERE oxlang=0;
UPDATE oxseo SET oxlang = 0 WHERE oxlang=1;
UPDATE oxseo SET oxlang = 1 WHERE oxlang=-1;

#swap all multilanguage data fields
UPDATE oxactions SET
  OXTITLE = (@TEMP:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP;

UPDATE oxarticles SET
  OXVARNAME = (@TEMP1:=OXVARNAME), OXVARNAME = OXVARNAME_1, OXVARNAME_1 = @TEMP1,
  OXVARSELECT = (@TEMP2:=OXVARSELECT), OXVARSELECT = OXVARSELECT_1, OXVARSELECT_1 = @TEMP2,
  OXTITLE = (@TEMP3:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP3,
  OXSHORTDESC = (@TEMP4:=OXSHORTDESC), OXSHORTDESC = OXSHORTDESC_1, OXSHORTDESC_1 = @TEMP4,
  OXURLDESC = (@TEMP5:=OXURLDESC), OXURLDESC = OXURLDESC_1, OXURLDESC_1 = @TEMP5,
  OXSEARCHKEYS = (@TEMP6:=OXSEARCHKEYS), OXSEARCHKEYS = OXSEARCHKEYS_1, OXSEARCHKEYS_1 = @TEMP6,
  OXSTOCKTEXT = (@TEMP7:=OXSTOCKTEXT), OXSTOCKTEXT = OXSTOCKTEXT_1, OXSTOCKTEXT_1 = @TEMP7,
  OXNOSTOCKTEXT = (@TEMP8:=OXNOSTOCKTEXT), OXNOSTOCKTEXT = OXNOSTOCKTEXT_1, OXNOSTOCKTEXT_1 = @TEMP8;

UPDATE oxartextends SET
  OXLONGDESC = (@TEMP1:=OXLONGDESC), OXLONGDESC = OXLONGDESC_1, OXLONGDESC_1 = @TEMP1,
  OXTAGS = (@TEMP2:=OXTAGS), OXTAGS = OXTAGS_1, OXTAGS_1 = @TEMP2;

UPDATE oxattribute SET
  OXTITLE = (@TEMP:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP;

UPDATE oxcategories SET
  OXACTIVE = (@TEMP1:=OXACTIVE), OXACTIVE = OXACTIVE_1, OXACTIVE_1 = @TEMP1,
  OXTITLE = (@TEMP2:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP2,
  OXDESC = (@TEMP3:=OXDESC), OXDESC = OXDESC_1, OXDESC_1 = @TEMP3,
  OXLONGDESC = (@TEMP4:=OXLONGDESC), OXLONGDESC = OXLONGDESC_1, OXLONGDESC_1 = @TEMP4;

UPDATE oxcontents SET
  OXACTIVE = (@TEMP1:=OXACTIVE), OXACTIVE = OXACTIVE_1, OXACTIVE_1 = @TEMP1,
  OXTITLE = (@TEMP2:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP2,
  OXCONTENT = (@TEMP3:=OXCONTENT), OXCONTENT = OXCONTENT_1, OXCONTENT_1 = @TEMP3;

UPDATE oxcountry SET
  OXTITLE = (@TEMP1:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP1,
  OXSHORTDESC = (@TEMP2:=OXSHORTDESC), OXSHORTDESC = OXSHORTDESC_1, OXSHORTDESC_1 = @TEMP2,
  OXLONGDESC = (@TEMP3:=OXLONGDESC), OXLONGDESC = OXLONGDESC_1, OXLONGDESC_1 = @TEMP3;

UPDATE oxdelivery SET
  OXTITLE = (@TEMP:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP;

UPDATE oxdiscount SET
  OXTITLE = (@TEMP:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP;

UPDATE oxlinks SET
  OXURLDESC = (@TEMP:=OXURLDESC), OXURLDESC = OXURLDESC_1, OXURLDESC_1 = @TEMP;

UPDATE oxnews SET
  OXACTIVE = (@TEMP1:=OXACTIVE), OXACTIVE = OXACTIVE_1, OXACTIVE_1 = @TEMP1,
  OXSHORTDESC = (@TEMP2:=OXSHORTDESC), OXSHORTDESC = OXSHORTDESC_1, OXSHORTDESC_1 = @TEMP2,
  OXLONGDESC = (@TEMP3:=OXLONGDESC), OXLONGDESC = OXLONGDESC_1, OXLONGDESC_1 = @TEMP3;

UPDATE oxobject2attribute SET
  OXVALUE = (@TEMP:=OXVALUE), OXVALUE = OXVALUE_1, OXVALUE_1 = @TEMP;

UPDATE oxpayments SET
  OXDESC = (@TEMP1:=OXDESC), OXDESC = OXDESC_1, OXDESC_1 = @TEMP1,
  OXVALDESC = (@TEMP2:=OXVALDESC), OXVALDESC = OXVALDESC_1, OXVALDESC_1 = @TEMP2,
  OXLONGDESC = (@TEMP3:=OXLONGDESC), OXLONGDESC = OXLONGDESC_1, OXLONGDESC_1 = @TEMP3;

update oxselectlist SET
  OXTITLE = (@TEMP1:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP1,
  OXVALDESC = (@TEMP2:=OXVALDESC), OXVALDESC = OXVALDESC_1, OXVALDESC_1 = @TEMP2;

update oxshops SET
  OXTITLEPREFIX = (@TEMP1:=OXTITLEPREFIX), OXTITLEPREFIX = OXTITLEPREFIX_1, OXTITLEPREFIX_1 = @TEMP1,
  OXTITLESUFFIX = (@TEMP2:=OXTITLESUFFIX), OXTITLESUFFIX = OXTITLESUFFIX_1, OXTITLESUFFIX_1 = @TEMP2,
  OXSTARTTITLE = (@TEMP3:=OXSTARTTITLE), OXSTARTTITLE = OXSTARTTITLE_1, OXSTARTTITLE_1 = @TEMP3,
  OXORDERSUBJECT = (@TEMP4:=OXORDERSUBJECT), OXORDERSUBJECT = OXORDERSUBJECT_1, OXORDERSUBJECT_1 = @TEMP4,
  OXREGISTERSUBJECT = (@TEMP5:=OXREGISTERSUBJECT), OXREGISTERSUBJECT = OXREGISTERSUBJECT_1, OXREGISTERSUBJECT_1 = @TEMP5,
  OXFORGOTPWDSUBJECT = (@TEMP6:=OXFORGOTPWDSUBJECT), OXFORGOTPWDSUBJECT = OXFORGOTPWDSUBJECT_1, OXFORGOTPWDSUBJECT_1 = @TEMP6,
  OXSENDEDNOWSUBJECT = (@TEMP7:=OXSENDEDNOWSUBJECT), OXSENDEDNOWSUBJECT = OXSENDEDNOWSUBJECT_1, OXSENDEDNOWSUBJECT_1 = @TEMP7,
  OXSEOACTIVE = (@TEMP8:=OXSEOACTIVE), OXSEOACTIVE = OXSEOACTIVE_1, OXSEOACTIVE_1 = @TEMP8;

UPDATE oxwrapping SET
  OXACTIVE = (@TEMP1:=OXACTIVE), OXACTIVE = OXACTIVE_1, OXACTIVE_1 = @TEMP1,
  OXNAME = (@TEMP2:=OXNAME), OXNAME = OXNAME_1, OXNAME_1 = @TEMP2;

UPDATE oxdeliveryset SET
  OXTITLE = (@TEMP:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP;

UPDATE oxvendor SET
  OXTITLE = (@TEMP1:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP1,
  OXSHORTDESC = (@TEMP2:=OXSHORTDESC), OXSHORTDESC = OXSHORTDESC_1, OXSHORTDESC_1 = @TEMP2;

UPDATE oxmanufacturers SET
  OXTITLE = (@TEMP1:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP1,
  OXSHORTDESC = (@TEMP2:=OXSHORTDESC), OXSHORTDESC = OXSHORTDESC_1, OXSHORTDESC_1 = @TEMP2;

UPDATE oxmediaurls SET
  OXDESC = (@TEMP:=OXDESC), OXDESC = OXDESC_1, OXDESC_1 = @TEMP;

UPDATE oxstates SET
  OXTITLE = (@TEMP:=OXTITLE), OXTITLE = OXTITLE_1, OXTITLE_1 = @TEMP;

#English newsletter sample
REPLACE INTO `oxnewsletter` VALUES ('oxidnewsletter', 'oxbaseshop', 'Newsletter Example', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">\r\n<html>\r\n<head>\r\n<title>OXID eSales Newsletter</title>\r\n<style media="screen" type="text/css"><!--\r\nA        {\r\n        font-size: 9pt;\r\n        text-decoration: none;\r\n        color: black;\r\n        }\r\nA:Hover     {\r\n        text-decoration: underline;\r\n        color: #AB0101;\r\n        }\r\nbody    {\r\n    margin-bottom : 0;\r\n    margin-left : 0;\r\n    margin-right : 0;\r\n    margin-top : 0;\r\n    background-color: #FFFFFF;\r\n}\r\n.pagehead {\r\n font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n font-size: 10px;\r\n    color: #000000;\r\n font-weight: normal;\r\n    background-color: #494949;\r\n  height : 50;\r\n    vertical-align : bottom;\r\n}\r\n.pageheadlink {\r\n    font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n font-size: 11px;\r\n    color: #F7F7F7;\r\n font-weight: normal;\r\n}\r\n.pagebottom {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 9px;\r\n        color: #000000;\r\n        font-weight: normal;\r\n     height : 13;\r\n        vertical-align : top;\r\n}\r\n.defaultcontent {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #000000;\r\n        font-weight: normal;\r\n       vertical-align : top;\r\n}\r\n.detailcontent {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #000000;\r\n        font-weight: normal;\r\n        vertical-align : top;\r\n       padding-left: 10px;\r\n}\r\n.detailproductlink {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 9px;\r\n        color: #9D0101;\r\n        font-weight: bold;\r\n}\r\n.detailheader {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 11px;\r\n        color: #9D0101;\r\n        font-weight: bold;\r\n}\r\n.detailsales {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #000000;\r\n        font-weight: bold;\r\n      background-color: #CECDCD;\r\n}\r\n.aktionhead {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #FFFFFF;\r\n        font-weight: bold;\r\n        background-color: #767575;\r\n}\r\n.aktionmain {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #000000;\r\n        font-weight: normal;\r\n      border : 3px #767575;\r\n       border-style : none solid solid solid;\r\n      padding-left : 2px;\r\n     padding-top : 5px;\r\n      padding-bottom : 5px;\r\n       padding-right : 2px;\r\n}\r\n.aktion {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #000000;\r\n        font-weight: normal;\r\n}\r\n.aktionhighlight {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #AB0101;\r\n        font-weight: bold;\r\n}\r\n.startpageFirstProductTitle {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 12px;\r\n        color: #AB0101;\r\n        font-weight: bold;\r\n}\r\n.startpageFirstProductText {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #242424;\r\n        font-weight: normal;\r\n}\r\n.startpageFirstProductPrice {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 17px;\r\n        color: #AB0101;\r\n        font-weight: bold;\r\n}\r\n.startpageFirstProductOldPrice {\r\n font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n font-size: 17px;\r\n    color: #AB0101;\r\n font-weight: bold;\r\n  text-decoration : line-through;\r\n}\r\n.startpageProductTitle {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 12px;\r\n        color: #242424;\r\n        font-weight: bold;\r\n}\r\n.startpageProductText {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #AB0101;\r\n        font-weight: normal;\r\n}\r\n.startpageBoxContent {\r\n   font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n font-size: 10px;\r\n    color: #000000;\r\n font-weight: normal;\r\n    border : 3px #CECDCD;\r\n   border-style : none solid solid solid;\r\n  padding-left : 5px;\r\n padding-top : 5px;\r\n  padding-bottom : 5px;\r\n   padding-right : 5px;\r\n}\r\n.startpageBoxHead {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #000000;\r\n        font-weight: bold;\r\n        background-color: #CECDCD;\r\n}\r\n.newestProductHead {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #8D0101;\r\n        font-weight: bold;\r\n}\r\n.newestProduct {\r\n        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;\r\n        font-size: 10px;\r\n        color: #000000;\r\n        font-weight: normal;\r\n}\r\n}\r\n--></style>\r\n</head>\r\n<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">\r\n<table width="780" height="100%" cellspacing="0" cellpadding="0" border="0"><!-- Kopf Start --><tbody><tr><td class="pagehead">\r\n<table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td width="152" align="right" class="pagehead"> <a class="pageheadlink" href="[{$oViewConf->getBaseDir()}]"><img border="0" alt="" src="[{$oViewConf->getImageUrl()}]/logo.png"></a> </td></tr></tbody></table></td></tr><tr><td height="15"> <br>\r\n</td></tr><!-- Kopf Ende --> <!-- Content Start --><tr><td valign="top">\r\n<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="defaultcontent">\r\nHello [{ $myuser->oxuser__oxsal->value|oxmultilangsal }] [{ $myuser->oxuser__oxfname->value }] [{ $myuser->oxuser__oxlname->value }],<br>\r\n<br>\r\nas you can see, our newsletter works really well.<br>\r\n<br>\r\nIt is not only possible to display your address here:<br>\r\n[{ $myuser->oxuser__oxaddinfo->value }]<br>\r\n[{ $myuser->oxuser__oxstreet->value }]<br>\r\n[{ $myuser->oxuser__oxzip->value }] [{ $myuser->oxuser__oxcity->value }]<br>\r\n[{ $myuser->oxuser__oxcountry->value }]<br>\r\nPhone: [{ $myuser->oxuser__oxfon->value }]<br>\r\n<br>\r\nYou want to unsubscribe from our newsletter? No problem - simply click <a class="defaultcontent" href="[{$oViewConf->getBaseDir()}]index.php?cl=newsletter&fnc=removeme&uid=[{$myuser->oxuser__oxid->value}]">here</a>.\r\n<br>\r\n<br>\r\n [{if isset($simarticle0) }]\r\n     This is a similar product related to your last order:<br>\r\n\r\n<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td> <a href="[{$simarticle0->oxdetaillink}]" class="startpageProduct"><img vspace="0" hspace="0" border="0" alt="[{ $simarticle0->oxarticles__oxtitle->value }]" src="[{$oViewConf->getPictureDir()}][{$simarticle0->oxarticles__oxpic1->value }]"></a> </td><td width="10" valign="top" class="startpageFirstProductTitle">*</td><td width="320" valign="top" class="startpageFirstProductTitle"> [{ $simarticle0->oxarticles__oxtitle->value }]<br>\r\n <br>\r\n <span class="startpageFirstProductText">[{ $simarticle0->oxarticles__oxshortdesc->value }]</span><br>\r\n <br>\r\n <span class="startpageProductText"><strong>Now </strong></span><span class="startpageFirstProductPrice">[{ $mycurrency->sign}][{ $simarticle0->fprice }]</span> instead of <span class="startpageFirstProductOldPrice">[{ $mycurrency->sign}][{ $simarticle0->ftprice}]</span><br>\r\n <br>\r\n <a href="[{$simarticle0->oxdetaillink}]" class="startpageProductText"><strong>more information</strong></a><br>\r\n </td></tr></tbody></table> [{/if}] <br>\r\n<br>\r\n [{if isset($articlelist) }]\r\n     Assorted products from our store especially for this newsletter: <br>\r\n\r\n<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td height="1" bgcolor="#cecdcd"><br>\r\n</td><td height="1" bgcolor="#cecdcd"><br>\r\n</td></tr><tr><td height="7"><br>\r\n</td><td><br>\r\n</td></tr>[{assign var="iPos" value=1}]\r\n       [{foreach from=$articlelist item=product}]\r\n     \r\n        [{if $iPos == 1}] <tr><td valign="top">\r\n<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td width="220" height="100" align="left" class="startpageProduct"> <a href="[{$product->oxdetaillink}]" class="startpageProduct"><img vspace="0" hspace="0" border="0" alt="[{ $product->oxarticles__oxtitle->value }]" src="[{$oViewConf->getPictureDir()}][{$product->oxarticles__oxthumb->value }]"></a> </td></tr><tr><td align="left" class="startpageProductTitle"> [{ $product->oxarticles__oxtitle->value }] </td></tr><tr><td height="20" align="left" class="startpageProductText"> <strong>Only [{ $mycurrency->sign}][{ $product->fprice }]</strong> </td></tr><tr><td height="20" align="left" class="startpageProductText"> <a href="[{$product->oxdetaillink}]" class="startpageProductText">more information</a><br>\r\n </td></tr></tbody></table> </td>[{assign var="iPos" value=2}]\r\n       [{elseif $iPos==2}] <td valign="top">\r\n<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td width="220" height="100" align="left" class="startpageProduct"> <a href="[{$product->oxdetaillink}]" class="startpageProduct"><img vspace="0" hspace="0" border="0" alt="[{ $product->oxarticles__oxtitle->value }]" src="[{$oViewConf->getPictureDir()}][{$product->oxarticles__oxthumb->value }]"></a> </td></tr><tr><td align="left" class="startpageProductTitle"> [{ $product->oxarticles__oxtitle->value }] </td></tr><tr><td height="20" align="left" class="startpageProductText"> <strong>Only [{ $mycurrency->sign}][{ $product->fprice }]</strong> </td></tr><tr><td height="20" align="left" class="startpageProductText"> <a href="[{$product->oxdetaillink}]" class="startpageProductText">more information</a><br>\r\n </td></tr></tbody></table> </td></tr><tr><td height="7"><br>\r\n</td><td><br>\r\n</td></tr><tr><td height="1" bgcolor="#cecdcd"><br>\r\n</td><td height="1" bgcolor="#cecdcd"><br>\r\n</td></tr><tr><td height="7"><br>\r\n</td><td><br>\r\n</td></tr><!-- end of line --> [{assign var="iPos" value=1}]\r\n       [{/if}]\r\n     [{/foreach}] <!-- adjust missing --> [{if $iPos == 1}] <tr><td><br>\r\n</td></tr>[{/if}] </tbody></table> [{/if}] <br>\r\n </td><td width="165" align="right" class="defaultcontent"> [{ if $simarticle1 }]\r\n     This is a similar product related to your last order as well:<br>\r\n\r\n<table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td width="100%" height="15" align="center" class="aktionhead">Top Bargain of the Week</td></tr><tr><td class="aktionmain">\r\n<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="aktion"><a href="[{$simarticle1->oxdetaillink}]" class="aktion"><img vspace="0" hspace="0" border="0" alt="[{ $simarticle1->oxarticles__oxtitle->value }]" src="[{$oViewConf->getPictureDir()}][{$simarticle1->oxarticles__oxthumb->value }]"></a></td></tr><tr><td height="15" class="aktion">[{ $simarticle1->oxarticles__oxtitle->value }]</td><td class="aktion"><br>\r\n</td></tr><tr><td height="15" class="aktionhighlight"><strong>Only [{ $mycurrency->sign}][{ $simarticle1->fprice }]!!</strong></td></tr><tr><td height="25" class="aktion">\r\n<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td width="120" class="aktion"><a href="[{$simarticle1->oxdetaillink}]" class="aktion">more information</a></td></tr></tbody></table> </td></tr></tbody></table> </td></tr></tbody></table> [{ /if }] <br>\r\n <br>\r\n [{ if $simarticle2 }]\r\n       And at last a similar product related to your last order again:<br>\r\n\r\n<table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td width="165" height="15" align="center" class="aktionhead">Bargain!</td></tr><tr><td valign="top" height="145" class="aktionmain"> You will get our bestseller <a class="aktionhighlight" href="[{$simarticle2->oxdetaillink}]">[{ $simarticle2->oxarticles__oxtitle->value }]</a> in a special edition on a suitable price exklusively at OXID!<br>\r\n Order <a class="aktionhighlight" href="[{$simarticle2->tobasketlink}]&am=1">now</a> !<br>\r\n </td></tr></tbody></table> [{/if}] </td></tr></tbody></table> </td></tr><tr><td align="right" class="pagebottom">\r\n� 2009 OXID </td></tr></tbody></table>\r\n</body>\r\n</html>', 'OXID eSales Newsletter\r\n\r\nHello [{ $myuser->oxuser__oxsal->value|oxmultilangsal }] [{ $myuser->oxuser__oxfname->getRawValue() }] [{ $myuser->oxuser__oxlname->getRawValue() }],\r\n\r\nas you can see, our newsletter works really well.\r\n\r\nIt is not only possible to display your address here:\r\n\r\n[{ $myuser->oxuser__oxaddinfo->getRawValue() }]\r\n[{ $myuser->oxuser__oxstreet->getRawValue() }]\r\n[{ $myuser->oxuser__oxzip->value }] [{ $myuser->oxuser__oxcity->getRawValue() }]\r\n[{ $myuser->oxuser__oxcountry->getRawValue() }]\r\nPhone: [{ $myuser->oxuser__oxfon->value }]\r\n\r\nYou want to unsubscribe from our newsletter? No problem - simply click here: [{$oViewConf->getBaseDir()}]index.php?cl=newsletter&fnc=removeme&uid=[{ $myuser->oxuser__oxid->value}]\r\n\r\n[{if isset($simarticle0) }]\r\n   This is a similar product related to your last order:\r\n \r\n    [{ $simarticle0->oxarticles__oxtitle->getRawValue() }] \r\nOnly [{ $mycurrency->name}][{ $simarticle0->fprice }] instead of [{ $mycurrency->name}][{ $simarticle0->ftprice}]\r\n[{/if}]\r\n\r\n[{if isset($articlelist) }]\r\n  Assorted products from our store especially for this newsletter: \r\n     [{foreach from=$articlelist item=product}]  \r\n        [{ $product->oxarticles__oxtitle->getRawValue() }]   Only [{ $mycurrency->name}][{ $product->fprice }]\r\n    [{/foreach}] \r\n[{/if}]');

#US as a default user country
UPDATE oxuser SET oxstateid = 'CA', oxcountryid = '8f241f11096877ac0.98748826';