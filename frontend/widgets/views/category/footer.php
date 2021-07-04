<?php

use yii\helpers\Url;

if (!empty($menu)) { ?>
<ul >
    <?php for($i = 0; $i < count($menu); $i++) {
        if($menu[$i]->on_main == 1) {
            $url = Url::to(['service/list', 'id' => $menu[$i]->url]);
        } else {
            $url = Url::to(['category/index', 'id' => $menu[$i]->url]);
        }
        ?>
        <li><a href="<?=$url?>"><?=$menu[$i]->translate->name?></a></li>
    <?php } ?>
</ul>
<?php } ?>