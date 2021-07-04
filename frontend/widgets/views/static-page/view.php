<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 19.09.2017
 * Time: 6:19
 */

use yii\helpers\Url;
use common\models\Language;

?>
<?php if (!empty($static_page_cats)) { ?>
    <ul >
        <?php for ($i = 0; $i < count($static_page_cats); $i++) { ?>
            <?php if (empty($static_page_cats[$i]->activeStaticPages)) continue; ?>
            <?php for ($p = 0; $p < count($static_page_cats[$i]->activeStaticPages); $p++) { ?>
                <li >
                    <a href="<?= ($static_page_cats[$i]->activeStaticPages[$p]->external) ? $static_page_cats[$i]->activeStaticPages[$p]->url : Url::to(['site/page', 'id' => $static_page_cats[$i]->activeStaticPages[$p]->url]) ?>"  <?= ($static_page_cats[$i]->activeStaticPages[$p]->external) ? 'target="_blank"' : '' ?>>
                        <?= $static_page_cats[$i]->activeStaticPages[$p]->translate->name ?>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>

        <li >
            <a href="/files/pdd_<?=(Language::getCurrent()->url == 'oz' || Language::getCurrent()->url == 'uz')? 'uz': 'ru'?>.pdf" target="_blank" >
                <?=Yii::t('frontend', 'PDD')?>
            </a>
        </li>
        <li >
            <a href="<?=Url::to(['wiki/list'])?>" >
                <?=Yii::t('frontend', 'Wiki')?>
            </a>
        </li>
<!--        <li class="footer-top__item">-->
<!--            <a href="#" class="footer-top__link">-->
<!--                Оплата парковой-->
<!--            </a>-->
<!--        </li>-->
        <li >
            <a href="<?=Url::to(['map/radar'])?>" >
                <?=Yii::t('frontend', 'Radar maps')?>
            </a>
        </li>
        <li >
            <a href="<?=Yii::$app->params['partner_domain']?>" >
                <?=Yii::t('frontend', 'Partners panel')?>
            </a>
        </li>
    </ul>
<?php } ?>
