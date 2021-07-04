<?php

use frontend\widgets\WStaticPage;
use frontend\widgets\WSocials;
use frontend\widgets\WCategory;

?>
<footer class="footer">
    <div class="container">
        <div class="footerMain">
            <div class="row">
                <div class="col-xl-6">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="footerMainItem">

                                <?= WStaticPage::widget(['key' => 'info']); ?>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="footerMainItem">
                                <?= WCategory::widget(['key' => 'footer']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="footerMainAbout">
                        <p>
                            <?=Yii::t('frontend', 'Bottom text')?>
                        </p>
                        <h4><?=Yii::t('frontend', 'Bottom title')?></h4>
                        <div class="footerMainAboutSoc mb-3">
                            <?=Yii::t('frontend', 'App Store')?>
                            <?=Yii::t('frontend', 'Google Play')?>
                            <div class="clearfix"></div>
                        </div>

                        <div class="footer-top__block-soc d-block">
                            <?= WSocials::widget(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="footerBottom">
    <div class="container">
        <div class="footerFlex">
            <div class="footerBottomContent">
                <img src="/gta/images/footer-logo.png" alt="">
                <span>© <?=date('Y')?> <?=Yii::t('common', 'copy')?></span>
            </div>
            <div class="footerCopyright">
                <?= WStaticPage::widget(['key' => 'copy']); ?>
                <span>
                    <?=Yii::t('common', 'powered')?>
                </span>
            </div>
        </div>
    </div>
</div>