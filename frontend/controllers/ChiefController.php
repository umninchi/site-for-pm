<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * CommentsController implements the CRUD actions for Comments model.
 */
class ChiefController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),                
                'only' => ['cabinet','approve-graduate'],
                'rules' => [
                    [   
                        'actions' =>  ['cabinet','approve-graduate'],
                        'allow' => true,
                        'roles' => ['chief'],
                    ],                  
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCabinet(){
        
        return $this->render('cabinet');
    }
    
    public function actionApproveGraduate($id, $status)
    {
        $model = \common\models\Work::findOne($id);
        if(Yii::$app->user->can('chief')&&($model->approve_status == 1)){
            $model->approve_status = $status;
            $model->save();
        }        
    }
}
