[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function EditThis( sID)
{
    var oTransfer = document.getElementById("transfer");
    oTransfer.oxid.value=sID;
    oTransfer.cl.value='article_main';
    oTransfer.submit();

    var oSearch = parent.list.document.getElementById("search");
    oSearch.actedit.value = 0;
    oSearch.oxid.value=sID;
    oSearch.submit();
}

//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{ if $message}]<div class="messagebox">[{ $message }]</div>[{/if}]

<p>
[{ oxmultilang ident="EFIRE_GETCONNECTOR" }]
</p>

<p>
[{ oxmultilang ident="EFIRE_CONNECTORINSTRUCTION" }]
</p>


<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
  <input type=hidden name=cl value=efire_downloader>
  <input type=hidden name=fnc value=getConnector>
<p>
[{ oxmultilang ident="EFIRE_USERNAME" }]:<br>
<input type="text" name=etUsername value="[{$sEfiUsername}]">

<p>
[{ oxmultilang ident="EFIRE_PASSWORD" }]:<br>
<input type="password" name=etPassword value="[{$sEfiPassword}]">

<p>

<input type="hidden" name="blSaveCredentials" value="0">
<input type="checkbox" name="blSaveCredentials" value="1" checked>
[{ oxmultilang ident="EFIRE_SAVECREDENTIALS" }]

<p>
<input type=Submit>

</form>


[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
