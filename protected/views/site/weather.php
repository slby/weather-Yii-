<?php
/* @var $this SiteController */
/* @var $model WeatherForm */

/* @var $dataProvider CSqlDataProvider */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Weather ';
$this->breadcrumbs = array(
    'Weather',
);
?>



<?php if (Yii::app()->user->hasFlash('weather')): ?>

    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('weather'); ?>
    </div>

<?php else: ?>


    <h1>Weather</h1>
    <p>
        With this FORM REQUEST you will get current weather in gridView
    </p>

    <div class="form">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'weather-form',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        )); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>



        <?php if (CCaptcha::checkRequirements()): ?>
            <div class="row">
                <?php echo $form->labelEx($model, 'verifyCode'); ?>
                <div>
                    <?php $this->widget('CCaptcha'); ?>
                    <?php echo $form->textField($model, 'verifyCode'); ?>
                </div>
                <div class="hint">Please enter the letters as they are shown in the image above.
                    <br/>Letters are not case-sensitive.
                </div>
                <?php echo $form->error($model, 'verifyCode'); ?>
            </div>
        <?php endif; ?>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Submit'); ?>
        </div>

        <?php $this->endWidget(); ?>

    </div><!-- form -->

    <div>
        You can also get weather with <a href="#" id="AjaxLink">Ajax(JS) Request</a>;<br>
        <table id="weatherSimple">
            <thead></thead>
            <tbody></tbody>
        </table>

        <?php
        $javascript = <<< EOT
<script type="text/javascript">
$('#AjaxLink').bind('click', function(){

       var table = $('#weatherSimple');
       var thead = table.find('thead');
       var tbody = table.find('tbody');
       thead.html('');
       tbody.html('');
       thead.append('<tr></tr>');
       tbody.append('<tr></tr>');

        $.post('index.php?r=site/weather',
            {
                ajax: 'weather-form'
            },
        function(response){

        if(response.data) {
            $.each(response.data, function(index, value){
                thead.find('tr').append('<th>'+index+'</th>');
                tbody.find('tr').append('<td>'+value+'</td>');
            });
            }
        }, 'json'
        );

});


</script>
EOT;

        echo $javascript;
        ?>

    </div>

<?php endif; ?>
<!--
<?php
/*
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
*/?>

-->
