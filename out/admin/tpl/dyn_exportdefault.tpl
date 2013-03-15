[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE_1"|oxmultilangassign skip_onload="true"}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="98%">

<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        <form name="myedit" id="myedit" action="[{ $shop->selflink }]" target="dynexport_do" method="post">
        [{ $shop->hiddensid }]
        <input type="hidden" name="cl" value="[{$sClass_do}]">
        <input type="hidden" name="fnc" value="start">
        <tr>
            <td class="edittext" width="180" height="40" valign="top">
            [{ oxmultilang ident="GENERAL_CATEGORYSELECT" }]
            </td>
            <td class="edittext">
            <select name="acat[]" size="20" multiple class="editinput" style="width: 210px;" [{ $readonly }]>
            [{foreach from=$cattree item=oCat}]
            <option value="[{ $oCat->getId() }]">[{ $oCat->oxcategories__oxtitle->value }]</option>
            [{/foreach}]
            </td>
            </select>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_SEARCHKEY" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="39" maxlength="128" name="search" value="" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            </td>
            <td class="edittext">
            <input type="submit" class="edittext" style="width: 210px;" name="save" value="[{ oxmultilang ident="GENERAL_ESTART" }]" [{ $readonly }]>
            </td>
        </tr>
        </table>

    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left">

        <table cellspacing="0" cellpadding="0" border="0">
        <!--<tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTDELCOST" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportDelCost" value="0,00" [{ $readonly }]> &euro;
            </td>
        </tr>-->
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMINSTOCK" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinStock" value="1" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMINPRICE" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="10" maxlength="10" name="sExportMinPrice" value="0" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPOSTVARS" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportVars" value="true" checked [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTMAINVARS" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blExportMainVars" value="true" checked [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="GENERAL_EXPORTCAMPAIGN" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="20" maxlength="10" name="sExportCampaign" value="" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" width="180" height="40">
            [{ oxmultilang ident="DYNBASE_ADDCATTOCAMPAIGN" }]
            </td>
            <td class="edittext">
            <input type="checkbox" name="blAppendCatToCampaign" value="true">
            </td>
        </tr>
        </table>

        <!--
        Bitte Land f&uuml;r Versandkosten w&auml;hlen : <br>
        <select name="country" class="editinput" style="width: 210px;" [{ $readonly }]>
        [{foreach from=$countrylist item=oCountry}]
        <option value="[{ $oCountry->oxcountry__oxid->value }]">[{ $oCountry->oxcountry__oxtitle->value }]</option>
        [{/foreach}]
        -->
    </td>
    </form>
</tr>
</table>

[{include file="bottomnaviitem.tpl" }]

[{include file="bottomitem.tpl"}]