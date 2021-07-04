<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 28.11.2017
 * Time: 2:50
 */
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html; ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>

<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
