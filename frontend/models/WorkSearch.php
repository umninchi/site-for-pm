<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Work;

/**
 * WorkSearch represents the model behind the search form about `common\models\Work`.
 */
class WorkSearch extends Work
{
    /**
     * @inheritdoc
     * 
     */
    public $studentFullname;
    public $groupName;    
    public $disciplineName;


    public function rules()
    {
        return [
            [['id', 'work_type_id', 'name', 'student_id', 'teacher_id', 'date', 'approve_status'], 'integer'],
            [['studentFullname','groupName','disciplineName'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Work::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'studentFullname' => [
                    'asc' => ['user.last_name' => SORT_ASC],
                    'desc' => ['user.last_name' => SORT_DESC],
                    'label' => 'studentFullname'
                ],
                'groupName' => [
                    'asc' => ['student.group.name' => SORT_ASC],
                    'desc' => ['student.group.name' => SORT_DESC],
                    'label' => 'groupName'
                ],
                'disciplineName' => [
                    'asc' => ['groupHasDiscipline.discipline.name' => SORT_ASC],
                    'desc' => ['groupHasDiscipline.discipline.name' => SORT_DESC],
                ]
            ]
        ]);
        
        $this->load($params);        

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'work_type_id' => $this->work_type_id,
            'name' => $this->name,
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'date' => $this->date,
            'approve_status' => $this->approve_status,
        ]);
        
        $query->joinWith('student')->joinWith(['student.user' => function ($q) {
                $q->where('user.first_name LIKE "%' . $this->studentFullname . '%" ' .
            'OR user.last_name LIKE "%' . $this->studentFullname . '%"'.
            'OR user.middle_name LIKE "%' . $this->studentFullname . '%"'
            ); 
       }]);
        
        $query->joinWith('student')->joinWith(['student.group' => function ($q) {
                $q->where('group.name LIKE "%' . $this->groupName . '%" ');
       }]);
        
       if($this->work_type_id == Work::TYPE_TERM){
            $query->joinWith('groupHasDiscipline')->joinWith(['groupHasDiscipline.discipline' => function ($q) {
                     $q->where('discipline.name LIKE "%' . $this->disciplineName . '%" ');
            }]);
       }
       
        return $dataProvider;
    }
}
