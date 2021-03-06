<?php

namespace frontend\controllers;

use Yii;
use common\models\GivenTask;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;
use frontend\models\GivenTaskSearch;
use yii\filters\AccessControl;
/**
 * GivenTaskController implements the CRUD actions for GivenTask model.
 */
class GivenTaskController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),                
                'only' => ['index','control','give','check','view', 'create','update','delete','taken'],
                'rules' => [
                    [   
                        'actions' =>  ['index','control','give','check','view', 'create','update','delete'],
                        'allow' => true,
                        'roles' => ['teacher'],
                    ], 
                    [   
                        'actions' =>  ['taken'],
                        'allow' => true,
                        'roles' => ['student'],
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
     * Lists all GivenTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => GivenTask::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GivenTask model.
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
     * Creates a new GivenTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GivenTask();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionControl()
    {
        $searchModel = new GivenTaskSearch();        
        $query = GivenTask::find()->where(['task.teacher_id' => Yii::$app->user->identity->teacher->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->get(), $query);        
        return $this->render('control',['dataProvider' => $dataProvider, 
            'searchModel' => $searchModel]);
    }

    /**
     * Updates an existing GivenTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionGive()
    {
        $result = -1;
        if(Yii::$app->request->post()){
            $result = GivenTask::createGivenTask(Yii::$app->request->post());  
            if($result)
            {
                return $this->redirect(Yii::$app->request->referrer);
            }
        }               
        $model = new GivenTask();
        $model->status = 0;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          
        } else {
            if(isset(Yii::$app->request->post()['dropdown']))
                $drop = Yii::$app->request->post()['dropdown'];
            else $drop = 0;
            if(isset(Yii::$app->request->post()['exersices']))
            {               
               return $this->renderAjax('give', [
                'model' => $model,
            ]);
            }
            return $this->renderAjax('give', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionCheck($id)
    {   
        $model = GivenTask::findOne($id);
        //$givenTask = $model->givenTask;
        if ($model->load(Yii::$app->request->post()) && $model->save()/* && $givenTask->load(Yii::$app->request->post()) && $givenTask->save()*/) {
           // return $this->redirect(['view', 'id' => $givenTask->status]);            
        }
        
        //$exersices_all = CompleteExercise::find()->indexBy('id')->all();
        //Model::loadMultiple($model->exercises, Yii::$app->request->post()) && Model::validateMultiple($model->exercises);
        //$exersices_all = CompleteExercise::find()->indexBy('id')->all();
        $exercises = \common\models\GivenExercise::find()->where(['given_task_id' => $model->id])->indexBy('id')->all();
        if (Model::loadMultiple($exercises, Yii::$app->request->post()) && Model::validateMultiple($exercises)) {
            foreach ($exercises as $exersice) {               
                if($model->status == 3)
                    $exersice->remake = 0;
                $exersice->save(false);
            }            
            //return $this->redirect('given-list');
        }
        
        $model = $this->findModel($id);
        $exersices = $model;
        return $this->renderAjax('_check_form', ['model' => $model,'exercises' => $exercises]);
    }
    
    /**
     * @taken tasks
     */
    public function actionTaken()
    {        
        $searchModel = new GivenTaskSearch();          
        $query = GivenTask::find()->where(['student_id' => Yii::$app->user->identity->student->id])->orderBy('status');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $query);                                   
        return $this->render('taken_tasks_list', ['dataProvider' => $dataProvider,'searchModel' => $searchModel]);         
                      
    }
    
    /**
     * @taken view
     */
    public function actionTakenView($id = null){
            
            /*$takenTask = GivenTask::find($id)->one();             
            if(isset(Yii::$app->request->post()['close'])){
                return $this->render('taken_task', ['takenTask' => $takenTask, 'openform' => false]);                
            }
            elseif(isset(Yii::$app->request->post()['submit'])){
                $cEx = new \common\models\CompleteExercise();
                $cEx->text = Yii::$app->request->post()['textarea'];
                $cEx->save();
                return $this->render('taken_task', ['takenTask' => $takenTask, 'complete' => true]);
            } 
            else{
                return $this->render('taken_task', ['takenTask' => $takenTask, 'openform' => true]);
            }*/
            
            $takenTask = GivenTask::findOne($id);            
            if($takenTask->student_id != Yii::$app->user->identity->student->id)
              throw new ForbiddenHttpException('У вас нет доступа к этому заданию.');
            return $this->render('taken_task', ['takenTask' => $takenTask]);
            
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $result = false;
        if(Yii::$app->request->post()){
            $result = GivenTask::updateGivenTask(Yii::$app->request->post(),$model);
            $model = $this->findModel($id);
        }                
        /*
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else { */
            return $this->renderAjax('update', [
                'model' => $model,
                'result' => $result,
            ]);
        //}
    }

    /**
     * Deletes an existing GivenTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['control']);
    }

    /**
     * Finds the GivenTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GivenTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GivenTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
