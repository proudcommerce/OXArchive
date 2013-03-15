
[{assign var="_oProduct" value=$oView->getProduct()}]
[{assign var="editval" value=$oView->getInviteData()}]
<form class="oxValidate" action="[{ $oViewConf->getSslSelfLink() }]" method="post">
    <div class="suggestView">
        [{ $oViewConf->getHiddenSid() }]
        [{ $oViewConf->getNavFormParams() }]
        <input type="hidden" name="fnc" value="send">
        <input type="hidden" name="cl" value="invite">
        <input type="hidden" name="anid" value="[{ $_oProduct->oxarticles__oxnid->value }]">
        <input type="hidden" name="CustomError" value='invite'>
        [{assign var="oCaptcha" value=$oView->getCaptcha() }]
        <input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]">
        <h3 class="blockHead">[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_SENDTO" }]</h3>
        <ul class="form">
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_RECIPIENTEMAIL" }] #1:<span class="req">*</span></label>
                <input type="text" name="editval[rec_email][1]" size="73" maxlength="73" value="[{$editval->rec_email.1}]">
                <p class="oxValidateError">
                    <span class="oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                    <span class="oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_RECIPIENTEMAIL" }] #2:</label>
                <input type="text" name="editval[rec_email][2]" size="73" maxlength="73" value="[{$editval->rec_email.2}]">
                <p class="oxValidateError">
                    <span class="oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_RECIPIENTEMAIL" }] #3:</label>
                <input type="text" name="editval[rec_email][3]" size="73" maxlength="73" value="[{$editval->rec_email.3}]">
                <p class="oxValidateError">
                    <span class="oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_RECIPIENTEMAIL" }] #4:</label>
                <input type="text" name="editval[rec_email][4]" size="73" maxlength="73" value="[{$editval->rec_email.4}]">
                <p class="oxValidateError">
                    <span class="oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_RECIPIENTEMAIL" }] #5:</label>
                <input type="text" name="editval[rec_email][5]" size="73" maxlength="73" value="[{$editval->rec_email.5}]">
                <p class="oxValidateError">
                    <span class="oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
        </ul>


        <h3 class="blockHead">[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_FROM" }]</h3>
        <ul class="form">
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_SENDERNAME" }]<span class="req">*</span></label>
                <input type="text" name="editval[send_name]" size=73 maxlength=73 value="[{$editval->send_name}]">
                <p class="oxValidateError">
                    <span class="oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_SENDEREMAIL" }]<span class="req">*</span></label>
                <input type="text" name="editval[send_email]" size=73 maxlength=73 value="[{$editval->send_email}]" >
                <p class="oxValidateError">
                    <span class="oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                    <span class="oxError_email">[{ oxmultilang ident="EXCEPTION_INPUT_NOVALIDEMAIL" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_CAPTION" }]<span class="req">*</span></label>
                <input type="text" name="editval[send_subject]" size=73 maxlength=73 value="[{if $editval->send_subject}][{$editval->send_subject}][{else}][{ oxmultilang ident="FORM_SUGGEST_SUBJECT" }] [{ $_oProduct->oxarticles__oxtitle->value|strip_tags }][{/if}]">
                <p class="oxValidateError">
                    <span class="oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li>
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_YOURMESSAGE" }]<span class="req">*</span></label>
                <textarea cols="70" rows="8" name="editval[send_message]" class="areabox">[{if $editval->send_message}][{$editval->send_message}][{else}][{ oxmultilang ident="FORM_SUGGEST_MESSAGE1" }] [{ $oxcmp_shop->oxshops__oxname->value }] [{ oxmultilang ident="FORM_SUGGEST_MESSAGE2" }][{/if}]</textarea>
                <p class="oxValidateError">
                    <span class="oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li class="verify">
                <label>[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_VERIFICATIONCODE" }]<span class="req">*</span></label>
                [{assign var="oCaptcha" value=$oView->getCaptcha() }]
                [{if $oCaptcha->isImageVisible()}]
                    <img src="[{$oCaptcha->getImageUrl()}]" alt="">
                [{else}]
                    <span class="verificationCode" id="verifyTextCode">[{$oCaptcha->getText()}]</span>
                [{/if}]
                <input type="text" field="verify" name="c_mac" value="">
                <p class="oxValidateError">
                    <span class="oxError_notEmpty">[{ oxmultilang ident="EXCEPTION_INPUT_NOTALLFIELDS" }]</span>
                </p>
            </li>
            <li class="formSubmit">
                <button class="submitButton largeButton" type="submit">[{ oxmultilang ident="FORM_PRIVATESALES_INVITE_SEND" }]</button>
            </li>
        </ul>
    </div>
</form>