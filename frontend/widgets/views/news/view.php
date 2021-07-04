<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 20.09.2017
 * Time: 4:09
 */
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

if (!empty($news)) {
    ?>
    <div class="news">
        <div class="container">
            <div class="newsTop">
                <div class="newsTitle">
                    <h2><?=Yii::t('frontend', 'News')?></h2>
                </div>
                <div class="newsAll">
                    <a href="<?=Url::to(['news/list'])?>"><?=Yii::t('frontend', 'All news')?></a>
                </div>
            </div>
            <div class="newsList">
                <div class="row ">
                    <?php for ($i = 0; $i < count($news); $i++) { ?>
                        <div class="col-lg-3">
                            <a href="<?=Url::to(['news/index', 'id' => $news[$i]->url])?>" class="newsItem">
                                <div class="newsThumbnail img-thumbnail">
                                    <img src="<?= $news[$i]->translate->image ?>" alt="<?= $news[$i]->translate->name ?>" width="100%">
                                </div>
                                <div class="newsItemTitle">
                                    <h4><?= $news[$i]->translate->name ?></h4>
                                </div>
                                <div class="newsExcerpt">
                                    <p><?= $news[$i]->translate->short ?></p>
                                </div>
                                <div class="newsItemDate">
                                    <span><?= date('d.m.Y', $news[$i]->created_at) ?></span>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>