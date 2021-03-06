<?php

namespace frontend\controllers;

use Yii;
use common\models\Student;
use yii\data\ActiveDataProvider;
use frontend\models\StudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;

/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),                
                'only' => ['index', 'view', 'create','update', 'manage','delete','lists','cabinet'],
                'rules' => [
                    [   
                        'actions' =>  ['manage','create','update','delete'],
                        'allow' => true,
                        'roles' => ['chief','manager','admin'],
                    ],
                    [
                        'actions' =>  ['index','cabinet'],
                        'allow' => true,
                        'roles' => ['student'],
                    ],
                    [
                        'actions' =>  ['lists'],
                        'allow' => true,
                        'roles' => ['teacher'],
                    ],
                    [
                        'actions' =>  ['view'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Student models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentSearch();
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
       ]);
    }
    
    /**
     * Lists all Student models.
     * @return mixed
     */
    public function actionManage()
    {
        $searchModel = new StudentSearch();
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('manage', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
       ]);
    }
    
    /**
     * Displays a single Student model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * Displays a single Student model.
     * @param integer $id
     * @return mixed
     */
    public function actionCabinet()
    {
        return $this->render('cabinet');
    }

    /**
     * Creates a new Student model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Student();
      
        if ($model->load(Yii::$app->request->post()) && $model->save()) {  
            
            return $this->redirect(['manage']);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Student model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionLists()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            $list = Student::find()
                 ->where(['group_id' => $id])
                 ->all();
            $selected  = null;
            if ($id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $student) {
                    $out[] = ['id' => $student->id, 'name' => $student->user->fullname];
                    if ($i == 0) {
                        $selected = $student->id;
                    }
                }
                // Shows how you can preselect a value
                echo Json::encode(['output' => $out, 'selected'=>$selected]);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected'=>'']);                          
    }

    /**
     * Deletes an existing Student model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['manage']);
    }

    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
