<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 14.10.2017
 * Time: 2:34
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = $question->question;
$ava = $question->user->avatar? $question->user->avatar: '/uploads/site/default_shop.png';

$this->registerJs(
    '$(\'.delete-theme\').on(\'click\', function () {
        var r = confirm("'.Yii::t('frontend', 'Confirm deleting').'");
        if (r == true) {
            var button = $(this);
            $.ajax({
                url: "/forum/delete-theme",
                data: {\'theme_id\': button.data(\'id\')}, //data: {}
                type: "post",
                success: function (t) {
                    t = JSON.parse(t);
                    if (t.error !== true) {
                           window.location.href = "'.Url::to(['forum/index']).'"
                    }
                }
            });
            return false;
        }
        return false;
    });
    $(\'.delete-answer\').on(\'click\', function () {
        var r = confirm("'.Yii::t('frontend', 'Confirm deleting').'");
        if (r == true) {
            var button = $(this);
            $.ajax({
                url: "/forum/delete-answer",
                data: {\'id\': button.data(\'id\')}, //data: {}
                type: "post",
                success: function (t) {
                    t = JSON.parse(t);
                    if (t.error !== true) {
                        button.parent().parent().parent().hide();
                        button.parent().parent().parent().remove();
                    }
                }
            });
            return false;
        }
        return false;
    });
    '
);
?>

<div class="forum-themes-list">
    <div class="forum-themes-item">
        <div class="row">
            <div class="col-md-1">#<?=$question->id?></div>
            <div class="col-md-7">
                    <span class="pull-right"><i class="fa fa-trash-o text-muted pointer delete-theme" style="z-index: 1000;" data-id="<?=$question->id?>"></i></span>
                <strong><?=$question->question?></strong>
                <div class="clearfix"></div>
            </div>
            <div class="col-md-3"><img src="<?=$ava?>" class="forum__user-image"> <?=$question->user->name?></div>
            <div class="col-md-1"><i class="fa fa-comment-o"></i> <?=count($question->answers)?></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span class="forum-time text-muted"><?=date('d.m.Y, H:i', $question->user->created_at)?></span>
            </div>
        </div>
    </div>
    <?php if(!empty($answers)) { ?>
        <div class="forum-answer-list">
    <?php for($i = 0; $i < count($answers); $i++) {
        $ava = $answers[$i]->user->avatar? $answers[$i]->user->avatar: '/uploads/site/default_shop.png';
        ?>
            <div class="forum-answer-item">
                <p class="user-name">
                        <span class="pull-right"><i class="fa fa-trash-o text-muted pointer delete-answer" style="z-index: 1000;" data-id="<?=$answers[$i]->id?>"></i></span>
                    <img src="<?=$ava?>" class="forum__user-image"> <?=$answers[$i]->user->name?> <span class="forum-time text-muted"><?=date('d.m.Y, H:i', $answers[$i]->created_at)?></span>
                <span class="clearfix"></span>
                </p>

                <?php if ($answers[$i]->is_moderated == 0) { ?>
                    <p class="text-danger user-answer"><?=$answers[$i]->answer?></p>
                <?php } else { ?>
                    <p class="user-answer"><?=$answers[$i]->answer?></p>
                <?php } ?>
            </div>
    <?php

        $answers[$i]->is_moderated = 1;
        $answers[$i]->save();
    } ?>
        </div>
    <?php } ?>

    <div class="text-center">
        <?php echo LinkPager::widget([
            'pagination' => $pages,
        ]); ?>
    </div>
    <div class="text-center">
        <a href="<?=Url::to(['forum/index'])?>" class="btn btn-danger">Назад</a>
    </div>
</div>