<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "comments".
 *
 * @property integer $id
 * @property string $text
 * @property integer $user_id
 * @property string $class_name
 * @property integer $parent_id
 * @property integer $datetime
 * @property integer $item_id
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text', 'class_name', 'item_id'], 'required'],
            [['text'], 'string'],
            [['user_id', 'parent_id', 'datetime', 'item_id'], 'integer'],
            [['class_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'user_id' => 'User ID',
            'class_name' => 'Class Name',
            'parent_id' => 'Parent ID',
            'datetime' => 'Datetime',
            'item_id' => 'Item ID',
        ];
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(),['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $this->user_id = Yii::$app->user->id;
            $this->datetime = date('U');
            
            return true;
        }
        return false;
    } 
}
