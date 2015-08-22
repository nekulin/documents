<?php

use app\models\SearchAttachments;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Documents */

$searchModel = new SearchAttachments();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>
<h1>Файлы</h1>

<?php \yii\widgets\Pjax::begin(); ?>
<?= GridView::widget([
    'id' => 'attachment-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'name',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    $url = yii\helpers\Url::to(['/attachments/delete', 'id' => $model->id, 'hash' => $model->hash]);
                    $options = array_merge([
                        'title' => Yii::t('yii', 'Delete'),
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '1',
                    ], []);
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>

<div class="row">
    <?= \kato\DropZone::widget([
        'uploadUrl' => \yii\helpers\Url::to(['/documents/upload', 'id' => $model->id]),
        'options' => [
            'maxFilesize' => '2',
        ],
        'clientEvents' => [
            'queuecomplete' => "function(file){ $.pjax.reload({container:'#attachment-grid'}); }",
        ],
    ]); ?>
</div>