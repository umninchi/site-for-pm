<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;
use common\models\GivenTask;
use yii\bootstrap\ButtonDropdown;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    
    NavBar::begin([
        'brandLabel' => 'ПМ',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse',
        ],
    ]);       
    $menuItems[] = ['label' => 'Главная', 'url' => ['/site/index']];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Регистрация', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Вход', 'url' => ['/site/login']];
    } else {
           $menuItems[] = ['label' => 'Новости', 'url' => ['/news/index']];
        //for student
        if((Yii::$app->user->can('student'))&&(Yii::$app->user->identity->isStudent)){
            $menuSubItems[] = ['label' => 'Кабинет студента','url' => Url::to(['/student/cabinet'])];
            $menuSubItems[] = ['label' => '','url' => '#','options' => ['class' => 'divider']];
            $menuSubItems[] = ['label' => 'Группа ('.Yii::$app->user->identity->student->group->name.')',
                'url' => Url::to(['/group/my'])];
            $menuSubItems[] = ['label' => 'Задания '.Html::tag('span',Yii::$app->user->identity->student->newTasksCount,['class' => 'badge']),'url' => Url::to(['/given-task/taken'])];  
            $menuSubItems[] = ['label' => 'Диплом','url' => Url::to(['/work/graduate'])];  
            $menuSubItems[] = ['label' => 'Курсовые работы','url' => Url::to(['/work/term'])];  
            $menuItems[] = ['label' => 'Студенту',
                'url' => Url::to(['/group/my']), 'items' => $menuSubItems];
            $menuSubItems = null;
        }
        //for teacher
        if((Yii::$app->user->can('teacher'))&&(Yii::$app->user->identity->isTeacher)){
            $menuSubItems[] = ['label' => 'Кабинет преподавателя','url' => Url::to(['/teacher/cabinet'])];
            $menuSubItems[] = ['label' => '','url' => '#','options' => ['class' => 'divider']];
            $menuSubItems[] = ['label' => 'Выдать задания '.Html::tag('span',Yii::$app->user->identity->teacher->newTasksCheckCount,['class' => 'badge'])
                , 'url' => Url::to(['/given-task/control'])]; 
            $menuSubItems[] = ['label' => 'Управление заданиями', 'url' => Url::to(['/task/control'])];
            $menuSubItems[] = ['label' => 'Управление упражнениями', 'url' => Url::to(['/exercise/control'])];
            $menuSubItems[] = ['label' => 'Дипломы', 'url' => Url::to(['/work/teacher-graduate'])];
            $menuSubItems[] = ['label' => 'Курсовые', 'url' => Url::to(['/work/teacher-term'])];
            $menuItems[] = ['label' => 'Преподавателю',
                'url' => Url::to(['group/my']), 'items' => $menuSubItems];
            $menuSubItems = null;
        }
        
        //for chief
        
        if((Yii::$app->user->can('chief'))){   
            $menuSubItems[] = ['label' => 'Кабинет заведующего','url' => Url::to(['/chief/cabinet'])];
            //$menuSubItems[] = ['label' => '','url' => '#','options' => ['class' => 'divider']];            
            $menuItems[] = ['label' => 'Заведующему',
                'items' => $menuSubItems];
            $menuSubItems = null;
        }
        
        if((Yii::$app->user->can('chief'))||(Yii::$app->user->can('manager'))){
            $menuSubItems[] = ['label' => 'Группы', 'url' => Url::to(['/group/manage'])];
            $menuSubItems[] = ['label' => 'Дисциплины', 'url' => Url::to(['/discipline/manage'])];
            $menuSubItems[] = ['label' => 'Предметы', 'url' => Url::to(['/group-has-discipline/manage'])];
            $menuSubItems[] = ['label' => 'Расписание', 'url' => Url::to(['/lesson/manage'])];
            $menuSubItems[] = ['label' => 'Специальности', 'url' => Url::to(['/speciality/manage'])];
            $menuSubItems[] = ['label' => 'Стандарты', 'url' => Url::to(['/standart/manage'])];
            $menuSubItems[] = ['label' => 'Студенты', 'url' => Url::to(['/student/manage'])];
            $menuSubItems[] = ['label' => 'Преподаватели', 'url' => Url::to(['/teacher/manage'])];
            $menuSubItems[] = ['label' => 'Новости', 'url' => Url::to(['/news/manage'])];
            $menuSubItems[] = ['label' => 'Пользователи', 'url' => Url::to(['/user/manage'])];
            $menuItems[] = ['label' => 'Управление',
                'url' => Url::to(['/group/my']), 'items' => $menuSubItems];
            $menuSubItems = null;
        }       
        
        //information
        $menuItems[] = ['label' => 'Информация', 'items' => [
            ['label' => 'Расписание','url' => Url::to(['/lesson/index'])],            
            ['label' => 'Пользователи','url' => Url::to(['/user/index'])],
            ['label' => 'Группы','url' => Url::to(['/group/index']), "visible" => ((Yii::$app->user->can('chief'))||(Yii::$app->user->can('admin'))||
                    (Yii::$app->user->can('manager'))||(Yii::$app->user->can('teacher'))||(Yii::$app->user->can('student')))]
        ]];
        
        $newMessages = Yii::$app->user->identity->newMessagesCount ? '<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>' : "";
        $menuItems[] = ['label' => "Личный кабинет {$newMessages}", 'url' => ['/site/cabinet']];
        $menuItems[] = [
            'label' => 'Выход (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post'],
        ];
    }
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
        'encodeLabels' => false,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>        
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy;  <?= date('Y') ?></p>

        <p class="pull-right"><?//= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
