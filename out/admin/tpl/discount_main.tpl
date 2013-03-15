[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
[{ if $updatelist == 1}]
    UpdateList('[{ $oxid }]');
[{ /if}]

function UpdateList( sID)
{
    var oSearch = parent.list.document.getElementById("search");
    oSearch.oxid.value=sID;
    oSearch.submit();
}

function ChangeDiscountType(oObj)
{   var oHObj = document.getElementById("itmart");
    var oDObj = document.getElementById("editval[oxdiscount__oxaddsum]");
    if ( oDObj != null && oHObj != null && oObj != null)
    {   if ( oObj.value == "itm")
        {   oHObj.style.display = "";
            oDObj.style.display = "none";
        }
        else
        {   oHObj.style.display = "none";
            oDObj.style.display = "";
        }
    }
}

function ChangeLanguage(obj)
{
    var oTransfer = document.getElementById("transfer");
    oTransfer.language.value=obj.value;
    oTransfer.submit();
}

function SetSticker( sStickerId, oObject)
{
    if ( oObject.selectedIndex != -1)
    {   oSticker = document.getElementById(sStickerId);
        oSticker.style.display = "";
        oSticker.style.backgroundColor = "#FFFFCC";
        oSticker.style.borderWidth = "1px";
        oSticker.style.borderColor = "#000000";
        oSticker.style.borderStyle = "solid";
        oSticker.innerHTML         = oObject.item(oObject.selectedIndex).innerHTML;
    }
    else
        oSticker.style.display = "none";
}
//-->
</script>

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="oxidCopy" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="discount_main">
    <input type="hidden" name="language" value="[{ $actlang }]">
</form>

<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="discount_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxdiscount__oxid]" value="[{ $oxid }]">
<input type="hidden" name="language" value="[{ $actlang }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="edittext" width="120">
            [{ oxmultilang ident="GENERAL_NAME" }]
            </td>
            <td class="edittext" width="250">
            <input type="text" class="editinput" size="50" maxlength="[{$edit->oxdiscount__oxtitle->fldmax_length}]" name="editval[oxdiscount__oxtitle]" value="[{$edit->oxdiscount__oxtitle->value}]" [{ $readonly }]>
            </td>
        </tr>
        [{ if $oxid != "-1"}]
        <tr>
            <td class="edittext" width="120">
            [{ oxmultilang ident="GENERAL_ACTIVE" }]
            </td>
            <td class="edittext">
            <input class="edittext" type="checkbox" name="editval[oxdiscount__oxactive]" value='1' [{if $edit->oxdiscount__oxactive->value == 1}]checked[{/if}] [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_ACTIVFROMTILL" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="27" name="editval[oxdiscount__oxactivefrom]" value="[{$edit->oxdiscount__oxactivefrom|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{ $readonly }]>[{ oxmultilang ident="DISCOUNT_MAIN_AFROM" }]<br>
            <input type="text" class="editinput" size="27" name="editval[oxdiscount__oxactiveto]" value="[{$edit->oxdiscount__oxactiveto|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{ $readonly }]>[{ oxmultilang ident="DISCOUNT_MAIN_ATILL" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="DISCOUNT_MAIN_AMOUNT" }]
            </td>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_FROM" }] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxamount->fldmax_length}]" name="editval[oxdiscount__oxamount]" value="[{$edit->oxdiscount__oxamount->value}]" [{ $readonly }]>
            [{ oxmultilang ident="GENERAL_TILL" }] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxamountto->fldmax_length}]" name="editval[oxdiscount__oxamountto]" value="[{$edit->oxdiscount__oxamountto->value}]" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="DISCOUNT_MAIN_PRICE" }]
            </td>
            <td class="edittext">
            [{ oxmultilang ident="GENERAL_FROM" }] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxprice->fldmax_length}]" name="editval[oxdiscount__oxprice]" value="[{$edit->oxdiscount__oxprice->value}]" [{ $readonly }]>
            [{ oxmultilang ident="GENERAL_TILL" }] <input type="text" class="editinput" size="10" maxlength="[{$edit->oxdiscount__oxpriceto->fldmax_length}]" name="editval[oxdiscount__oxpriceto]" value="[{$edit->oxdiscount__oxpriceto->value}]" [{ $readonly }]>
            </td>
        </tr>
        <tr>
            <td class="edittext" height="30">
            [{ oxmultilang ident="DISCOUNT_MAIN_REBATE" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="15" maxlength="[{$edit->oxdiscount__oxaddsum->fldmax_length}]" name="editval[oxdiscount__oxaddsum]" id="editval[oxdiscount__oxaddsum]" value="[{$edit->oxdiscount__oxaddsum->value }]" style="display: [{if $itm_disp == ""}]none[{/if}];" [{ $readonly }]>
                <select name="editval[oxdiscount__oxaddsumtype]" class="editinput" [{include file="help.tpl" helpid=addsumitmtype}] onChange="Javascript:ChangeDiscountType(this);" [{ $readonly }]>
                [{foreach from=$sumtype item=sum}]
                <option value="[{ $sum }]" [{ if $sum == $edit->oxdiscount__oxaddsumtype->value}]SELECTED[{/if}]>[{ $sum }]</option>
                [{/foreach}]
                </select>
            </td>
        </tr>
        <tr id="itmart" style="display: [{$itm_disp}];">
            <td class="edittext" height="30">
            [{ oxmultilang ident="DISCOUNT_MAIN_EXTRA" }]
            </td>

            <td class="edittext">

                <table cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td class="edittext" valign="bottom">
                      <select name="editval[oxdiscount__oxitmartid]" class="editinput" style="width:220px;" [{ $readonly }]>
                      [{foreach from=$itmarttree item=pcat}]
                        <option value="[{ $pcat->oxarticles__oxid->value }]" [{ if $pcat->selected}]SELECTED[{/if}]>[{ $pcat->oxarticles__oxartnum->value }] [{ $pcat->oxarticles__oxtitle->value }]</option>
                      [{/foreach}]
                      </select>
                    </td>
                    <td class="edittext" valign="bottom">
                      [{ oxmultilang ident="DISCOUNT_MAIN_MULTIPLY_DISCOUNT_AMOUNT" }]
                      <input type="text" class="editinput" size="5" maxlength="[{$edit->oxdiscount__oxitmamount->fldmax_length}]" name="editval[oxdiscount__oxitmamount]" value="[{$edit->oxdiscount__oxitmamount->value}]" [{ $readonly }]>
                       </td>
                       <td class="edittext" valign="bottom">
                      [{ oxmultilang ident="DISCOUNT_MAIN_MULTIPLY_DISCOUNT_ARTICLES" }]
                      <input type="hidden" name="editval[oxdiscount__oxitmmultiple]" value="0">
                      <input class="edittext" type="checkbox" name="editval[oxdiscount__oxitmmultiple]" value='1' [{if $edit->oxdiscount__oxitmmultiple->value == 1}]checked[{/if}] [{ $readonly }]>
                       </td>
                    <td class="edittext" valign="bottom">
                         [{ oxmultilang ident="GENERAL_FROMCATEGORY" }]
                      <select name="itmartcat" class="editinput" onChange="Javascript:document.myedit.fnc.value='save';document.myedit.submit();" [{ $readonly }]>
                      [{foreach from=$artcattree->aList item=pcat}]
                      <option value="[{ $pcat->oxcategories__oxid->value }]" [{ if $pcat->selected}]SELECTED[{/if}]>[{ $pcat->oxcategories__oxtitle->value }]</option>
                      [{/foreach}]
                      </select>
                    </td>
                  </tr>
                </table>
          </td>
        </tr>
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>
        [{ /if}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]><br>
            </td>
        </tr>
        </table>
    </td>
    <td valign="top" width="50%">
        [{ if $oxid != "-1"}]

        <input [{ $readonly }] type="button" value="[{ oxmultilang ident="GENERAL_ASSIGNCOUNTRIES" }]" class="edittext" onclick="JavaScript:showDialog('?cl=discount_main&aoc=1&oxid=[{ $oxid }]');">

        [{ /if}]
    </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
