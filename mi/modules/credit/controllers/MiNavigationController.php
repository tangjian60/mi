<?php

namespace mi\modules\credit\controllers;

use Yii;
use mi\modules\credit\models\MiNavigation;
use mi\modules\credit\models\MiNavigationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
/**
 * MiNavigationController implements the CRUD actions for MiNavigation model.
 */
class MiNavigationController extends Controller
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
     * Lists all MiNavigation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MiNavigationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MiNavigation model.
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
     * Creates a new MiNavigation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MiNavigation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	$model->picture = UploadedFile::getInstance($model, 'picture');
        	if($model->picture){
        		$model->picture->saveAs(Yii::getAlias('@webroot').'/uploads/' . $model->picture->name);
        	}
        	\Yii::$app->db->createCommand()->update('mi_navigation', [
        			'picture' => '/uploads/' . $model->picture->name], "id=$model->id")->execute();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MiNavigation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
        	$array=Yii::$app->request->post()['MiNavigation'];
        	$model->picture = UploadedFile::getInstance($model, 'picture');
        	$picture = $array['picture'];
        	if($model->picture){
        		$model->picture->saveAs(Yii::getAlias('@webroot').'/uploads/' . $model->picture->name);
        		$picture = '/uploads/' . $model->picture->name;
        	}
        	\Yii::$app->db->createCommand()->update('mi_navigation', [
        			'title' => $array['title'],'status' => $array['status'],'url' => $array['url'],'picture' => $picture], "id=$id")->execute();
        	 
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MiNavigation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MiNavigation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MiNavigation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MiNavigation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
