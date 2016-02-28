<?php
/* @var $this SiteController */
/* @var $dataProvider CSqlDataProvider */

$this->pageTitle = Yii::app()->name . ' - Weather DATA ';
$this->breadcrumbs = array(
    'Weather DATA',
);
?>
<!--
<?php /*if(Yii::app()->user->hasFlash('success')):*/?>
    <div class="info">
        <?php /*echo Yii::app()->user->getFlash('success'); */?>
    </div>
--><?php /*endif; */?>

<h1>Weather DATA</h1>

<?php if (Yii::app()->user->hasFlash('success')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('weatherData'); ?>
    </div>
<?php endif ?>
<?php
if (isset($dataProvider)) {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'weatherData',
        'dataProvider' => $dataProvider,
        'columns' => array(
            'id',
            'description',
            'temp',
            'humidity',
            'lastUpdated',

        ),
    ));
}
?>

