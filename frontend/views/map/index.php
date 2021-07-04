<?php

use common\models\Language;

$this->title = Yii::t('frontend', 'Radar maps');
?>
    <div id="map"></div>
    <script type="text/javascript">
        // Функция ymaps.ready() будет вызвана, когда
        // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
        ymaps.ready(init);
        function init(){

            <?php if($city && $city->lat && $city->lng) { ?>
            var myLatLng = [<?=$city->lat?>, <?=$city->lng?>];
            var zoom = 12;
            <?php } else { ?>
            var myLatLng = [41.4450174,64.8666701];
            var zoom = 6;
            <?php } ?>

            // Создание карты.
            var myMap = new ymaps.Map("map", {
                // Координаты центра карты.
                // Порядок по умолчанию: «широта, долгота».
                // Чтобы не определять координаты центра карты вручную,
                // воспользуйтесь инструментом Определение координат.
                center: myLatLng,
                // Уровень масштабирования. Допустимые значения:
                // от 0 (весь мир) до 19.
                zoom: zoom
            });

            <?php for($i = 0; $i < count($radars); $i++) {
            $image = '/uploads/site/speed-limit.png';
            if ($radars[$i]->type == 1) $image = '/uploads/site/stop.png';
            ?>


            var myPlacemark<?=$i?> = new ymaps.Placemark([<?=$radars[$i]->lat?>, <?=$radars[$i]->lng?>], {
                balloonContent: '<?=str_replace("'", '`', Yii::t('frontend', 'Radar type ' . $radars[$i]->type))?>'
            }, {
                iconLayout: 'default#imageWithContent',
                iconImageHref: '<?=$image?>',
                iconImageSize: [32, 32],
                iconImageOffset: [-16, -16],
                iconContentOffset: [0, 0],
            });

            myMap.geoObjects.add(myPlacemark<?=$i?>);

            <?php } ?>
        }
    </script>

<?php

$this->registerCss('
    #map {
        width: 100%;
        height: 400px;
    }
');