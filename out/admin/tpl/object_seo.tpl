[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{* if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if*}]

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="oxidCopy" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="[{ $shop->cl }]">
    <input type="hidden" name="language" value="[{ $actlang }]">
</form>


<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="[{ $shop->cl }]">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="language" value="[{ $actlang }]">

        <table border="0" width="98%">

        [{if $blShowCatSelect }]
        <tr>
            <td class="edittext" width="120">
            [{ oxmultilang ident="GENERAL_SEO_ACTCAT" }]
            </td>
            <td class="edittext">
            <select [{ $readonly }] onChange="document.myedit.submit();" name="aSeoData[oxparams]">

              [{ if $oCategories && $oCategories->count() }]
                [{assign var="blCat" value="1"}]
                <optgroup label="[{ oxmultilang ident="GENERAL_SEO_CAT" }]">
                [{ foreach from=$oCategories item=oCategory }]
                <option value="oxcategory#[{if $oCategory->isPriceCategory() }][{ $oCategory->getId() }][{else}][{$oCategory->oxcategories__oxrootid->value}][{/if}]" [{if ( $oCategory->isPriceCategory() && $sCatId == $oCategory->getId() ) || $sCatId == $oCategory->oxcategories__oxrootid->value}]selected[{/if}]>[{$oCategory->oxcategories__oxtitle->value}]</option>
                [{ /foreach }]
                </optgroup>
              [{/if}]

              [{ if $oVendors && $oVendors->count() }]
                [{assign var="blCat" value="1"}]
                <optgroup label="[{ oxmultilang ident="GENERAL_SEO_VND" }]">
                [{ foreach from=$oVendors item=oVendor }]
                <option value="oxvendor#[{$oVendor->oxvendor__oxid->value}]" [{if $sCatType && $sCatType == 'oxvendor' && $sCatId == $oVendor->oxvendor__oxid->value}]selected[{/if}]>[{$oVendor->oxvendor__oxtitle->value}]</option>
                [{ /foreach }]
                </optgroup>
              [{/if}]

              [{ if $oManufacturers && $oManufacturers->count() }]
                [{assign var="blCat" value="1"}]
                <optgroup label="[{ oxmultilang ident="GENERAL_SEO_MANUFACTURER" }]">
                [{ foreach from=$oManufacturers item=oManufacturer }]
                <option value="oxmanufacturer#[{$oManufacturer->oxmanufacturers__oxid->value}]" [{if $sCatType && $sCatType == 'oxmanufacturer' && $sCatId == $oManufacturer->oxmanufacturers__oxid->value}]selected[{/if}]>[{$oManufacturer->oxmanufacturers__oxtitle->value}]</option>
                [{ /foreach }]
                </optgroup>
              [{/if}]

              [{ if $oTags && count($oTags) }]
                [{assign var="blCat" value="1"}]
                [{ foreach from=$oTags key=iLang item=aLangTags }]
                  [{assign var="oTagLang" value=$otherlang.$iLang }]
                  <optgroup label="[{ oxmultilang ident="GENERAL_SEO_TAG" }] [{ $oTagLang->sLangDesc }]">
                    [{ foreach from=$aLangTags key=sTag item=sItem }]
                    <option value="oxtag#[{$sTag}]#[{$iLang}]" [{if $sCatType && $sCatType == 'oxtag' && $sCatId == $sTag && $sCatLang == $iLang }]selected[{/if}]>[{$sTag}]</option>
                    [{ /foreach }]
                  </optgroup>
                [{ /foreach }]
              [{/if}]

              [{if !$blCat}]
                <option value="">--</option>
              [{/if}]

              </optgroup>

            </select>
            </td>
        </tr>
        [{/if}]


        <tr>
            <td class="edittext" width="120">
            [{ oxmultilang ident="GENERAL_SEO_FIXED" }]
            </td>
            <td class="edittext">
            <input class="edittext" type="checkbox" name="aSeoData[oxfixed]" value='1' [{if $aSeoData.OXFIXED == 1}]checked[{/if}] [{ $readonly }]>
            </td>
        </tr>

        [{if $blShowSuffixEdit }]
        <tr>
            <td class="edittext" width="120">
            [{ oxmultilang ident="GENERAL_SEO_SHOWSUFFIX" }]
            </td>
            <td class="edittext">
            <input class="edittext" type="checkbox" name="blShowSuffix" value='1' [{if $blShowSuffix == 1}]checked[{/if}] [{ $readonly }]>
            </td>
        </tr>
        [{/if}]

        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_SEO_URL" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" style="width: 100%;" name="aSeoData[oxseourl]" value="[{ $aSeoData.OXSEOURL }]" [{ $readonly }]>
            </td>
        </tr>

        <tr>
            <td class="edittext" valign="top">
              [{ oxmultilang ident="GENERAL_SEO_OXKEYWORDS" }]
            </td>
            <td class="edittext">
              <textarea type="text" class="editinput" style="width: 100%; height: 78px"  name="aSeoData[oxkeywords]" [{ $readonly }]>[{ $aSeoData.OXKEYWORDS }]</textarea>
            </td>
        </tr>

        <tr>
            <td class="edittext" valign="top">
              [{ oxmultilang ident="GENERAL_SEO_OXDESCRIPTION" }]
            </td>
            <td class="edittext">
              <textarea type="text" class="editinput" style="width: 100%; height: 78px"  name="aSeoData[oxdescription]" [{ $readonly }]>[{ $aSeoData.OXDESCRIPTION }]</textarea>
            </td>
        </tr>


        [{if $oxid != "-1"}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{if $sCatType == 'oxtag' }][{assign var="blTags" value="readonly disabled"}][{else}][{assign var="blTags" value=$readonly}][{/if}]
                [{include file="language_edit.tpl" readonly=$blTags }]
            </td>
        </tr>
        [{/if}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext">
            <input type="submit" class="edittext" onclick="document.getElementById('myedit').fnc.value='save';" name="saveArticle" value="[{ oxmultilang ident="GENERAL_SAVE" }]" [{ $readonly }]><br>
            </td>
        </tr>


        </table>


</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]