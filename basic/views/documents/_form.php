<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Documents */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="documents-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?php if ($model->isNewRecord) { ?>
        <div class="form-group">
            <?= \kato\DropZone::widget([
                'uploadUrl' => \yii\helpers\Url::to(['/documents/upload-temp', 'id' => $model->id]),
                'options' => [
                    'maxFilesize' => '2',
                ],
                'clientEvents' => [
                    'queuecomplete' => (!$model->isNewRecord) ? "function(file){ $.pjax.reload({container:'#attachment-grid'}); }" : 'function(file){}',
                ],
            ]); ?>
        </div>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
