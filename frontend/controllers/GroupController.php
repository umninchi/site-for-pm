<?php

namespace frontend\controllers;

use Yii;
use common\models\Group;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\GroupSearch;
use common\models\GroupSemesters;
use yii\helpers\Url;
use common\models\GroupAnounces;
use yii\helpers\Json;
/**
 * GroupController implements the CRUD actions for Group model.
 */
class GroupController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),                
                'only' => ['index', 'view', 'my','create',
                    'update','delete','manage','preview','create-anounce'],
                'rules' => [
                    [
                        'actions' =>  ['view','index','create-anounce'],
                        'allow' => true,
                        'roles' => ['teacher','chief','manager'],
                    ],
                    [
                        'actions' =>  ['my','view','index','create-anounce'],
                        'allow' => true,
                        'roles' => ['student'],
                    ],
                    [
                        'actions' =>  ['create','update','delete','manage','preview'],
                        'allow' => true,
                        'roles' => ['chief','manager','admin'],
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
     * Lists all Group models.
     * @return mixed
     */
    public function actionIndex()
    {        
        $searchModel = new GroupSearch();        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all Group models.
     * @return mixed
     */
    public function actionManage()
    {
        $searchModel = new GroupSearch();        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('manage', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Group model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    public function actionCreateAnounce($id)
    {
        if(Yii::$app->request->isAjax) {
        $model = new \common\models\GroupAnounces();
        if($id != null) $model->group_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->renderAjax('create-anounce',[
                'model' => $model,
            ]);
        }
        }
        else {
             throw new NotFoundHttpException('Страница не существует.');
        }
    }
    
    public function actionUpdateAnounce($id)
    {
        if(Yii::$app->request->isAjax) {
        $model = \common\models\GroupAnounces::findOne($id);
        if($model->user_id !== Yii::$app->user->id)
            throw new NotFoundHttpException('Страница не существует.');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->renderAjax('create-anounce',[
                'model' => $model,
            ]);
        }
        }
        else {
             throw new NotFoundHttpException('Страница не существует.');
        }
    }

        /**
     * Displays a single Group model.
     * @param integer $id
     * @return mixed
     */
    public function actionPreview($id)
    {
        if(Yii::$app->request->isAjax) {
        return $this->renderAjax('preview', [
            'model' => $this->findModel($id),
        ]);
        }
    }
    
    /**
     * Displays a single Group model.
     * @param integer $id
     * @return mixed
     */
    public function actionMy()
    {
        if(Yii::$app->user->identity->isStudent)
        return $this->render('view', [
            'model' => $this->findModel(Yii::$app->user->identity->student->group_id),
        ]);
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionLists()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            $list = Group::find()->select('group.*')
                 ->leftJoin('group_has_discipline','group_has_discipline.group_id = group.id')
                 ->where(['group_has_discipline.id' => $id])
                 ->all();
            $selected  = null;
            if ($id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $group) {
                    $out[] = ['id' => $group['id'], 'name' => $group['name']];
                    if ($i == 0) {
                        $selected = $group['id'];
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
     * Creates a new Group model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
       // if(Yii::$app->request->isAjax) {
        $model = new Group();

        if ($model->load(Yii::$app->request->post()) && $model->save() ) {
            GroupSemesters::createSemestersForGroup($model->getPrimaryKey(), 
                    Yii::$app->request->post()['sem_count'],
                    Yii::$app->request->post()['begin_year']);
            return $this->redirect(['manage']);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
        //}
       // else {
       //      throw new NotFoundHttpException('Страница не существует.');
       // }
    }

    /**
     * Updates an existing Group model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelGS = GroupSemesters::find()->where(['group_id' => $id])->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
                'modelGS' => $modelGS,
            ]);
        }
    }
    /*
    
     * Deletes an existing Group model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['manage']);
    }
    
    public function actionDeleteAnounce($id)
    {        
        $model = GroupAnounces::findOne($id);
        if(Yii::$app->user->id === $model->user_id){
            $model->delete();           
        }
       $this->redirect(Yii::$app->request->referrer);
    }

    /*
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Group the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
