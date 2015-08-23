<?php

namespace app\controllers;

use app\models\Attachments;
use Yii;
use app\models\Documents;
use app\models\SearchDocuments;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DocumentsController implements the CRUD actions for Documents model.
 */
class DocumentsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Documents models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchDocuments();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Documents model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Documents model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Documents();
        $transaction = $model->getDb()->beginTransaction();
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                // сохраним файлы
                $session = Yii::$app->session;
                $files_temp = $session->get('files_temp', []);
                $upload_path_temp = \Yii::getAlias('@documents_dir_files_temp');
                $upload_path = \Yii::getAlias('@document_files_dir');
                foreach ($files_temp as $file) {
                    $file_path = $upload_path_temp . '/' . md5($file['hash']) . '.' . $file['ext'];
                    if (!file_exists($file_path)) continue;
                    $attachment = new Attachments();
                    $attachment->document_id = $model->id;
                    $attachment->name = $file['name'];
                    $attachment->hash = $file['hash'];
                    $attachment->size = $file['size'];
                    $attachment->ext = $file['ext'];
                    $attachment->save();
                    copy(
                        $upload_path_temp . '/' . md5($file['hash']) . '.' . $file['ext'],
                        $upload_path . '/' . md5($file['hash']) . '.' . $file['ext']
                    );
                    unlink($upload_path_temp . '/' . md5($file['hash']) . '.' . $file['ext']);
                }
                $session->remove('files_temp');
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            $transaction->rollBack();
        }
    }

    /**
     * Updates an existing Documents model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Documents model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $transaction = $model->getDb()->beginTransaction();
        if ($model->delete()) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return $this->redirect(['index']);
    }

    /**
     * Загрузка файлов для документов
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionUpload($id)
    {
        $model = $this->findModel($id);
        $fileName = 'file';
        $uploadPath = \Yii::getAlias('@document_files_dir');
        if (isset($_FILES[$fileName])) {
            $file = \yii\web\UploadedFile::getInstanceByName($fileName);
            $hash = md5_file($file->tempName);
            if ($file->saveAs($uploadPath . '/' . md5($hash) . '.' . $file->extension)) {
                $attachment = new Attachments();
                $attachment->document_id = $model->id;
                $attachment->name = $file->baseName;
                $attachment->hash = $hash;
                $attachment->size = $file->size;
                $attachment->ext = $file->extension;
                if (!$attachment->save()) {
                    Yii::$app->response->statusCode = 400;
                    echo array_values($attachment->getFirstErrors())[0];
                }
            }
        }
        return false;
    }

    /**
     * Временная загрузка файлов для создаваемого документа
     * @return bool
     */
    public function actionUploadTemp()
    {
        $fileName = 'file';
        $uploadPath = \Yii::getAlias('@documents_dir_files_temp');
        if (isset($_FILES[$fileName])) {
            $file = \yii\web\UploadedFile::getInstanceByName($fileName);
            $hash = md5_file($file->tempName);
            if ($file->saveAs($uploadPath . '/' . md5($hash) . '.' . $file->extension)) {
                $session = Yii::$app->session;
                $files_temp = $session->get('files_temp', []);
                $files_temp[] = [
                    'name' => $file->baseName,
                    'hash' => $hash,
                    'md5' => md5($hash),
                    'size' => $file->size,
                    'ext' => $file->extension,
                ];
                $session->set('files_temp', $files_temp);
            }
        }
        return false;
    }

    /**
     * Finds the Documents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Documents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Documents::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
