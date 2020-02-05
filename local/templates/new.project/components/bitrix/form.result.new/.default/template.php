<? if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;

$isAjax = function() use ( $arParams ) {
    /** @var Bitrix\Main\Server */
    $server = Context::getCurrent()->getServer();

    if( 'xmlhttprequest' === strtolower( $server->get( 'HTTP_X_REQUESTED_WITH' ) ) ) return true;
    if( isset($arParams['IS_AJAX']) && 'Y' == $arParams['IS_AJAX'] ) return true;

    return false;
};

$message = '';

if ("Y" == $arResult["isFormErrors"]) {
    $message = '<div class="fail-msg"><p class="text-danger">' . $arResult["FORM_ERRORS_TEXT"] . '</p></div>';
}
elseif ("Y" == $arResult["isFormNote"]) {
    $message = '<div class="note-msg">' . $arResult["FORM_NOTE"] . '</div>';
}
elseif( !empty($_REQUEST['formresult']) && 'addok' === $_REQUEST['formresult'] ) {
    $message = '<div class="success-msg"><p class="text-success">' . $arParams['SUCCESS_MESSAGE'] . '</p></div>';
}

echo $message;
if( $message && $isAjax() ) return;

?>
<? if ($arResult["isFormImage"] == "Y"): ?>
    <a href="<?= $arResult["FORM_IMAGE"]["URL"] ?>" target="_blank" alt="<?= GetMessage("FORM_ENLARGE") ?>"><img
                src="<?= $arResult["FORM_IMAGE"]["URL"] ?>"
                <? if ($arResult["FORM_IMAGE"]["WIDTH"] > 300): ?>width="300"
                <? elseif ($arResult["FORM_IMAGE"]["HEIGHT"] > 200): ?>height="200"<? else: ?><?= $arResult["FORM_IMAGE"]["ATTR"] ?><? endif; ?>
                hspace="3" vscape="3" border="0"/></a>
<? endif; ?>

<? if ($arResult["isFormTitle"]): ?><h4><?= $arResult["FORM_TITLE"] ?></h4><? endif; ?>
<?
if ('TOP' == $arParams['SHOW_DESCRIPTION']) {
    echo '<div class="description">' . $arResult["FORM_DESCRIPTION"] . '</div>';
}

echo $arResult["FORM_HEADER"];
foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
    if( 'hidden' == $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] ) {
        echo $arQuestion["HTML_CODE"];
        continue;
    }

    if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])) {
        printf('<span class="error-fld" title="%s"></span>', htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID]));
    }

    echo '<div class="form-group">';

    switch ($arQuestion['STRUCTURE'][0]['FIELD_TYPE']) {
        case 'checkbox':
            echo $arQuestion["HTML_CODE"], $arQuestion['LABEL'];
            break;

        default:
            echo $arQuestion['LABEL'], $arQuestion["HTML_CODE"];
            break;
    }

    echo '</div>';
}

if ($arResult["isUseCaptcha"] == "Y") {
    echo GetMessage("FORM_CAPTCHA_TABLE_TITLE");
    ?>
    <?= GetMessage("FORM_CAPTCHA_FIELD_TITLE") ?><?= $arResult["REQUIRED_SIGN"]; ?>
    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>" width="180"
         height="40"/>
    <input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext"/>

    <input type="hidden" name="captcha_sid" value="<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>"/>
    <?
}

if ('BEFORE_SUBMIT' == $arParams['SHOW_DESCRIPTION']) {
    echo '<div class="description">' . $arResult["FORM_DESCRIPTION"] . '</div>';
}
?>
    <input <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?> type="submit"
                                                                                      name="web_form_submit"
                                                                                      value="<?= htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]); ?>"
                                                                                      class="btn btn-primary"/>
<?
if ('BOTTOM' == $arParams['SHOW_DESCRIPTION']) {
    echo '<div class="description">' . $arResult["FORM_DESCRIPTION"] . '</div>';
}
?>
<?= $arResult["FORM_FOOTER"]; ?>