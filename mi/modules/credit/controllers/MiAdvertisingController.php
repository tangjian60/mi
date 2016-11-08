<?php

namespace mi\modules\credit\controllers;

use Yii;
use mi\modules\credit\models\MiAdvertising;
use mi\modules\credit\models\MiAdvertisingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
/**
 * MiAdvertisingController implements the CRUD actions for MiAdvertising model.
 */
class MiAdvertisingController extends Controller
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
     * Lists all MiAdvertising models.
     * @return mixed
     */
    public function actionIndex()
    {
    	
        $searchModel = new MiAdvertisingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MiAdvertising model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MiAdvertising model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MiAdvertising();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	
        	$model->picture = UploadedFile::getInstance($model, 'picture');
        	if($model->picture){
        		$model->picture->saveAs(Yii::getAlias('@webroot').'/uploads/' . $model->picture->name);
        	}
        	\Yii::$app->db->createCommand()->update('mi_advertising', [
        			'picture' => '/uploads/' . $model->picture->name], "id=$model->id")->execute();
        	
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MiAdvertising model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
        	
        	$array=Yii::$app->request->post()['MiAdvertising'];
        	$model->picture = UploadedFile::getInstance($model, 'picture');
        	$picture = $array['picture'];
        	if($model->picture){
        		$model->picture->saveAs(Yii::getAlias('@webroot').'/uploads/' . $model->picture->name);
        		$picture = '/uploads/' . $model->picture->name;
        	}
        	\Yii::$app->db->createCommand()->update('mi_advertising', [
        			'title' => $array['title'],'status' => $array['status'],'type' => $array['type'],'align' => $array['align'],'url' => $array['url'],'content' => $array['content'],'description' => $array['description'],'picture' => $picture], "id=$id")->execute();
        	
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MiAdvertising model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MiAdvertising model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return MiAdvertising the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MiAdvertising::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
