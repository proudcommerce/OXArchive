[{oxhasrights ident="TOBASKET"}]
[{if $oxcmp_basket->getContents()}]
[{assign var="currency" value=$oView->getActCurrency() }]
<dl class="box basket">
    <dt id="tm.basket.dt">
        <a id="test_TopBasketHeader" rel="nofollow" href="[{ oxgetseourl ident=$oViewConf->getBasketLink() }]">[{ oxmultilang ident="INC_HEADER_CART" }]</a>
    </dt>
    <dd id="tm.basket.dd" class="dropdown">
        [{strip}]
        <ul id="basket_menu" class="menue verticall">
            <li><a href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=basket" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_TOBASKET" }]</a></li>
            [{if $oxcmp_user->oxuser__oxpassword->value && $oView->isLowOrderPrice()}]
            <li><a href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=payment" }]" rel="nofollow">[{ oxmultilang ident="INC_ACCOUNT_HEADER_TOPAYMENT" }]</a></li>
            [{/if}]
        </ul>
        [{/strip}]
    </dd>
    <dd>
         <table summary="[{ oxmultilang ident="INC_HEADER_CART" }]">
          <tr>
             <th>[{ oxmultilang ident="INC_CMP_BASKET_PRODUCT" }]</th>
             <td id="test_TopBasketProducts">[{ $oxcmp_basket->getProductsCount()}]</td>
          </tr>
          <tr>
             <th>[{ oxmultilang ident="INC_CMP_BASKET_QUANTITY" }]</th>
             <td id="test_TopBasketItems">[{ $oxcmp_basket->getItemsCount()}]</td>
          </tr>
          [{if $oxcmp_basket->getFDeliveryCosts() }]
          <tr>
             <th>[{ oxmultilang ident="INC_CMP_BASKET_SHIPPING" }]</th>
             <td id="test_TopBasketShipping">[{ $oxcmp_basket->getFDeliveryCosts() }] [{ $currency->sign}]</td>
          </tr>
          [{/if}]
          <tr>
             <th>[{ oxmultilang ident="INC_CMP_BASKET_TOTALPRODUCTS" }]</th>
             <td id="test_TopBasketTotal">[{ $oxcmp_basket->getFProductsPrice()}] [{ $currency->sign}]</td>
          </tr>
         </table>
    </dd>
</dl>
[{oxscript add="oxid.topnav('tm.basket.dt','tm.basket.dd');" }]
[{/if}]
[{/oxhasrights}]