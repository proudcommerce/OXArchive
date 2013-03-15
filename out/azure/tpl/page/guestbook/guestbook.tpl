[{capture append="oxidBlock_content"}]
    <h1 class="pageHead">[{ oxmultilang ident="PAGE_GUESTBOOK_LIST_GUESTBOOK" }]</h1>
    <div class="listRefine clear bottomRound">
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() sort=true}]
    </div>
    <div class="reviews">
        [{include file="form/guestbook.tpl"}]
        <dl class="hreview">
            [{if $oView->getEntries()}]
                [{foreach from=$oView->getEntries() item=entry}]
                    <dt class="clear item">
                        <span>[{ $entry->oxuser__oxfname->value }] [{oxmultilang ident="PAGE_GUESTBOOK_LIST_WRITES"}] <span class="dtreviewed">[{$entry->oxgbentries__oxcreate->value|date_format:"%d.%m.%Y"}] [{ $entry->oxgbentries__oxcreate->value|date_format:"%H:%M" }]<span></span></span>
                    </dt>
                    <dd>
                        <div class="description">[{ $entry->oxgbentries__oxcontent->value|nl2br }]</div>
                    </dd>
                [{/foreach}]
            [{else}]
                <dt id="reviewName_[{$smarty.foreach.ReviewsCounter.iteration}]">
                    [{oxmultilang ident="PAGE_GUESTBOOK_LIST_NOENTRYAVAILABLE"}]
                </dt>
            [{/if}]
        </dl>
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
    </div>
[{/capture}]
[{include file="layout/page.tpl" sidebar="Right"}]
