<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'My Yii Application';

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div class="site-index">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/boost.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>


    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'file')->fileInput(['autofocus' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'button']) ?>
    </div>

    <?php ActiveForm::end(); ?>


    <div id="container" style="height: 400px;  width: 100%; margin: 0 auto"></div>

    <script type="text/javascript">
        Highcharts.chart('container', {
            chart: {
                zoomType: 'x'
            },
            title: {
                text: '<?=$title?>'
            },
            tooltip: {
                valueDecimals: 2
            },
            xAxis: {
                type: 'datetime'
            },
            series: [{
                data: <?=json_encode($result)?>,
                name: 'Profit'
            }]
        });
    </script>
</div>
