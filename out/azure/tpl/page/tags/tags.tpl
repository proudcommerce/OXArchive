[{capture append="oxidBlock_content"}]
    [{if $oView->getTagCloudManager() }]
        <h1 class="pageHead" id="tags">[{ oxmultilang ident="TAGS"}]</h1>
        <div >
            <p class="tags" id="tagsCloud">
                [{assign var="oCloudManager" value=$oView->getTagCloudManager() }]
                [{foreach from=$oCloudManager->getCloudArray() item=iCount key=sTagTitle}]
                    <a class="tagitem_[{$oCloudManager->getTagSize($sTagTitle)}]" href="[{$oCloudManager->getTagLink($sTagTitle)}]">[{$oCloudManager->getTagTitle($sTagTitle)}]</a>
                [{/foreach}]
            </p>
        </div>
    [{/if}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]