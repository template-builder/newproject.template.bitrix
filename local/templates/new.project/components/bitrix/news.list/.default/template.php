<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

?>
<section class='<?= $arResult['SECTION_CLASS'] ?>'>
    <?if("Y" == $arParams['LAZY_LOAD'] && !empty($_GET['LAZY_LOAD'])) echo "<!--RestartBuffer-->"?>
    <?if( $arParams["DISPLAY_TOP_PAGER"] ):?>
    <div class="<?= $arParams['IBLOCK_CODE'] ?>_<?= $arParams['ITEM_CLASS'] ?>__pager <?= $arParams['IBLOCK_CODE'] ?>_<?= $arParams['ITEM_CLASS'] ?>__pager_top"><?= $arResult["NAV_STRING"] ?></div>
    <?endif;?>

    <div class="<?= $arParams['ROW_CLASS'] ?>">
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <div class="<?= $arItem['COLUMN_CLASS'] ?>" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                <article class="<?if('HORIZONTAL' == $arParams['ITEM_DIRECTION']) echo "media "?><?= $arParams['ITEM_CLASS'] ?>">

                <?if( "N" !== $arParams["DISPLAY_PICTURE"] && !empty($arItem["PREVIEW_PICTURE"]["SRC"]) ):?>
                    <div class="<?= $arParams['ITEM_CLASS'] ?>__pict">
                        <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= htmlspecialcharsEx($arItem["~NAME"]) ?>" />
                    </div>
                <?endif?>

                    <div class="media-body <?= $arParams['ITEM_CLASS'] ?>__body">

                    <?if( "Y" == $arParams["DISPLAY_NAME"] ):?>
                        <<?= $arParams["NAME_TAG"] ?> class="<?= $arParams['ITEM_CLASS'] ?>__name">
                            <?= $arItem["NAME"] ?>
                        </<?= $arParams["NAME_TAG"] ?>>
                    <?endif?>

                    <?if( "Y" === $arParams["DISPLAY_DATE"] && $arItem["DISPLAY_ACTIVE_FROM"]):?>
                        <div class="<?= $arParams['ITEM_CLASS'] ?>__date"><?= $arItem["DISPLAY_ACTIVE_FROM"] ?></div>
                    <?endif?>

                    <?if( "N" !== $arParams["DISPLAY_PREVIEW_TEXT"] && $arItem["PREVIEW_TEXT"]):?>
                        <div class="<?= $arParams['ITEM_CLASS'] ?>__desc">
                            <?= $arItem["PREVIEW_TEXT"] ?>
                        </div>
                    <?endif?>

                    <?if( !empty($arItem['DETAIL_PAGE_URL']) && !empty($arParams["MORE_LINK_TEXT"]) ):?>
                        <a class="<?= $arParams['ITEM_CLASS'] ?>__more" href="<?= $arItem['DETAIL_PAGE_URL'] ?>"><?= $arParams["MORE_LINK_TEXT"] ?></a>
                    <?endif?>

                    </div>

                </article>
            </div>

            <?// add edit areas
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
                CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
                CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array(
                    "CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM') ) );
            ?>
        <?endforeach?>
    </div><!-- .<?= $arParams['ROW_CLASS'] ?> -->

    <?/*if( $arParams["DISPLAY_BOTTOM_PAGER"] ):?>
    <div class="<?= $arParams['IBLOCK_CODE'] ?>_<?= $arParams['ITEM_CLASS'] ?>__pager <?= $arParams['IBLOCK_CODE'] ?>_<?= $arParams['ITEM_CLASS'] ?>__pager_bottom"><?=$arResult["NAV_STRING"];?></div>
    <?endif;*/?>

    <?if($arResult['MORE_ITEMS_LINK']):?>
    <div class="ajax-pager-wrap">
        <a class="more-items-link btn btn-red" href="<?= $arResult['MORE_ITEMS_LINK'] ?>">больше<br> статей</a>
    </div>
    <?endif?>

    <?if("Y" == $arParams['LAZY_LOAD'] && !empty($_GET['LAZY_LOAD'])) echo "<!--RestartBuffer-->"?>

    <?if("Y" == $arParams['LAZY_LOAD']):?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $section = $('.<?= implode('.', explode(' ', $arResult['SECTION_CLASS'])) ?>'),
                wrapperClass = '.<?= implode('.', explode(' ', $arParams['ROW_CLASS'])) ?>',
                $wrapper = $(wrapperClass, $section),

                ajaxPagerLoadingTpl     = ['<span class="ajax-pager-loading">',
                                               'Загрузка…',
                                           '</span>'].join(''),
                ajaxBusy = false;

            <?if("Y" == $arParams['INFINITY_SCROLL']):?>
            var $window = $(window);
            $window.on('scroll', function() {
                var wrapperOffsetBottom = $wrapper.offset().top + $wrapper.height();
                var windowOffsetBottom  = $window.scrollTop() + $window.height();

                if(windowOffsetBottom > wrapperOffsetBottom && !ajaxBusy) {
                    $('.more-items-link').trigger('click');
                }
            });
            <?endif?>

            $(document).on('click', '.more-items-link', function(event) {
                event.preventDefault();
                ajaxBusy = true;

                var $loadingLabel = $(ajaxPagerLoadingTpl);

                $(this).parent().append($loadingLabel);

                $.get($(this).attr('href'), {'LAZY_LOAD' : 'Y'}, function(newElements) {
                    var $new = $(newElements);
                    $wrapper = $(wrapperClass, $section);

                    $new.each(function(index, el) {
                        if( $(el).hasClass('row') ) {
                            $(el).prepend( $wrapper.html() );
                        }
                    });

                    $section.html($new);
                    // $wrapper.append(newElements);
                    // $loadingLabel.remove();

                    ajaxBusy = false;
                });
            });
        });
    </script>
    <?endif?>
</section>
