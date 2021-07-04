<?php

use yii\helpers\Url;

$root_cat = Yii::$app->session->get('root_category');
$page = Yii::$app->session->get('page');
$found_variants = Yii::$app->session->get('found_variants', 0);

$new_mes = 0;
if (!Yii::$app->user->isGuest) {
    $new_mes_user = \common\models\Chat::find()->where(['shop_id' => Yii::$app->getUser()->identity->id, 'type' => 'user', 'is_read' => 0])->count();
    $new_mes_shop = \common\models\Chat::find()->where(['user_id' => Yii::$app->getUser()->identity->id, 'type' => 'shop', 'type' => 'shop', 'direction' => '2', 'is_read' => 0])->count();
    $new_mes = $new_mes_user + $new_mes_shop;
    if($new_mes > 9) {
        $new_mes = '9+';
    }
}
$currency = Yii::$app->session->get('currency', 'uzs');
if(Yii::$app->request->get('currency', '') == 'uzs' || Yii::$app->request->get('currency', '') == 'usd') {
    $currency = Yii::$app->request->get('currency', Yii::$app->session->get('currency', 'uzs'));
    Yii::$app->session->set('currency', $currency);
    Yii::$app->response->redirect(Url::current(['currency' => '']));
}
?>
<ul class="navbar-nav mr-auto headerMenu">
    <?php for ($i = 0; $i < count($menu); $i++) { ?>
        <li class="nav-item dropdown">
            <?php if($menu[$i]->on_main == 1) { ?>
                <a class="nav-link " href="<?= ($menu[$i]->on_main == 1)? Url::to(['service/list', 'id' => $menu[$i]->url]) :Url::to(['category/index', 'id' => $menu[$i]->url]) ?>"
                   id="navbarDropdown<?=$menu[$i]->id?>" role="button"   >
                    <?= $menu[$i]->translate->name ?>
                </a>
            <?php } else { ?>
            <a class="nav-link dropdown-toggle" href="<?= ($menu[$i]->on_main == 1)? Url::to(['service/list', 'id' => $menu[$i]->url]) :Url::to(['category/index', 'id' => $menu[$i]->url]) ?>"
               id="navbarDropdown<?=$menu[$i]->id?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $menu[$i]->translate->name ?>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <?php if($menu[$i]->on_main == 0) { ?>
                    <a class="dropdown-item" href="<?=Url::to(['category/index', 'id' => $menu[$i]->url])?>"><?= Yii::t('frontend', 'All Announces') ?></a>
                    <?php if($menu[$i]->spec == 0) { ?>
                        <a class="dropdown-item" href="<?=Url::to(['category/index', 'id' => $menu[$i]->url, 'type' => 'sell'])?>"><?= Yii::t('frontend', 'Sell') ?></a>
                        <a class="dropdown-item" href="<?=Url::to(['category/index', 'id' => $menu[$i]->url, 'type' => 'buy'])?>"><?= Yii::t('frontend', 'Buy') ?></a>
                        <a class="dropdown-item" href="<?=Url::to(['category/index', 'id' => $menu[$i]->url, 'type' => 'arenda'])?>"><?= Yii::t('frontend', 'Arenda') ?></a>
                    <?php } ?>
                    <a class="dropdown-item" href="<?= Url::to(['shop/list', 'id' => $menu[$i]->url]) ?>"><?= Yii::t('frontend', 'Shops') ?></a>
                <?php } ?>
            </div>
        </li>
        <?php } ?>
    <?php } ?>
</ul>
<div class="headerForum">
    <a href="<?= Url::to(['announcement/create']) ?>" class="headerAdd">
        <img src="/gta/images/plus-icon.svg" alt="<?= Yii::t('frontend', 'Add announce') ?>">
        <span><?= Yii::t('frontend', 'Add announce') ?></span>
    </a>
</div>
<div class="headerRight">
    <div class="headerCab dropdown">
        <div class="headerCabImage">
            <?php if(!Yii::$app->user->isGuest && Yii::$app->getUser()->identity->avatar) { ?>
            <img src="<?=Yii::$app->getUser()->identity->avatar?>" alt="<?= Yii::t('frontend', 'Cabinet')  ?>" style="width: 53px; height: 53px;border-radius: 100%;">
            <?php } else { ?>
                <img src="/gta/images/cab.svg" alt="<?= Yii::t('frontend', 'Cabinet')  ?>">
            <?php } ?>
        </div>
        <a href="#" class="headerCabText dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <h4><?= Yii::t('frontend', 'Cabinet')  ?></h4>
            <img src="/gta/images/bottom.svg" alt="">
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php if (Yii::$app->user->isGuest) { ?>
            <a class="dropdown-item" href="<?= Url::to(['announcement/index']) ?>"><?= Yii::t('frontend', 'Sign In') ?></a>
            <a class="dropdown-item" href="<?= Url::to(['site/signup']) ?>"><?= Yii::t('frontend', 'Sign Up') ?></a>
            <?php } else { ?>
            <a class="dropdown-item" href="<?= Url::to(['announcement/index']) ?>"><?= Yii::t('frontend', 'Announce') ?></a>
            <a class="dropdown-item" href="<?= Url::to(['user/messages']) ?>"><?= Yii::t('frontend', 'Messages') ?> <?=($new_mes > 0)? '<span class="badge badge-danger">'.$new_mes.'</span>': ''?></a>
            <a class="dropdown-item" href="<?= Url::to(['user/index']) ?>"><?= Yii::t('frontend', 'Settings') ?></a>
            <a class="dropdown-item" href="<?= Url::to(['favorite/index']) ?>"><?= Yii::t('frontend', 'Favorites') ?></a>
            <a class="dropdown-item" href="<?= Url::to(['site/logout']) ?>"><?= Yii::t('frontend', 'Logout') ?></a>
            <?php } ?>
        </div>

    </div>
    <div class="headerSettings">
        <a href="#" class="headerSettingsText">
            <h4><?=$current->name?> / <?=Yii::t('frontend', $currency)?></h4>
            <img src="/gta/images/bottom.svg" alt="">
        </a>
        <div class="headerSettingsOptions">
            <select id="inputGroupSelect01" class="headerSettingsOptionsSelect js-inputGroupSelectNav">
                <option selected><?= Yii::t('frontend', 'Choose language') ?></option>
                <?php foreach ($langs as $lang) { ?>
                    <option value="<?= '/' . $lang->url . Yii::$app->getRequest()->getLanguageUrl() ?>"><?=$lang->name?></option>
                <?php } ?>
            </select>
            <select id="inputGroupSelect01" class="headerSettingsOptionsSelect js-inputGroupSelectNav">
                <option selected><?= Yii::t('frontend', 'Choose currency') ?></option>
                <option value="<?=Url::current(['currency' => 'uzs'])?>"><?=Yii::t('frontend', 'uzs')?></option>
                <option value="<?=Url::current(['currency' => 'usd'])?>"><?=Yii::t('frontend', 'usd')?></option>
            </select>
        </div>
    </div>
</div>
