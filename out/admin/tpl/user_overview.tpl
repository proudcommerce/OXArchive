[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}] 

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="user_overview">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="99%" height="100%">
<tr>
<td valign="top" background="[{$shop->imagedir}]/edit_back.gif" width="100%">

&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_REVIEW" }]<br>
<br>
&nbsp;&nbsp;&nbsp;[{$edit->oxuser__oxsal->value }] [{$edit->oxuser__oxfname->value }] [{$edit->oxuser__oxlname->value }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_FON" }] : [{$edit->oxuser__oxfon->value }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_EMAIL" }] : <a href="mailto:[{$edit->oxuser__oxusername->value}]">[{$edit->oxuser__oxusername->value}]</a><br>
<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="USER_OVERVIEW_GROUPS" }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_RETURN" }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="USER_OVERVIEW_LASTITEM" }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="USER_OVERVIEW_LASTBUY" }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="USER_OVERVIEW_BONI" }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="USER_OVERVIEW_OXID" }] : [{ $oxid }]<br>
&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="USER_OVERVIEW_BREACKORDER" }]<br>


</td>
</tr>
[{include file="bottomnaviitem.tpl"}] 
</table>
[{include file="bottomitem.tpl"}]         
