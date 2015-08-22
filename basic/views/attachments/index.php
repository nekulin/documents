<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchAttachments */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attachments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachments-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Attachments', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'document_id',
            'name',
            'size',
            'ext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
