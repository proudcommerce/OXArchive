[{assign var="template_title" value="MALLSTART_TITLE"|oxmultilangassign}]
[{include file="_header_plain.tpl" title=$template_title location=$template_title allowindexing=true meta_description=$meta_description meta_keywords=$meta_keywords}]

    <div class="mallbox">

        <div class="mallhead">
            <div><a href="[{ $oViewConf->getSelfLink() }]"><img src="[{$oViewConf->getImageUrl()}]/logo.png"  class="logo_header" alt=""></a></div>
            [{if $oView->getMenueList()}]
              <ul class="mallmenu">
                [{ foreach from=$oView->getMenueList() item=oMenueContent name=mainmenu }]
                  <li[{if $smarty.foreach.mainmenu.last}] class="last"[{/if}]><a href="[{ $oMenueContent->getLink() }]">[{$oMenueContent->oxcontents__oxtitle->value}]</a></li>
                [{/foreach}]
              </ul>
            [{/if}]
        </div>

    <div class="langbox">
       [{ include file="inc/cmp_lang.tpl" }]
    </div>

    <div class="locationbox">
       [{ oxmultilang ident="MALLSTART_LOCATION" }]
    </div>

    [{if $oView->isDemoShop()}]
      [{ include file="inc/admin_banner.tpl" }]
    [{/if}]

    <div class="welcomebox"
        [{ oxcontent ident="oxstartwelcome" }]

        <div class="shopselectbox">
            [{ oxmultilang ident="MALLCATITEM_PLEASECHOOSE" }]<br>
            <br>
            [{assign var="shoplinks" value=$oView->getShopLinks()}]
            [{assign var="shoplangs" value=$oView->getShopDefaultLangs()}]
            [{foreach from=$oView->getShopList() item=mallshop key=shopid}]
              [{if $shoplinks.$shopid}]
                <a href="[{$shoplinks.$shopid}]">[{ $mallshop->oxshops__oxname->value }]</a><br>
              [{else}]
                <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=mallstart" params="fnc=chshp&amp;shp=`$mallshop->oxshops__oxid->value`&amp;lang=`$shoplangs.$shopid`" }]" >[{ $mallshop->oxshops__oxname->value }]</a><br>
              [{/if}]
            [{/foreach}]
         </div>
    </div>


    <div class="mallfooter">
        <div class="copyright">
            <div class="right">&copy; <a href="http://www.oxid-esales.com">[{ oxmultilang ident="MALLSTART_OXIDSOFTWARE" }]</a> &nbsp;</div>
            <div class="right"><img src="[{$oViewConf->getImageDir()}]/barrcode.gif" alt="">&nbsp;</div>
        </div>
        <br><br>
        <div class="left"><img src="[{$oViewConf->getImageDir()}]/cc.jpg" alt=""></div>
        <div class="right"><a href="http://www.oxid-esales.com"><img src="[{$oViewConf->getImageDir()}]/oxid_powered.jpg" alt="[{ oxmultilang ident="MALLSTART_OXIDSOFTWAREALT" }]"></a></div>
      </div>
    </div>


[{include file="_footer_plain.tpl"}]