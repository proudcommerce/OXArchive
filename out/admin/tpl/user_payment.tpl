[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="user_payment">
</form>


<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="user_payment">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxuserpayments__oxid]" value="[{ $oxpaymentid }]">
<input type="hidden" name="editval[oxuserpayments__oxuserid]" value="[{ $oxid }]">
[{include file="autosave.form.tpl"}]

<table cellspacing="0" cellpadding="0" border="0"  width="98%">

<tr>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
    [{ if $oxid != "-1"}]
        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="USER_PAYMENT_PAYMENT" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
                <select name="oxpaymentid" class="editinput" style="width:320px;" onChange="document.myedit.submit();" [{ $readonly}]>
					<option value="-1">[{ oxmultilang ident="USER_PAYMENT_NEWPAYMENT" }]</option>
                    [{foreach from=$edituser->oPayments item=payment}]
                    <option value="[{ $payment->oxuserpayments__oxid->value }]" [{ if $payment->selected}]SELECTED[{/if}]>[{ $payment->oxpayments__oxdesc->value }]</option>
                    [{/foreach}]
                </select>
            </td>

        </tr>
        </table>
    [{ /if}]
    </td>

    <td valign="top" class="edittext vr">

        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="edittext" width="70">
            [{ oxmultilang ident="USER_PAYMENT_PAYMENTTYPE" }]
            </td>
            <td class="edittext">
                <select name="editval[oxuserpayments__oxpaymentsid]" class="editinput" [{ $readonly}]>
                    [{foreach from=$paymenttypes item=payment}]
                    <option value="[{ $payment->oxpayments__oxid->value }]" [{ if $payment->selected}]SELECTED[{/if}]>[{ $payment->oxpayments__oxdesc->value }]</option>
                    [{/foreach}]
                </select>
            </td>
        </tr>
        <!--tr>
            <td class="edittext" width="70">
            [{ oxmultilang ident="USER_PAYMENT_VALUE" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="15" maxlength="[{$edit->oxuserpayments__oxvalue->fldmax_length}]" name="editval[oxuserpayments__oxvalue]" value="[{$edit->oxuserpayments__oxvalue->value }]">
            </td>
        </tr-->
        [{foreach from=$edit->aDynValues item=value}]
        <tr>
            <td class="edittext" width="70">
            [{ $value->name}]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="20" maxlength="64" name="dynvalue[[{$value->name}]]" value="[{ $value->value}]" [{ $readonly}]>
            </td>
        </tr>
        [{/foreach}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly}]>
            [{ if $oxpaymentid != "-1"}]
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_DELETE" }]" onClick="Javascript:document.myedit.fnc.value='delpayment'"" [{ $readonly}]>
            [{/if}]
            </td>
        </tr>
        </table>

    </td>

</tr>
</table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
