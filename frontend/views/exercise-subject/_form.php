<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\ExerciseSubject */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Pjax::begin(['id' => 'modalContent','enablePushState' => false]); ?>

<div class="exercise-subject-form">
    
    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true]]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'teacher_id')->textInput() ?>

    <div class="form-group">
        <?= Html::button($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'type' => 'submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>

<?php Pjax::end(); ?>