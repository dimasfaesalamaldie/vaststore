<?php

namespace backend\controllers;

use Yii;
use common\models\Barang;
use common\models\BarangSearch;
use common\models\SafetyStock;
use common\models\SafetyStockSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BarangController implements the CRUD actions for Barang model.
 */
class BarangController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Barang models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BarangSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Barang model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new SafetyStockSearch();
        $searchModel->sku_barang = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Barang model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Barang();
        $model->dibuat_oleh = Yii::$app->user->identity->username;
        $model->diubah_oleh = Yii::$app->user->identity->username;

        if ($model->load(Yii::$app->request->post())) {            
            $model->harga = str_replace(".", "", $model->harga);
            if($model->save()) {
                return $this->redirect(['view', 'id' => $model->sku]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Barang model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->diubah_oleh = Yii::$app->user->identity->username;

        if ($model->load(Yii::$app->request->post())) {            
            if(($sf = SafetyStock::find()->andWhere(['sku_barang' => $model->sku])->one()) !== null) {
                $sf->save();
            }    
            $model->harga = str_replace(".", "", $model->harga);
            if($model->save()) {
                return $this->redirect(['view', 'id' => $model->sku]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Barang model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Barang model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Barang the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Barang::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
