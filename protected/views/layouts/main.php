<?
/* @var $this Controller */
/* @var $content String */
?>
<!DOCTYPE html>
<html>
<head>

    <meta charset='utf-8'>
    <title>Школа виртуального пилотирования LuftwaffeSchule</title>
    <script type="text/javascript">
        var UserLoginData = <?=json_encode(Yii::app()->user->privateAttributes)?>;
        var isIL2 = true;
    </script>
    <script src="/scripts.js"></script>
    <script src="/socket.io/socket.io.js"></script>
    <link rel="stylesheet" href="/style.css" type="text/css">
    <!--[if lt IE 8]>
    <script type="text/javascript">
        alert('Ваш броузер не поддерживается, пожалуйста обновите! :)');
    </script>
    <![endif]-->
</head>
<body ng-app="app">
<div id="main_content">
    <div class="main_wrapper body-hidden" ng-controller="AppCtrl" ng-class="{'body-visible' : true}">
        <div class="cover_screen" ng-if="game.loading"><div class="spinner-icon"></div></div>
        <div class="notification_bank">
            <div ng-repeat="notification in notifications" class="alert alert-{{notification.type}} alert-notification shadowed" ng-click="closeNotification($index)">
                <div bind-compiled-html="notification.text"></div>
            </div>
        </div>
        <div class="photostack" id="main_header">
            <img ng-repeat="image in headerImages" class="header-image" ng-src="{{image}}">
        </div>
        <!--<div class="site_switch" ng-if="game.selection">
            <select ui-select2="gameSelect2Options"
                    style="width: 210px;"
                    class="form-control"
                    ng-model="game.selection">
                <option value="wt">War Thunder</option>
                <option value="bos">Il2: Battle of stalingrad</option>
            </select>
        </div>-->
        <div class="whiteline"></div>
        <a ui-sref="news" id="logo"></a>
        <table class="contentTable" cellpadding=0 cellspacing=0>
            <tbody>
            <tr>
                <td class="mm left_panel">
                    <div class="left_panel_2">
                    <div class="left_panel_3">
                    <div  ng-if="UserIdentity.id == 1 || UserIdentity.id == 14" ng-controller="InactiveCountCtrl">
                        <div ng-if="count > 0">
                            <div class="panel_menu">
                                <a href="">Дезертиры</a>
                            </div>
                            <div class="left_content">
                                <a ui-sref="inactiveUsers" class="list-group-item list-group-item-danger"><b>{{count}}</b> Дезертиров</a>
                            </div>
                        </div>
                    </div>
                    <div ng-controller="BirthdayViewCtrl" style="padding:0">
                        <div ng-if="birthdays.length">
                            <div class="panel_menu">
                                <a href="">Дни рождения</a>
                            </div>
                            <div class="left_content">
                                <a ui-sref="user({userId:pilot.id})"
                                   ng-repeat="pilot in birthdays  | orderBy:'birthday'"
                                   class="list-group-item" ng-class="{'list-group-item-danger' : pilot.today, 'list-group-item-info' : pilot.tomorow}">{{pilot.nickname}} ({{pilot.birthday | date : "dd.MM.yyyy"}})</a>
                            </div>
                        </div>
                    </div>
                    <div ng-if="UserIdentity.isInstructor" ng-controller="RosterViewCtrl" style="padding:0">
                        <div ng-if="roster.length">
                            <div class="panel_menu">
                                <a href="">Новобранцы</a>
                            </div>
                            <div class="left_content">
                                <a ui-sref="rosterUser({userId:pilot.id})" ng-repeat="pilot in roster"
                                   class="list-group-item">{{pilot.nickname}} ({{pilot.firstname}})</a>
                            </div>
                        </div>
                    </div>
                    <div class="panel_menu"><a href="ts3server://luftwaffeschule.ru/?nickname={{UserIdentity.canMakeOrders ? '=LwS=' : ''}}{{UserIdentity.fullname}}"><span class="ts_icon">Подключиться</span></a>
                    </div>
                    <div class="left_content ts_channels">
                        <ul ng-controller="TSViewCtrl" style="padding:15px">
                            <li ng-show="tree.length" ng-repeat="channel in tree" ng-include="'TreeItemTmpl'">
                            </li>
                            <li ng-show="!tree.length && !tree.empty">
                                Помойму что-то сломалось :(
                            </li>
                            <li ng-show="tree.empty">
                                Никого нету :)
                            </li>
                        </ul>
                    </div>
                </td>
                <td class="mm center_panel">
                    <div class="main_menu_cover">
                    <div class="main_menu">
                        <a ui-sref="news" ng-class="{'update':sectionIsUpdated(['news'])}">Сводки<i></i></a>
                        <a ui-sref="texts({id:5})" ng-class="{'update':sectionIsUpdated(['text_5'])}">История<i></i></a>
                        <a ui-sref="orders({page:1})" ng-if="!UserIdentity.canMakeOrders && !UserIdentity.isGuest" >Приказы<i></i></a>
                        <span class="dropdown dropdown-hover" ng-if="UserIdentity.canMakeOrders">
                        <a href="">Приказы<i></i></a>
                        <ul class="dropdown-menu" style="top: 45px;left: -7px;">
                            <li><a ui-sref="orders({page:1})">Архив</a></li>
                            <li class="divider"></li>
                            <li><a ui-sref="makeorder">Отдать приказ</a></li>
                            <li><a ui-sref="makenews">Добавить новость</a></li>
                            <li ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)" class="divider"></li>
                            <li ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)"><a ui-sref="awards">Награды</a></li>
                        </ul>
                        </span>
                        <span class="dropdown dropdown-hover">
                        <a href="" ng-class="{'update':sectionIsUpdated(['text_1','text_2']) || (UserIdentity.isInstructor && sectionIsUpdated(['instructor_training']))}">Документы<i></i></a>
                        <ul class="dropdown-menu" style="top: 45px;left: -7px;">
                            <li><a ui-sref="texts({id:1})"  ng-class="{'update':sectionIsUpdated(['text_1'])}">Устав</a></li>
                            <li><a ui-sref="texts({id:2})" ng-class="{'update':sectionIsUpdated(['text_2'])}">Приложения к уставу</a></li>
                            <li role="presentation" ng-if="UserIdentity.isInstructor" class="divider"></li>
                            <li><a ui-sref="materials({slug:'instructor_training'})" ng-class="{'update':sectionIsUpdated(['instructor_training'])}" ng-if="UserIdentity.isInstructor">Наставление инструктору</a></li>
                        </ul>
                        </span>
                        <a ui-sref="roster" ng-if="UserIdentity.isGuest">Вступить<i></i></a>
                        <span class="dropdown dropdown-hover" ng-if="!UserIdentity.isGuest">
                        <a href="" ng-class="{'update':sectionIsUpdated(['flight_basics','fighter_course','bomber_basics','sturm_course','bomber_course','war_basics'])}">Класс<i></i></a>
                        <ul class="dropdown-menu" style="top: 45px;left: -7px;">
                            <li><a>Расписание занятий</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ui-sref="materials({slug:'flight_basics'})" ng-class="{'update':sectionIsUpdated(['flight_basics'])}">Наставление по лётной подготовке истребителей</a></li>
                            <li><a ui-sref="materials({slug:'fighter_course'})" ng-class="{'update':sectionIsUpdated(['fighter_course'])}">Программа обучения истребителей</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ui-sref="materials({slug:'bomber_basics'})" ng-class="{'update':sectionIsUpdated(['bomber_basics'])}">Наставление по лётной подготовке бомбардировщиков</a></li>
                            <li><a ui-sref="materials({slug:'sturm_course'})" ng-class="{'update':sectionIsUpdated(['sturm_course'])}">Программа обучения штурмовиков</a></li>
                            <li><a ui-sref="materials({slug:'bomber_course'})" ng-class="{'update':sectionIsUpdated(['bomber_course'])}">Программа обучения бомбардировщиков</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ui-sref="materials({slug:'war_basics'})" ng-class="{'update':sectionIsUpdated(['war_basics'])}">Боевой устав</a></li>
                            <!--<li role="presentation" class="divider"></li>
                            <li><a ui-sref="texts({id:'6'})">Техническая эксплуатационная часть</a></li>-->
                        </ul>
                        </span>
                        <a ui-sref="pilots">Казарма<i></i></a>
                        <a ui-sref="flood" ng-if="!UserIdentity.isGuest" ng-class="{'update':sectionCategoryIsUpdated(['topic_'])}">Курилка<i></i></a>

                        <span class="dropdown dropdown-hover" ng-if="!UserIdentity.isGuest">
                            <a href="" ng-class="{'update':sectionIsUpdated(['tech_doc','tech_hardware'])}">ТЭЧ<i></i></a>
                                <ul class="dropdown-menu" style="top: 45px;left: -7px;">
                                    <li><a ui-sref="materials({slug:'tech_doc'})" ng-class="{'update':sectionIsUpdated(['tech_doc'])}">Техническая документация</a></li>
                                    <li><a ui-sref="topic.page({topicId:6,page:1})" ng-class="{'update':sectionIsUpdated(['tech_hardware'])}">Заявки на настройку оборудования</a></li>
                            </ul>
                        </span>

                        <a href="" class="profile_btn" style="float:right" ng-click="login()" ng-if="UserIdentity.isGuest">Вход</a>
                        <span ng-if="!UserIdentity.isGuest" class="dropdown dropdown-hover">
                        <a href="" class="profile_btn">
                            <span class="user_name">{{UserIdentity.nickname}}</span> <span class="glyphicon glyphicon-user"></span></a>
                        <ul class="dropdown-menu" style="top: 40px;left: -10px;">
                            <li><a ui-sref="user({userId:UserIdentity.id})">Посмотреть профиль</a></li>
                            <li><a ui-sref="reportvacation">Рапорт на отпуск</a></li>
                            <li><a ui-sref="messenger">Личные сообщения</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ng-click="logout()" href="">Выход</a></li>
                        </ul>
                        </span>
                    </div>
                    </div>
                    <div class="content" style="min-height:400px">
                        <div class="center_content" ui-view>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <a href="https://plus.google.com/108457946413274910426" rel="publisher"></a>
</div>
<script type="text/ng-template" id="NewsTmpl">
    <h2>Сводки</h2>
    <div class="big-spinner" ng-if="!news.records.length">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="newsRec in news.records" class="news-row panel panel-default">
        <div class="panel-heading">{{newsRec.title}}
            <div ng-if="UserIdentity.canMakeOrders" class="pull-right">
                <button type="button" ng-click="" ui-sref="editnews({id:newsRec.id})" class="btn btn-xs btn-default">
                    <span class="glyphicon glyphicon-pencil"></span>
                </button>
            </div>
        </div>
        <div class="panel-body" ng-bind-html="newsRec.text | to_trusted"></div>
        <div class="panel-footer">
            <span>{{newsRec.time}}</span> <img
                ng-src="/img/users/{{newsRec.issuer.img_src ? newsRec.issuer.id+'_'+newsRec.issuer.img_src+'.jpg' : (newsRec.issuer.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{newsRec.issuer.id}}">{{newsRec.issuer.nickname}}</a>
        </div>
    </div>
    <pagination ng-show="news.records.length" total-items="news.count" items-per-page="itemsPerPage" page="currentPage" max-size="7"
                class="pagination-sm" boundary-links="true" rotate="false"></pagination>
</script>

<script type="text/ng-template" id="FloodTmpl">
    <h2>Курилка</h2>
    <div class="well">
        <a class="btn btn-success" ui-sref="newtopic()">Добавить тему</a>
    </div>
    <div class="big-spinner" ng-if="!topics.length">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="topic in topics | orderBy:'lastMessageTime':'true'" class="relativeposition">
        <div ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)" ng-click="deleteTopic(topic)" class="button btn-danger btn-sm pull-right chat-delete-btn"><span class="glyphicon glyphicon-remove"></span></div>
        <div class="forumTopicHeader">
            <img
                ng-src="/img/users/{{topic.author.img_src ? topic.author.id+'_'+topic.author.img_src+'.jpg' : (topic.author.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                style=""><a href="#/user/view/{{topic.author.id}}">{{topic.author.nickname}}</a>
        </div>
        <div class="news-row panel panel-primary" ng-class="{'update':sectionIsUpdated(['topic_'+ topic.id])}">
            <a ui-sref="topic.page({topicId:topic.id,page:1})" class="panel-body" style="display: block;margin-top: 10px;white-space: nowrap;position: relative;">
                <b>{{topic.title}}: </b>
                <span style="color:#aaa">{{topic.firstMessageText}}</span>
                <div class="tinter"></div>
            </a>
            <div class="panel-footer">
                &nbsp;
            <span ng-if="topic.lastMessage" class="pull-right">
             Последнее сообщение:
             <img
                 ng-src="/img/users/{{topic.lastMessage.author.img_src ? topic.lastMessage.author.id+'_'+topic.lastMessage.author.img_src+'.jpg' : (topic.lastMessage.author.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                 style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{topic.lastMessage.author.id}}">{{topic.lastMessage.author.nickname}}</a> <span>{{topic.lastMessage.time | date:'dd.MM.yyyy, HH:mm'}}</span>
            </span>
            </div>
        </div>
    </div>
</script>

<script type="text/ng-template" id="TopicTmpl">
    <div class="big-spinner" ng-if="!topic.id">
        <div class="spinner-icon"></div>
    </div>
    <div ng-if="topic">
        <h2>{{topic.title}}</h2>
        <div ui-view>
        </div>
    </div>
        <pagination total-items="topic.itemCount" items-per-page="topic.limit" page="topic.currentPage" max-size="7"
                    class="pagination-sm" boundary-links="true" rotate="false"></pagination>

    <ng-form name="messageForm" role="form">
    <textarea sceditor="1" style="width: 100%;resize: none" rows="20" required></textarea>
    <div class="well">
        <a href="" class="btn btn-primary" ng-disabled="sceditor.text.length == 0" ng-click="post()">Отправить</a>
    </div>
    </ng-form>
</script>

<script type="text/ng-template" id="TopicMessagesTmpl">
    <div class="big-spinner" ng-if="!topic.messages || isLoading">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="message in topic.messages" class="media news-row" style="position: relative">
        <a class="pull-left" href="#/user/view/{{message.author.id}}">
            <img class="media-object"
                 style="width: 64px;height: 64px;border-radius: 50%;"
                 ng-src="/img/users/{{message.author.img_src ? message.author.id+'_'+message.author.img_src+'.jpg' : (message.author.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}">
        </a>
        <div class="media-body">
            <span class="pull-right">{{message.time | date:'dd.MM.yyyy, HH:mm'}}</span>
            <h4 class="media-heading with-underline">{{message.author.nickname}}</h4>
            <div bind-compiled-html="message.text"></div>
        </div>
        <div ng-if="message.isNew" class="small-spinner">
            <div class="spinner-icon"></div>
        </div>
    </div>
</script>


<script type="text/ng-template" id="MessengerTmpl">
    <h2>Личные сообщения</h2>
    <div class="big-spinner" ng-if="!conversations.length">
        <div class="spinner-icon"></div>
    </div>
    <div class="relativeposition" ng-repeat="conversation in conversations | orderBy:'lastMessageTime':'true'">
        <div ng-click="deleteConverstion(conversation)" class="button btn-danger btn-sm pull-right chat-delete-btn"><span class="glyphicon glyphicon-remove"></span></div>
        <div class="forumTopicHeader">
            <img
                ng-src="/img/users/{{conversation.sender.img_src ? conversation.sender.id+'_'+conversation.sender.img_src+'.jpg' : (conversation.sender.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                style=""><a href="#/user/view/{{conversation.sender.id}}">{{conversation.sender.nickname}}</a>
        </div>
        <div class="news-row panel panel-primary">
            <a ui-sref="conversation.page({senderId:conversation.sender.id,page:1})" class="panel-body" style="display: block;margin-top: 10px;white-space: nowrap;position: relative;">
                <b>{{conversation.lastMessage.sender.nickname}}: </b>
                <span style="color:#aaa">{{conversation.lastMessage.text}}</span>
                <div class="tinter"></div>
            </a>
            <div class="panel-footer">
                &nbsp;
            <span ng-if="conversation.lastMessage" class="pull-right">
             Последнее сообщение:
             <img
                 ng-src="/img/users/{{conversation.lastMessage.sender.img_src ? conversation.lastMessage.sender.id+'_'+conversation.lastMessage.sender.img_src+'.jpg' : (conversation.lastMessage.sender.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                 style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{conversation.lastMessage.sender.id}}">{{conversation.lastMessage.sender.nickname}}</a> <span>{{conversation.lastMessage.time | date:'dd.MM.yyyy, HH:mm'}}</span>
            </span>
            </div>
        </div>
    </div>
</script>

<script type="text/ng-template" id="ConversationTmpl">
    <div class="big-spinner" ng-if="!conversation.sender">
        <div class="spinner-icon"></div>
    </div>
    <div ng-if="conversation.sender">
        <h2>Беседа с {{conversation.sender.nickname}}</h2>
        <div ui-view>
        </div>
    </div>
        <pagination total-items="conversation.itemCount" items-per-page="conversation.limit" page="conversation.currentPage" max-size="7"
                    class="pagination-sm" boundary-links="true" rotate="false"></pagination>

    <ng-form name="messageForm" role="form">
    <textarea sceditor="1" style="width: 100%;resize: none" rows="20" required></textarea>
    <div class="well">
        <a href="" class="btn btn-primary" ng-disabled="sceditor.text.length == 0" ng-click="post()">Отправить</a>
    </div>
    </ng-form>
</script>

<script type="text/ng-template" id="ConversationMessagesTmpl">
    <div class="big-spinner" ng-if="!conversation.messages || isLoading">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="message in conversation.messages | orderBy:'time'" class="media news-row relativeposition" ng-class="{'alert-success':message.sender.id!=UserIdentity.id && !message.is_read}" style="position: relative">
        <a class="pull-left" href="#/user/view/{{message.sender.id}}">
            <img class="media-object"
                 style="width: 64px;height: 64px;border-radius: 50%;"
                 ng-src="/img/users/{{message.sender.img_src ? message.sender.id+'_'+message.sender.img_src+'.jpg' : (message.sender.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}">
        </a>
        <div class="media-body">
            <span class="pull-right">{{message.time | date:'dd.MM.yyyy, HH:mm'}}</span>
            <h4 class="media-heading with-underline">{{message.sender.nickname}}</h4>
            <div bind-compiled-html="message.text"></div>
        </div>
        <div ng-if="message.isNew" class="small-spinner">
            <div class="spinner-icon"></div>
        </div>
    </div>
</script>

<script type="text/ng-template" id="TextTmpl">
    <div class="big-spinner" ng-if="!text">
        <div class="spinner-icon"></div>
    </div>
    <h2><span>{{text.title}}</span><a ng-if="UserIdentity.canMakeOrders" title="Редактировать"
                                      class="btn btn-xs btn-default pull-right" ui-sref="edittext(text)"><span
                class="glyphicon glyphicon-pencil"></span></a></h2>
    <div ng-bind-html="text.text"></div>
</script>

<script type="text/ng-template" id="PersonalFileTmpl">
    <div class="big-spinner" ng-if="!text">
        <div class="spinner-icon"></div>
    </div>
    <h2><span>{{text.title}}</span><a ng-if="UserIdentity.canMakeOrders" title="Редактировать"
                                      class="btn btn-xs btn-default pull-right" ui-sref="editpersonalFile(text)"><span
                class="glyphicon glyphicon-pencil"></span></a></h2>
    <div ng-bind-html="text.text | to_trusted"></div>
</script>


<script type="text/ng-template" id="SchoolTmpl">
    <h2>{{subject.name}}</h2>
    <div class="well" ng-if="UserIdentity.canMakeOrders">
        <a class="btn btn-success" ui-sref="editmaterial({materialId:0, slug:subject.slug})">Добавить новый материал <span
                class="glyphicon glyphicon-plus-sign"></span></a>
    </div>
    <div class="big-spinner" ng-if="!subject.id">
        <div class="spinner-icon"></div>
    </div>
    <div class="news-row panel panel-danger">
        <div class="panel-heading">Содержание</div>
        <div class="panel-body">
            <ul>
                <li ng-repeat="material in subject.materials"><a ng-click="scrollTo('material_'+material.id)" href="">{{material.title}}</a>
                </li>
            </ul>
        </div>
    </div>
    <div ng-repeat="material in subject.materials" id="material_{{material.id}}" class="panel panel-primary">
        <div class="panel-heading">{{material.title}}
            <a ng-if="UserIdentity.canMakeOrders" title="Удалить" class="btn btn-xs btn-danger pull-right" href="" style="margin-left:5px" ng-click="deleteMaterial(material)">
                <span class="glyphicon glyphicon-minus"></span>
            </a>
            <a ng-if="UserIdentity.canMakeOrders" title="Редактировать" class="btn btn-xs btn-default pull-right" ui-sref="editmaterial({materialId:material.id, slug:subject.slug})">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
        </div>
        <div class="panel-body" bind-compiled-html="material.text"></div>
    </div>
</script>

<script type="text/ng-template" id="EditMaterialTmpl">
    <h2 ng-if="material.id>0">Редактирование материала</h2>
    <h2 ng-if="material.id == -1">Добавление нового материала</h2>
    <div class="big-spinner" ng-if="!material.isLoaded">
        <div class="spinner-icon"></div>
    </div>
    <div>
        <label>Заголовок</label>

        <p class="form-group input-group-lng">
            <input type="text" placeholder="Пустой заголовок"
                   class="form-control" ng-model="material.title"/>
        </p>
    </div>
    <label>Содержание</label>
    <textarea sceditor="1" style="width: 100%;resize: none;" rows="20"></textarea>
    <div style="margin-top:5px;">
        <p class="well">
            <button type="button" ng-click="save()"
                    ng-disabled="!material.isLoaded"
                    class="btn btn-primary">Сохранить
            </button>
        </p>
    </div>
</script>

<script type="text/ng-template" id="EditTextTmpl">
    <h2 ng-if="material.id>0">Редактирование Текста</h2>
    <h2 ng-if="material.id == -1">Добавление нового Текста</h2>
    <div class="big-spinner" ng-if="!material.isLoaded">
        <div class="spinner-icon"></div>
    </div>
    <div>
        <label>Заголовок</label>

        <p class="form-group input-group-lng">
            <input type="text" placeholder="Пустой заголовок"
                   class="form-control" ng-model="material.title"/>
        </p>
    </div>
    <label>Содержание</label>
    <textarea sceditor="1" style="width: 100%;resize: none;" rows="20"></textarea>
    <div style="margin-top:5px;">
        <p class="well">
            <button type="button" ng-click="save()"
                    ng-disabled="!material.isLoaded"
                    class="btn btn-primary">Сохранить
            </button>
        </p>
    </div>
</script>


<script type="text/ng-template" id="EditPersonalFileTmpl">
    <h2>Редактирование: {{material.title}}</h2>
    <div class="big-spinner" ng-if="!material.isLoaded">
        <div class="spinner-icon"></div>
    </div>
    <label>Содержание</label>
    <textarea sceditor="1" style="width: 100%;resize: none;" rows="20"></textarea>
    <div style="margin-top:5px;">
        <p class="well">
            <button type="button" ng-click="save()"
                    ng-disabled="!material.isLoaded"
                    class="btn btn-primary">Сохранить
            </button>
        </p>
    </div>
</script>

<script type="text/ng-template" id="OrdersTmpl">
    <h2>Приказы</h2>
    <div class="big-spinner" ng-if="!news.records.length">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="newsRec in news.records" class="news-row panel panel-default">
        <div class="panel-heading">{{newsRec.title}}</div>
        <div class="panel-body" bind-compiled-html="newsRec.text"></div>
        <div class="panel-footer">
            <span>{{newsRec.time}}</span> <img
                ng-src="/img/users/{{newsRec.issuer.img_src ? newsRec.issuer.id+'_'+newsRec.issuer.img_src+'.jpg' : (newsRec.issuer.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{newsRec.issuer.id}}">{{newsRec.issuer.nickname}}</a>
        </div>
    </div>
    <pagination ng-show="news.records.length" total-items="news.count" items-per-page="itemsPerPage" page="currentPage" max-size="7"
                class="pagination-sm" boundary-links="true" rotate="false"></pagination>

</script>

<script type="text/ng-template" id="ReportVacationTmpl">
    <h2>Рапорт на отпуск</h2>
    <div ng-if="vacation.isSaved" class="alert alert-success">Рапорт успешно отправлен</div>
    <ng-form name="vacationForm" role="form">
    <div>
        <label>Даты отпуска</label>
        <div class="row">
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon">Начало</span>
                    <input type="date" class="form-control" required ng-model="vacation.dateFrom">
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon">Конец</span>
                    <input type="date" class="form-control" required ng-model="vacation.dateTo">
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
        </div><!-- /.row -->
    </div>
        <br>
    <label>Причина</label>
    <textarea style="width: 100%;resize: none;" ng-model="vacation.reason" rows="5" class="form-control" required></textarea>
    <div style="margin-top:5px;">
        <p class="well">
            <button type="button" ng-click="save()"
                    ng-disabled="(vacationForm.$dirty && vacationForm.$invalid) || vacationForm.$pristine || vacationForm.isSubmitting"
                    class="btn btn-primary">Отправить</button>
        </p>
    </div>
    </ng-form>
</script>

<script type="text/ng-template" id="fileUploadBoxTemplate">
    <div ng-if="!uploadItem.isUploading" style="max-width: 200px;overflow: hidden">
        <input type="file" ng-file-select style="height: 22px;max-height: 22px">

        <div>
            <a href="" class="btn btn-xs btn-default" style="width: 100%;margin-top: -45px;pointer-events:none">Поменять
                фотографию</a>
        </div>
    </div>
    <div class="big-spinner" ng-if="uploadItem.isUploading">
        <div class="spinner-icon"></div>
        <div class="text">{{uploadItem.progress}}%</div>
    </div>
</script>

<script type="text/ng-template" id="UserTmpl">
    <div class="big-spinner" ng-if="!user">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="user">
        <button style="margin-left:10px" type="button" ng-click="expel(user)" class="btn btn-sm btn-danger pull-right" ng-if="!user.isDisabled && ((UserIdentity.id == 14) || (UserIdentity.id == 1))"><span class="glyphicon glyphicon-ban-circle"></span> Исключить</button>
        <button style="margin-left:10px" type="button" ng-click="reenlist(user)" class="btn btn-sm btn-success pull-right" ng-if="user.isDisabled && ((UserIdentity.id == 14) || (UserIdentity.id == 1))"><span class="glyphicon glyphicon-heart"></span> Восстановить</button>
        <button type="button" ui-sref="editUser({userId:user.id})" class="btn btn-sm btn-default pull-right" ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)"><span class="glyphicon glyphicon-pencil"></span></button>
        <!--<button type="button" ng-click="sync()" class="btn btn-sm btn-default pull-right" ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)"><span class="glyphicon glyphicon-refresh"></span></button>-->
        <h2>{{user.rank.name}} "{{user.nickname}}" {{user.firstname}}</h2>
        <br>
        <div class="expelled_tag" ng-if="user.isDisabled"></div>
        <table style="width: 100%">
            <tr>
                <td style="height: 210px;width: 210px">
                    <div style="margin-right:10px;position: relative">
                        <img ng-src="/img/users/{{user.img_src ? user.id+'_'+user.img_src+'.jpg' : (user.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                             style="width: 200px;display:block">
                        <div ng-if="user.activeVacation" class="vacation" title="{{user.activeVacation.reason}}, с {{user.activeVacation.date_from | date : 'dd.MM.yyyy'}} по {{user.activeVacation.date_to | date : 'dd.MM.yyyy'}}">В Отпуске!</div>
                        <div ng-if="user.isDefector" class="defector">Дезертир!</div>
                        <div ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)" file-upload-box></div>
                    </div>
                    <div class="btn-group" ng-if="!UserIdentity.isGuest && (UserIdentity.id != user.id)">
                      <button class="button btn btn-success" style="margin-bottom:10px;width:200px" ui-sref="conversation.page({senderId:user.id,page:1})"><span class="glyphicon glyphicon-envelope"></span> Отправить сообщение</button>
                    </div>
                </td>
                <td>
                    <table class="table" style="width: 100%">
                        <tbody>
                        <tr>
                            <th style="width: 90px">Дата рождения</th>
                            <td><span>{{user.birthDate | date : "dd.MM.yyyy"}}</span><br><span> ({{user.birthDate | age}} лет)</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Поступил</th>
                            <td>{{user.joinDate | date : "dd.MM.yyyy"}}</td>
                        </tr>
                        <tr ng-if="user.rank">
                            <th>{{user.rank.order < 5 ? 'Курс' : 'Звание'}}</th>
                            <td>{{user.rank.name}}</td>
                        </tr>
                        <tr ng-if="user.instructor">
                            <th>Степень</th>
                            <td>{{user.instructor.name}}</td>
                        </tr>
                        <tr ng-if="user.qualifications">
                            <th>Специальность</th>
                            <td>{{user.qualifications}}</td>
                        </tr>
                        <tr ng-if="UserIdentity.isInstructor">
                            <th>Заявка</th>
                            <td><a ui-sref="rosterUser({userId:user.id})">Посмотреть</a></td>
                        </tr>
                        <tr>
                            <th>Книжка пилота</th>
                            <td><a ui-sref="battleLog({userId:user.id})">Посмотреть</a></td>
                        </tr>
                        <tr ng-if="UserIdentity.isInstructor || UserIdentity.id == user.id">
                            <th>Оценки</th>
                            <td><a ui-sref="userMarks({userId:user.id})">Посмотреть</a></td>
                        </tr>
                        <tr ng-if="UserIdentity.isInstructor || UserIdentity.id == user.id">
                            <th>Личное дело</th>
                            <td><a ui-sref="personalFile({id:user.id})">Посмотреть</a></td>
                        </tr>
                        <tr ng-if="UserIdentity.isInstructor || UserIdentity.id == user.id">
                            <th>Почта</th>
                            <td><a href="mailto:{{user.email}}">{{user.email}}</a></td>
                        </tr>
                        <tr>
                            <th>Онлайн</th>
                            <td>{{user.lastOnline | date : 'dd.MM.yyyy HH:mm'}}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <?if (Yii::app()->params['isIL2']) :?>
                    <td ng-if="user.rank && user.rank.id!=8" style="width: 386px;padding-left: 5px;max-width: 386px;">
                        <div class="uniform uniform_bos " style="background: url(/img/uniform/bos/{{getUniformClass(user.rank.id)}}.png) no-repeat;">
                            <div class="uniform_rank uniform_rank_{{getUniformClass(user.rank.id)}} uniform_rank_{{user.rank.id}}"
                                 style="background: url(/img/uniform/bos/{{user.rank.id}}.png) no-repeat"
                                 title="{{user.rank.name}}">

                                <img title="{{medal.name}}" ng-repeat="medal in user.medals"
                                     style="top:{{medal.top}}px;left:{{medal.left}}px;" ng-src="/img/awards/{{medal.id}}.png"
                                     dnd-rect="medal.rect"
                                     dnd-draggable="UserIdentity.id == 1 || UserIdentity.id == 14"
                                     dnd-on-dragend="medalDrop(medal)"
                                     dnd-on-dragstart="medalPickup(medal)"
                                     dnd-containment="uniform_rank">
                            </div>
                        </div>
                        <div ng-show="dragging" dnd-container="true" style="position: relative;height:100px;background: #ff0000;margin-top:-100px;opacity:0.5"></div>
                    </td>
                <?else:?>
                <td ng-if="user.rank && user.rank.id!=8" style="width: 380px;padding-left: 5px;max-width: 380px;">
                    <div class="uniform {{user.is_clanner ? 'clanner' : ''}}" dnd-container="true">
                        <div class="unform_rank"
                             style="background: url(/img/uniform/{{user.is_clanner ? 'clanner/' : ''}}{{user.rank.id}}.png) no-repeat"
                             title="{{user.rank.name}}">
                        </div>
                        <img title="{{medal.name}}" ng-repeat="medal in user.medals"
                             style="top:{{medal.top}}px;left:{{medal.left}}px;" ng-src="/img/awards/{{medal.id}}.png"
                             dnd-draggable="UserIdentity.id == 1 || UserIdentity.id == 14"
                             dnd-on-dragstart="medalPickup($element,medal)"
                             dnd-on-dragend="medalDrop($element, medal)">
                    </div>
                    <div ng-show="dragging" dnd-container="true" style="position: relative;height:100px;background: #ff0000;margin-top:-100px;opacity:0.5"></div>
                </td>
                <?endif;?>
            </tr>
            <tr>
                <td ng-if="user.rank && user.rank.id!=8" colspan="3" style="padding-right:10px">
                    <div class="panel panel-default">
                        <div class="panel-heading"><span>Книга событий</span>
                            <button type="button" ng-if="UserIdentity.canMakeOrders" ng-click="addEvent(user.id)"
                                    class="btn btn-xs btn-success pull-right">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </div>
                        <div style="max-height:300px;overflow-y: scroll">
                        <table class="table">
                            <tbody>
                            <tr ng-repeat="event in user.events">
                                <td bind-compiled-html="event.text"></td>
                                <td style="width: 5%">{{event.date | date : "dd.MM.yyyy"}}</td>
                                <td ng-if="UserIdentity.canMakeOrders" style="min-width: 68px;">
                                    <button type="button" ng-click="editEvent(event)" class="btn btn-xs btn-default">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </button>
                                    <button type="button" ng-click="deleteEvent(event)" class="btn btn-xs btn-danger">
                                        <span class="glyphicon glyphicon-minus"></span>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</script>

<script type="text/ng-template" id="TreeItemTmpl">
    <img src="/img/design/bullets/channel_icon.png"><span> {{channel.name}}</span>
    <ul>
        <li ng-repeat="channel in channel.channels" ng-include="'TreeItemTmpl'"></li>
        <li ng-repeat="client in channel.clients"><img class="ts_group_icon" ng-repeat="group in client.groups" ng-if="group != 6"
                                                       ng-src="/img/groups/{{group}}{{client.is_clanner ? '_clanner' : ''}}.png"/><a
                href="" ng-if="client.uid" ng-click="getByUid(client.uid)"> {{client.name | clearNickname}}</a>
        </li>
    </ul>
</script>

<script type="text/ng-template" id="RosterTmpl">
    <h2>Заявка на вступление</h2>
    <br>
    <div class="alert alert-danger" ng-show="userForm.error">{{userForm.error}}</div>
    <ng-form name="rosterForm" role="form">
        <div>
        <?if (Yii::app()->params['isIL2']) :?>
            <div class="form-group input-group">
                <label for="user_birthdate">Дата рождения?</label>
                <p class="input-group"
                   ng-class="{true: 'has-error'}[(rosterForm.birthdate.$dirty && rosterForm.birthdate.$invalid)]">
                    <input type="date" placeholder="Например: 09.05.1945" name="birthdate" class="form-control"
                           ng-model="user.birthdate" ng-required="true"/>
                </p>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.firstname.$dirty && rosterForm.firstname.$invalid)]">
                <label>Ваше имя</label>
                <input type="text"
                       name="firstname"
                       placeholder="" class="form-control long"
                       ng-model="user.firstname"
                       required/>
            </div>
            <label>Насколько усердно готовы изучать учебный материал? (выберите цифру по пятибальной шкале)</label>
            <div class="form-group input-group">
                <p class="btn-group"
                   ng-class="{true: 'has-error'}[(rosterForm.birthdate.$dirty && rosterForm.birthdate.$invalid)]">
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'1'" required>1
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'2'">2</button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'3'">3</button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'4'">4</button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'5'">5</button>
                </p>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.nickname.$dirty && rosterForm.nickname.$invalid)]">
                <label>Ваш никнейм в игре?</label>
                <input type="text" name="nickname" class="form-control long" ng-model="user.nickname"
                       required/>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.nickname.$dirty && rosterForm.nickname.$invalid)]">
                <label>Часовой пояс от МСК</label>
                <input type="text" name="nickname" class="form-control long" ng-model="user.timezone"
                       required/>
            </div>
            <label>Откуда узнали о нашей школе? школу?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" placeholder="Например: Друг рассказал, Вычитал в журнале, и.т.д" id="user_reason"
                           class="form-control long" ng-model="user.reason" required/>
                </p>
            </div>
            <div class="form-group input-group">
                <label>Название вашего полка:</label>
                <div>
                    <p>
                        <button type="button" class="btn btn-default pull-right" ng-model="user.in_squad" btn-checkbox>
                            Не состою
                        </button>
                        <input type="text" placeholder="Например: Lws, Heer, DerAdler, и.т.д" id="user_squad"
                               class="form-control pull-left long" ng-model="user.squad" ng-disabled="user.in_squad"/>
                    </p>
                </div>
            </div>
            <label>Какое направление хотите освоить?</label>
            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.profession" btn-radio="'fighter'"
                            required>Истребитель
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.profession" btn-radio="'bomber'">
                        Бомбардировщик
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.profession" btn-radio="'shturmovik'">
                        Штурмовик
                    </button>
                </p>
            </div>
            <label>Какие ещё осваивали авиасимуляторы?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" placeholder="" id="user_sim"
                           class="form-control long" ng-model="user.simulators"/>
                </p>
            </div>
            <label>Есть-ли микрофон, наушники и программа для радиообмена TeamSpeak?</label>
            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.teamspeak" btn-radio="'yes'"
                            required>Да
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.teamspeak" btn-radio="'no'">
                        Нет
                    </button>
                </p>
            </div>

            <label>Название вашего джойстика?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" class="form-control long" ng-model="user.joyname"/>
                </p>
            </div>
            <label>Чем ведёте обзор из кабины?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" class="form-control long" ng-model="user.viewmode"/>
                </p>
            </div>
            <label>Есть-ли педали?</label>
            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.pedals" btn-radio="'yes'"
                            required>Да
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.pedals" btn-radio="'no'">
                        Нет
                    </button>
                </p>
            </div>
            <label>Наиболее удобное время вашего онлайна:</label>
            <div class="form-group input-group">
                <span>С</span>

                <div ng-model="user.onlineFrom" style="display:inline-block;vertical-align:middle">
                    <timepicker hour-step="1" minute-step="30" show-meridian="false"></timepicker>
                </div>
                <span>По</span>

                <div ng-model="user.onlineTo" style="display:inline-block;vertical-align:middle">
                    <timepicker hour-step="1" minute-step="30" show-meridian="false"></timepicker>
                </div>
            </div>
            <label>Согласны ли вы выполнять приказы в соответствии с уставом?</label>

            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.rules" btn-radio="'yes'" required>Да
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.rules" btn-radio="'no'">Нет</button>
                </p>
            </div>
            <?else:?>
            <div class="form-group input-group">
                <label for="user_birthdate">Дата рождения?</label>

                <p class="input-group"
                   ng-class="{true: 'has-error'}[(rosterForm.birthdate.$dirty && rosterForm.birthdate.$invalid)]">
                    <input type="date" placeholder="Например: 09.05.1945" name="birthdate" class="form-control"
                           ng-model="user.birthdate" ng-required="true"/>
                </p>
            </div>
            <label>Насколько усердно готовы изучать учебный материал? (выберите цифру по пятибальной шкале)</label>

            <div class="form-group input-group">
                <p class="btn-group"
                   ng-class="{true: 'has-error'}[(rosterForm.birthdate.$dirty && rosterForm.birthdate.$invalid)]">
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'1'" required>1
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'2'">2</button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'3'">3</button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'4'">4</button>
                    <button type="button" class="btn btn-default" ng-model="user.scale" btn-radio="'5'">5</button>
                </p>
            </div>
            <label>Каким образом попали в нашу школу?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" placeholder="Например: Друг рассказал, Вычитал в журнале, и.т.д" id="user_reason"
                           class="form-control long" ng-model="user.reason" required/>
                </p>
            </div>
            <div class="form-group input-group">
                <label>Название вашего полка:</label>
                <div>
                    <p>
                        <button type="button" class="btn btn-default pull-right" ng-model="user.in_squad" btn-checkbox>
                            Не состою
                        </button>
                        <input type="text" placeholder="Например: Lws, Heer, DerAdler, и.т.д" id="user_squad"
                               class="form-control pull-left long" ng-model="user.squad" ng-disabled="user.in_squad"/>
                    </p>
                </div>
            </div>
            <div class="form-group input-group">
                <label>Что побудило Вас освоить режим симуляторных боев?</label>
                <div>
                    <p>
                        <textarea class="form-control pull-left long" ng-model="user.motive"></textarea>
                    </p>
                </div>
            </div>
            <label>Какую профессию хотите освоить?</label>
            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.profession" btn-radio="'fighter'"
                            required>Истребитель
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.profession" btn-radio="'bomber'">
                        Бомбардировщик
                    </button>
                </p>
            </div>
            <label ng-if="user.profession=='fighter'">Наличие в ангаре самолета Bf-109E-3 для истребителей</label>
            <label ng-if="user.profession=='bomber'">Наличие в ангаре самолетов Ju-87 и Не-111 для бомбардировщиков</label>
            <div class="form-group input-group" ng-if="user.profession">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-if="user.profession=='fighter'" ng-model="user.craft.bf109" btn-checkbox>Bf-109E-3</button>
                    <button type="button" class="btn btn-default" ng-if="user.profession=='bomber'" ng-model="user.craft.ju87" btn-checkbox>Ju-87</button>
                    <button type="button" class="btn btn-default" ng-if="user.profession=='bomber'" ng-model="user.craft.he111" btn-checkbox>He-111</button>
                </p>
            </div>
            <label>Технику какой нации предпочитаете пилотировать?</label>
            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.nation" btn-radio="'germany'" required>
                        Германия
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.nation" btn-radio="'ussr'">СССР
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.nation" btn-radio="'usa'">США</button>
                    <button type="button" class="btn btn-default" ng-model="user.nation" btn-radio="'uk'">Британия
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.nation" btn-radio="'japan'">Япония
                    </button>
                </p>
                <br>
                <label>Почему?</label>
                <textarea class="form-control pull-left long" ng-model="user.nation_reason"></textarea>
            </div>

            <label>Как давно играете в War Thunder?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" class="form-control long" ng-model="user.wttime"/>
                </p>
            </div>
            <label>Название вашего джойстика?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" class="form-control long" ng-model="user.joyname"/>
                </p>
            </div>
            <label>Чем ведёте обзор из кабины?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" class="form-control long" ng-model="user.viewmode"/>
                </p>
            </div>

            <label>Ваше хобби или профессия могущие принести пользу школе?</label>
            <div class="form-group input-group">
                <p class="form-group input-group-lng">
                    <input type="text" class="form-control long" ng-model="user.hobby"/>
                </p>
            </div>

            <label>Время вашего онлайна:</label>
            <div class="form-group input-group">
                <span>С</span>

                <div ng-model="user.onlineFrom" style="display:inline-block;vertical-align:middle">
                    <timepicker hour-step="1" minute-step="30" show-meridian="false"></timepicker>
                </div>
                <span>По</span>

                <div ng-model="user.onlineTo" style="display:inline-block;vertical-align:middle">
                    <timepicker hour-step="1" minute-step="30" show-meridian="false"></timepicker>
                </div>
            </div>
            <label>Согласны ли вы выполнять приказы в соответствии с уставом?</label>

            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.rules" btn-radio="'yes'" required>Да
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.rules" btn-radio="'no'">Нет</button>
                </p>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.nickname.$dirty && rosterForm.nickname.$invalid)]">
                <label>Ваш никнейм в игре?</label>
                <input type="text" name="nickname" class="form-control long" ng-model="user.nickname"
                       required/>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.firstname.$dirty && rosterForm.firstname.$invalid)]">
                <label>Ваше имя?</label>
                <input type="text"
                       name="firstname"
                       placeholder="Например: Женя, Паша, Виталий, и.т.д" class="form-control long"
                       ng-model="user.firstname"
                       required/>
            </div>
            <?endif?>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.email.$dirty && rosterForm.email.$invalid)]">
                <label>Укажите вашу электронную почту (будет использоватся в качестве Логина)</label>
                <input type="email"
                       placeholder="Укажите свою электроннную почту"
                       class="form-control long"
                       name="email"
                       id="email"
                       ng-model="user.private.email"
                       required>
            </div>
            <div class="form-group input-group">
                <label>Ваш скайп (если есть)</label>
                <input type="text"
                       class="form-control long"
                       ng-model="user.skype">
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.password.$dirty && rosterForm.password.$invalid)]">
                <label>Укажите пароль, который будет использоватся для входа в систему</label>
                <input type="password"
                       placeholder="Укажите новый пароль"
                       class="form-control long"
                       name="password"
                       id="password"
                       ng-model="user.private.password"
                       required>
            </div>
            </div>
    </ng-form>
    <div class="alert alert-danger" ng-show="userForm.error">{{userForm.error}}</div>
    <div>
        <p class="well">
            <span class="help-block" ng-show="(rosterForm.$dirty && rosterForm.$invalid) || rosterForm.$pristine"><b>Пожалуйста,
                    заполните все поля</b></span>
            <button type="button" ng-click="send()"
                    ng-disabled="(rosterForm.$dirty && rosterForm.$invalid) || rosterForm.$pristine || userForm.isSubmitting"
                    class="btn btn-primary">Отправить
            </button>
        </p>
    </div>
</script>

<script type="text/ng-template" id="EditUserTmpl">
    <div class="big-spinner" ng-if="!user.id">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="user.id">
        <h2>Редактировать профиль</h2>
        <br>

        <div class="alert alert-danger" ng-show="userForm.error">{{userForm.error}}</div>
        <ng-form name="rosterForm" role="form">
            <div>
                <div class="form-group input-group">
                    <label for="user_birthdate">Дата рождения</label>

                    <p class="input-group"
                       ng-class="{true: 'has-error'}[(rosterForm.birthdate.$dirty && rosterForm.birthdate.$invalid)]">
                        <input type="date" placeholder="Например: 09.05.1945" name="birthDate" class="form-control"
                               ng-model="user.birthDate" ng-required="true"/>
                    </p>
                </div>
                <label>Состоит в другом Полку</label>
                <div class="form-group input-group">
                    <p class="btn-group">
                        <button type="button" class="btn btn-default" ng-model="user.is_clanner" btn-radio="1" required>
                            Да
                        </button>
                        <button type="button" class="btn btn-default" ng-model="user.is_clanner" btn-radio="0">
                            Нет
                        </button>
                    </p>
                </div>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(rosterForm.nickname.$dirty && rosterForm.nickname.$invalid)]">
                    <label>Никнейм</label>
                    <input type="text"
                           style="margin-bottom: 10px" name="nickname" class="form-control" ng-model="user.nickname"
                           required/>
                </div>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(rosterForm.firstname.$dirty && rosterForm.firstname.$invalid)]">
                    <label>Имя</label>
                    <input type="text"
                           name="firstname"
                           style="margin-bottom: 10px"
                           placeholder="Например: Женя, Паша, Виталий, и.т.д" class="form-control"
                           ng-model="user.firstname"
                           required/>
                </div>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(rosterForm.qualifications.$dirty && rosterForm.qualifications.$invalid)]">
                    <label>Специальности</label>
                    <div class="form-group input-group">
                        <p class="btn-group">
                            <button type="button" class="btn btn-default" ng-model="user.qualifications.fighter" btn-checkbox>Истребитель
                            </button>
                            <button type="button" class="btn btn-default" ng-model="user.qualifications.bomber" btn-checkbox>Бомбардировщик
                            </button>
                            <button type="button" class="btn btn-default" ng-model="user.qualifications.shturmovik" btn-checkbox>Штурмовик
                            </button>
                        </p>
                    </div>
                </div>
                <div class="form-group input-group">
                    <label>Тимспик</label><br>
                    <input type="hidden" ng-model="user.ts_id" style="width:350px"
                            required
                            data-placeholder="Привязка к TeamSpeak"
                            ui-select2="tsSelect2Options">
                </div>
        </ng-form>
        <div class="alert alert-danger" ng-show="userForm.error">{{userForm.error}}</div>
        <div>
            <p class="well">
                <button type="button" ng-click="save()"
                        ng-disabled="userForm.isSubmitting || (!user.qualifications.bomber && !user.qualifications.fighter && !user.qualifications.shturmovik)" class="btn btn-primary">Сохранить</button>
            </p>
        </div>
    </div>
</script>

<script type="text/ng-template" id="AfterRosterTmpl">
    <h2>Заявка отправлена</h2>
    <br>
    <div class="alert alert-warning" style="font-size:20px">
        Что-бы вас приняли, пожалуйста зайдите на наш <a href="ts3server://luftwaffeschule.ru/?nickname={{UserIdentity.fullname}}">сервер
            TeamSpeak</a> для собеседования и ожидайте в приёмной. Кто-то из инструкторов к вам обязательно подойдёт.
        <br>
        TeamSpeak можно скачать пройдя по <a href="http://www.teamspeak.com/?page=downloads" target="_blank">этой</a>
        ссылке.
    </div>
</script>


<script type="text/ng-template" id="BarracksTmpl">
    <h2>Казарма - {{headings[filters.which]}}<span ng-show="pilots.length"> {{pilots.length}} пилотов</span></h2>
    <div class="well well-sm">
        <div class="input-group">
            <span class="input-group-addon glyphicon glyphicon glyphicon-search" style="top:0"></span>
            <input type="text" class="form-control pilot_search" placeholder="Найти по имени или никнейму" ng-model="filters.name">
                <p class="btn-group" style="float:right">
                    <button type="button" class="btn btn-default" ng-model="dataSize" btn-radio="2">
                        <span class="glyphicon glyphicon-list"></span>
                    </button>
                    <button type="button" class="btn btn-default" ng-model="dataSize" btn-radio="1">
                        <span class="glyphicon glyphicon-th-large"></span>
                    </button>
                </p>
        </div>
        <div class="btn-group btn-group-justified" style="margin-top:5px">
            <div class="btn-group">
                <button type="button" class="btn btn-default" ng-model="filters.which" btn-radio="0">
                    <span class="glyphicon glyphicon-screenshot"></span> На службе
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-success" ng-model="filters.which" btn-radio="1">
                    <span class="glyphicon glyphicon-briefcase"></span> В отпуске
                </button>
            </div>
            <div class="btn-group">
                <button type="button" ng-if="UserIdentity.canMakeOrders" class="btn btn-warning" ng-model="filters.which" btn-radio="2">
                    <span class="glyphicon glyphicon-ban-circle"></span> Дезертиры
                </button>
            </div>
            <div class="btn-group" ng-if="UserIdentity.id == 1 || UserIdentity.id == 14">
                <button type="button" class="btn btn-danger" ng-model="filters.which" btn-radio="3">
                    <span class="glyphicon glyphicon-remove"></span> Исключены
                </button>
            </div>
        </div>
    </div>
    <div style="min-height: 550px">
        <div class="big-spinner" ng-if="isLoading">
            <div class="spinner-icon"></div>
        </div>
        <div ng-if="(dataSize == 2)">
        <div class="barracks-separator">Офицеры</div>
        <div class="" ng-repeat="pilot in pilots | barracksFilter:0"">
            <a style="height: 90px;margin-bottom: 10px" class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:80px; height:80px; float:left">
                <div class="floating_rank small"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank small" style="left:23px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <div class="caption" style="padding-top: 20px;float: left;">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                </div>
            </a>
        </div>
        <div class="barracks-separator">Курсанты</div>
        <div class="" ng-repeat="pilot in pilots | barracksFilter:1"">
            <a style="height: 90px;margin-bottom: 10px" class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:80px; height:80px; float:left">
                <div class="floating_rank small"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank small" style="left:23px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <div class="caption" style="padding-top: 20px;float: left;">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                </div>
            </a>
        </div>
    <div class="barracks-separator">Выпускники</div>
        <div class="" ng-repeat="pilot in pilots | barracksFilter:2"">
            <a style="height: 90px;margin-bottom: 10px" class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:80px; height:80px; float:left">
                <div class="floating_rank small"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank small" style="left:23px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <div class="caption" style="padding-top: 20px;float: left;">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                </div>
            </a>
        </div>
        </div>
    <div ng-if="(dataSize == 1)">
        <div class="barracks-separator">Офицеры</div>
        <div class="col-sm-6 col-md-3 user-cell" ng-repeat="pilot in pilots | barracksFilter:0">
            <a class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:175px; height:175px">
                <div ng-if="pilot.activeVacation" class="vacation" title="{{pilot.activeVacation.reason}}, c {{pilot.activeVacation.date_from | date : 'dd.MM.yyyy'}} по {{pilot.activeVacation.date_to | date : 'dd.MM.yyyy'}}">В Отпуске!</div>
                <div ng-if="pilot.isDefector" class="defector">Дезертир!</div>
                <div class="floating_rank"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank" style="left:40px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <div class="caption">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                </div>
            </a>
        </div>
        <div class="barracks-separator">Курсанты</div>
        <div class="col-sm-6 col-md-3 user-cell" ng-repeat="pilot in pilots | barracksFilter:1">
            <a class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:175px; height:175px">
                <div ng-if="pilot.activeVacation" class="vacation" title="{{pilot.activeVacation.reason}}, c {{pilot.activeVacation.date_from | date : 'dd.MM.yyyy'}} по {{pilot.activeVacation.date_to | date : 'dd.MM.yyyy'}}">В Отпуске!</div>
                <div ng-if="pilot.isDefector" class="defector">Дезертир!</div>
                <div class="floating_rank"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank" style="left:40px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <div class="caption">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                </div>
            </a>
        </div>
        <div class="barracks-separator">Выпускники</div>
        <div class="col-sm-6 col-md-3 user-cell" ng-repeat="pilot in pilots | barracksFilter:2">
            <a class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:175px; height:175px">
                <div ng-if="pilot.activeVacation" class="vacation" title="{{pilot.activeVacation.reason}}, c {{pilot.activeVacation.date_from | date : 'dd.MM.yyyy'}} по {{pilot.activeVacation.date_to | date : 'dd.MM.yyyy'}}">В Отпуске!</div>
                <div ng-if="pilot.isDefector" class="defector">Дезертир!</div>
                <div class="floating_rank"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank" style="left:40px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <div class="caption">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                </div>
            </a>
        </div>
    </div>
    </div>
</script>

<script type="text/ng-template" id="RosterUserTmpl">
    <div class="big-spinner" ng-if="!pilot.id">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="pilot.id">
        <h2>Заявка на вступление от {{pilot.nickname}} ({{pilot.firstname}})</h2>
        <br>
        <label>Дата регистрации:</label><br>
        <span>{{pilot.joinDate | date : "dd.MM.yyyy"}}</span><br>
        <?if (Yii::app()->params['isIL2']) :?>
            <label>Дата рождения:</label><br>
            <span>{{pilot.roster.birthdate | date : "dd.MM.yyyy"}}</span>
            (<span>{{pilot.roster.birthdate | age}}</span> лет)<br>
            <label>Имя:</label><br>
            {{pilot.firstname}}<br>
            <label>Готовность к обучению:</label><br>
            <span>{{pilot.roster.scale}}</span><br>
            <label>Часовой пояс от МСК:</label><br>
            <span>{{pilot.roster.timezone}}</span><br>
            <label>Откуда узнал о школе:</label><br>
            <span>{{pilot.roster.reason}}</span><br>
            <label>Состоит в скваде:</label><br>
            <span ng-bind="pilot.roster.squad ? pilot.roster.squad : 'Нет'"></span><br>
            <label>Хочет стать:</label><br>
            <span ng-show="pilot.roster.profession == 'fighter'">Истребителем</span>
            <span ng-show="pilot.roster.profession == 'bomber'">Бомбардировщиком</span>
            <span ng-show="pilot.roster.profession == 'shturmovik'">Штурмовиком</span>
            <br>
            <label>Ещё играл/играет в симуляторы:</label><br>
            <span>{{pilot.roster.simulators}}</span><br>

            <label>Микрофон, наушники, ТС:</label><br>
            <span ng-show="pilot.roster.teamspeak == 'yes'">Да</span>
            <span ng-show="pilot.roster.teamspeak == 'no'">Нет</span>
            <br>

            <label>Джойстик:</label><br>
            <span>{{pilot.roster.joyname}}</span><br>
            <label>Обзор:</label><br>
            <span>{{pilot.roster.viewmode}}</span><br>

            <label>Педали:</label><br>
            <span ng-show="pilot.roster.pedals == 'yes'">Да</span>
            <span ng-show="pilot.roster.pedals == 'no'">Нет</span>
            <br>

            <label>Скайп:</label><br>
            <span>{{pilot.roster.skype}}</span><br>
            <br>


        <?else:?>
        <a href="http://warthunder.ru/ru/community/userinfo/?nick={{pilot.nickname}}"><img src="http://warthunder.ru/favicon.ico" height="40px">Профиль WT</a><br>
        <label>Дата рождения:</label><br>
        <span>{{pilot.roster.birthdate | date : "dd.MM.yyyy"}}</span>
        (<span>{{pilot.roster.birthdate | age}}</span> лет)<br>
        <label>Готовность к обучению:</label><br>
        <span>{{pilot.roster.scale}}</span><br>
        <label>Попал в школу посредством:</label><br>
        <span>{{pilot.roster.reason}}</span><br>
        <label>Освоить симуляторные бои побудило:</label><br>
        <span>{{pilot.roster.motive}}</span><br>
        <label>Состоит в скваде:</label><br>
        <span ng-bind="pilot.roster.squad ? pilot.roster.squad : 'Нет'"></span><br>
        <label>Хочет стать:</label><br>
        <span ng-show="pilot.roster.profession == 'fighter'">Истребителем</span>
        <span ng-show="pilot.roster.profession == 'bomber'">Бомбардировщиком</span>
        <br>
        <label>Наличие в ангаре самолетов:</label><br>
        <span ng-show="pilot.roster.craft.bf109">Bf 109E-3</span>
        <span ng-show="pilot.roster.craft.ju87">Ju-87</span>
        <span ng-show="pilot.roster.craft.he111">He-111</span>
        <br>
        <label>Предпочитает технику:</label><br>
        <span>{{pilot.roster.nation}}</span><br>
        <label>Потому-что:</label><br>
        <span>{{pilot.roster.nation_reason}}</span><br>

        <label>В WT играет:</label><br>
        <span>{{pilot.roster.wttime}}</span><br>

        <label>Джойстик:</label><br>
        <span>{{pilot.roster.joyname}}</span><br>

        <label>Обзор:</label><br>
        <span>{{pilot.roster.viewmode}}</span><br>

        <label>Полезное хобби:</label><br>
        <span>{{pilot.roster.hobby}}</span><br>
        <?endif;?>
        <label>Время онлайна:</label><br>
        <span>C </span><span>{{pilot.roster.onlineFrom | date : "HH:mm"}}</span><span> По </span><span>{{pilot.roster.onlineTo | date : "HH:mm"}}</span>
        <br><br>
        <div ng-show="pilot.rank!=8" class="alert alert-success">Пилот принят</div>
        <div ng-form="rosterForm" ng-show="pilot.rank==8">
            <p class="well">
                <input type="hidden" ng-model="rosterForm.tsId" style="width:350px"
                        required
                        data-placeholder="Привязка к TeamSpeak"
                        ui-select2="tsSelect2Options">&nbsp;
                <button type="button" ng-click="accept()"
                        ng-disabled="(rosterForm.$dirty && rosterForm.$invalid) || rosterForm.$pristine || rosterForm.isSubmitting"
                        class="btn btn-success">Принять
                </button>
                <button type="button" ng-click="reject()"
                        ng-disabled="rosterForm.isSubmitting"
                        class="btn btn-danger">Отклонить
                </button>

            </p>
        </div>
    </div>
</script>

<script type="text/ng-template" id="loginDialogTmpl">
    <div class="modal-content">
        <div class="modal-header"><h4 class="modal-title"><span class="glyphicon glyphicon-user"></span><span
                    ng-show="!userForm.forgotPass"> Вход</span><span
                    ng-show="userForm.forgotPass"> Восстановить пароль</span></h4></div>
        <div class="modal-body">
            <ng-form name="nameDialog" novalidate role="form" ng-if="!userForm.forgotPass">
                <div class="form-group input-group-lg"
                     ng-class="{true: 'has-error'}[(nameDialog.email.$dirty && nameDialog.email.$invalid) || (nameDialog.password.$dirty && nameDialog.password.$invalid)]">
                    <input type="email"
                           placeholder="Ваша электронная почта"
                           class="form-control"
                           name="email"
                           id="email"
                           ng-model="user.email"
                           ng-keyup="hitEnter($event)"
                           required>
                    <input type="password"
                           style="margin-top: 10px;"
                           placeholder="Ваш пароль"
                           class="form-control"
                           name="password"
                           id="password"
                           ng-model="user.password"
                           ng-keyup="hitEnter($event)"
                           required>
                    <span class="help-block" ng-show="user.error">{{user.error}}</span>
                    <span class="help-block"> <b><a href="" ng-click="userForm.forgotPass=true">Упс, я Забыл пароль
                                :(</a></b></span>
                </div>
            </ng-form>
            <ng-form name="forgotDialog" novalidate role="form" ng-if="userForm.forgotPass">
                <div class="form-group input-group-lg"
                     ng-class="{true: 'has-error'}[(forgotDialog.email2.$dirty && forgotDialog.email2.$invalid)]">
                    <input type="email"
                           placeholder="Ваша электронная почта"
                           class="form-control"
                           name="email2"
                           id="email2"
                           ng-model="user.email"
                           ng-keyup="hitEnterForgot($event)"
                           required>
                    <span class="help-block" ng-show="user.error">{{user.error}}</span>
                    <span class="help-block"> <b><a href="" ng-click="userForm.forgotPass=false">О, я вспомнил пароль!
                                :)</a></b></span>
                </div>
            </ng-form>
        </div>
        <div class="modal-footer" ng-if="!userForm.forgotPass">
            <button type="button" class="btn btn-default" ng-click="cancel()">Отмена</button>
            <button type="button" class="btn btn-primary" ng-click="save()"
                    ng-disabled="(nameDialog.$dirty && nameDialog.$invalid) || nameDialog.$pristine">Вход
            </button>
        </div>
        <div class="modal-footer" ng-if="userForm.forgotPass">
            <button type="button" class="btn btn-default" ng-click="cancel()">Отмена</button>
            <button type="button" class="btn btn-primary" ng-click="sendPass()"
                    ng-disabled="(forgotDialog.$dirty && forgotDialog.$invalid) || forgotDialog.$pristine">Выслать новый
            </button>
        </div>
    </div>
</script>

<script type="text/ng-template" id="OrderCreatorTmpl">
    <h2>Отдать приказ</h2>
    <ng-form name="orderForm" novalidate role="form">
        <div class="form-group">
            <div style="display: inline-block;width: 440px;vertical-align: top;">
                <select data-placeholder="Выберите пилотов"
                        multiple
                        ui-select2="pilotSelect2Options"
                        style="width: 440px;margin-bottom: 6px"
                        class="form-control"
                        ng-model="orderData.pilots"
                        required>
                    <option ng-repeat="pilot in initialData.pilotsArray  | orderBy:'nickname'" value="{{pilot.id}}"
                            data-rankid="{{pilot.rank}}">
                        {{pilot.nickname}}
                    </option>
                </select>
                <textarea style="resize: none;width: 100%" rows="3" class="form-control" placeholder="Впишите Событие"
                          ng-model="updatedData.event"></textarea>
            </div>
            <div
                class="form-control"
                style="resize: none;display:inline-block;width: 48%;padding:5px;border:1px solid #aaa;min-height:114px;background: #eee;color:#555"
                rows="5" id="completeData" data-ng-bind-html="updatedData.complete"></div>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Ручной приказ" ng-model="updatedData.customText">
        </div>
        <div class="panel panel-primary" ng-repeat="pilot in updatedData.pilots">
            <div class="panel-heading">{{pilot.nickname}}</div>
            <div class="panel-body">
                <div style="display: inline-block">
                    <div style="padding-bottom: 10px; padding-right:10px;vertical-align: top">
                        <select data-placeholder="Выберите Звание"
                                ui-select2="rankSelect2Options"
                                style="width: 300px"
                                ng-model="pilot.rank">
                            <option></option>
                            <option ng-repeat="rank in initialData.rankArray | orderBy:'order'" value="{{rank.id}}">
                                {{rank.name}}
                            </option>
                        </select>
                    </div>
                    <div>
                        <select data-placeholder="Выберите Инструкторскую Категорию"
                                ui-select2="instructorSelect2Options"
                                style="width: 300px;"
                                ng-model="pilot.instructor">
                            <option></option>
                            <option ng-repeat="rank in initialData.instructorsArray | orderBy:'order'"
                                    value="{{rank.id}}">{{rank.name}}
                            </option>
                        </select>
                    </div>
                </div>
                <select data-placeholder="Выберите Награды"
                        ui-select2="awardSelect2Options"
                        class="form-control"
                        style="width: 512px;vertical-align: top;"
                        multiple
                        ng-model="pilot.awards">
                    <option ng-repeat="award in initialData.awards" value="{{award.id}}"
                            ng-disabled="{{(initialData.pilots[pilot.id].awards.indexOf(award.id)>=0) && award.only_one_allowed ? true : false}}">
                        {{award.name}}
                    </option>
                </select>
            </div>
        </div>
        <div>
            <p class="well">
                <input type="date" placeholder="Например: 09.05.1945" name="time" class="form-control cdate"
                       ng-model="updatedData.time" ng-required="true"/>
                <button type="button" ng-click="save()"
                        ng-disabled="(orderForm.$dirty && orderForm.$invalid) || orderForm.$pristine || orderData.isSubmitting"
                        class="btn btn-primary">Отдать
                </button>
            </p>
        </div>
    </ng-form>
</script>

<script type="text/ng-template" id="NewsCreatorTmpl">
    <h2 ng-if="!newsRecord.id">Добавить новость</h2>
    <h2 ng-if="newsRecord.id">Редактировать новость</h2>
    <ng-form name="newsForm" novalidate role="form">
        <div class="form-group">
            <input type="text" placeholder="Впишите заголовок" class="form-control"
                   ng-model="newsRecord.title" ng-required="true"/>
            <div style="height: 5px"></div>
            <textarea style="resize: none;width: 100%;"
                      rows="6"
                      class="form-control"
                      placeholder="Впишите Новость"
                      ng-required="true"
                      ng-model="newsRecord.text">
            </textarea>
            <div class="form-group input-group" style="margin-top:10px">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="newsRecord.onlyRegistered" btn-checkbox><input type="checkbox" ng-model="newsRecord.onlyRegistered" style="margin:0;vertical-align:middle;"><span style="vertical-align:middle;"> Только для зарегистрированных пилотов</span></button>
                </p>
            </div>
        </div>
        <div>
            <p class="well">
                <button type="button" ng-click="save()"
                        ng-disabled="(newsForm.$dirty && newsForm.$invalid) || newsForm.$pristine || newsRecord.isSubmitting"
                        class="btn btn-primary"><span ng-if="!newsRecord.id">Опубликовать</span><span ng-if="newsRecord.id">Сохранить</span>
                </button>
            </p>
        </div>
    </ng-form>
</script>

<script type="text/ng-template" id="RecoverUserTmpl">
    <h2>Востановление пароля</h2>
    <br>
    <div class="alert alert-danger" ng-show="recoverForm.error">{{recoverForm.error}}</div>
    <ng-form name="recoverForm" role="form">
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(recoverForm.password.$dirty && recoverForm.password.$invalid)]">
                <label>Укажите новый пароль который будет использоватся для входа в систему</label>
                <input type="password"
                       style="margin-bottom: 10px"
                       placeholder="Укажите новый пароль"
                       class="form-control"
                       name="password"
                       id="password"
                       ng-model="recovery.password"
                       required>
            </div>
    </ng-form>
    <div>
        <p class="well">
            <button type="button" ng-click="recover()"
                    ng-disabled="(recoverForm.$dirty && recoverForm.$invalid) || recoverForm.$pristine || recoverForm.isSubmitting"
                    class="btn btn-primary">Обновить
            </button>
        </p>
    </div>
</script>


<script type="text/ng-template" id="NewTopicTmpl">
    <h2>Добавить Тему</h2>
    <ng-form name="topicForm" novalidate role="form">
        <div ng-if="topicRecord.topicAdded" class="alert alert-success">Тема успешно добавленна</div>
        <div class="form-group">
            <input type="text" placeholder="Впишите заголовок" class="form-control"
                   ng-model="topicRecord.title" ng-required="true"/>

            <div style="height: 5px"></div>
            <textarea style="resize: none;width: 100%;"
                      rows="10"
                      placeholder="Впишите Сообщение"
                      sceditor="1">
            </textarea>
        </div>
        <div>
            <p class="well">
                <button type="button" ng-click="save()"
                        ng-disabled="(topicForm.$dirty && topicForm.$invalid) || topicForm.$pristine || topicRecord.isSubmitting"
                        class="btn btn-primary">Опубликовать
                </button>
            </p>
        </div>
    </ng-form>
</script>


<script type="text/ng-template" id="InactiveTmpl">
    <h2>Дезертиры<span ng-show="pilots.length"> ({{pilots.length}})</span></h2>
    <div style="min-height: 550px">
        <div class="big-spinner" ng-if="isLoading">
            <div class="spinner-icon"></div>
        </div>
        <div class="" ng-repeat="pilot in pilots">
            <div style="height: 90px;margin-bottom: 10px" class="thumbnail isRelative hoverable" ng-class="{'expelled' : pilot.expelled}">
                <img ui-sref="user({userId:pilot.id})" ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:80px; height:80px; float:left">
                <div class="floating_rank small"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank small" style="left:23px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <a class="caption" style="padding-top: 5px;float: left;" ui-sref="user({userId:pilot.id})">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                    <div style="padding-top:10px">Ушёл <i>{{pilot.lastOnline | timeAgo}}</i></div>
                </a>
                <div style="margin-top: 15px;margin-right: 13px;" class="pull-right">
                    <button type="button" ng-click="acquit(pilot)" class="btn btn-s btn-success" style="margin-right:5px">
                        <span class="glyphicon glyphicon-heart"></span> Оправдать
                    </button>
                    <button type="button" ng-click="expel(pilot)" class="btn btn-s btn-danger">
                        <span class="glyphicon glyphicon-ban-circle"></span> Исключить
                    </button>
                    <div ng-if="pilot.lastWarning" style="margin-top:5px;text-align: center">Предупреждён {{pilot.lastWarning | timeAgo}}</div>
                    <div ng-if="!pilot.lastWarning" style="margin-top:5px;text-align: center">Ещё не предупреждён</div>
                </div>
            </div>
        </div>
    </div>
</script>


<script type="text/ng-template" id="BattleLogTmpl">
    <div class="big-spinner" ng-if="loading">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="!loading">
        <button style="margin-left:10px" type="button" ui-sref="addBattleLog({userId:user.id})" class="btn btn-sm btn-success pull-right" ng-if="((UserIdentity.isInstructor) || (UserIdentity.id == 1))"><span class="glyphicon glyphicon-plus"></span> Добавить</button>
        <h2>{{user.rank.name}} "{{user.nickname}}" {{user.firstname}}</h2>
        <br>
        <table class="table battle-log">
            <thead>
            <tr>
                <th style="vertical-align: middle">Дата</th>
                <th>Назначение вылета</th>
                <th>Полётное время</th>
                <th>Кол. сбитых</th>
                <th>Наз. цели</th>
                <th>Итог вылета</th>
                <th colspan="2">Штрафные очки</th>
                <th ng-if="((UserIdentity.isInstructor) || (UserIdentity.id == 1))"></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="log in battlelog.records">
                <td style="text-align: center">{{log.time | date:'dd.MM.yyyy'}}</td>
                <td>{{log.mission}}</td>
                <td style="text-align: center">{{log.flight_time | niceMinutes}}</td>
                <td style="text-align: center">{{log.air_targets}}</td>
                <td style="text-align: center">{{log.ground_targets}}</td>
                <td>{{log.result}}</td>
                <td style="text-align: center">{{log.fine_points}}</td>
                <td style="text-align: center">{{log.fine_points_times}}</td>
                <td ng-if="((UserIdentity.isInstructor) || (UserIdentity.id == 1))">
                    <a title="Удалить" class="btn btn-xs btn-danger pull-right" href="" style="margin-left:5px" ng-click="deleteBattleLog(log)">
                        <span class="glyphicon glyphicon-minus"></span>
                    </a>
                    <a title="Редактировать" class="btn btn-xs btn-default pull-right" ui-sref="editBattleLog({userId:user.id, logId:log.id})">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <th>Итог страницы</th>
                <th></th>
                <th style="text-align: center;vertical-align: middle">{{result.flight_time | niceMinutes}}</th>
                <th style="text-align: center;vertical-align: middle">{{result.air_targets}}</th>
                <th style="text-align: center;vertical-align: middle">{{result.ground_targets}}</th>
                <th></th>
                <th style="text-align: center;vertical-align: middle">{{result.fine_points}}</th>
                <th style="text-align: center;vertical-align: middle">{{result.fine_points_times}}</th>
                <th ng-if="((UserIdentity.id == 14) || (UserIdentity.id == 1))"></th>
            </tr>
            </tfoot>
        </table>
        <pagination ng-show="battlelog.count > 7" total-items="battlelog.count" items-per-page="itemsPerPage" page="currentPage" max-size="7"
                    class="pagination-sm" boundary-links="true" rotate="false"></pagination>
    </div>
</script>

<script type="text/ng-template" id="EditBattleLogTmpl">
    <div class="big-spinner" ng-if="!user || !battle">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="user">
        <h2>{{user.rank.name}} "{{user.nickname}}" {{user.firstname}}</h2>
        <h3 ng-if="!battle.id">Добавить новый вылет</h3>
        <h3 ng-if="battle.id">Редактировать вылет</h3>
        <div class="alert alert-danger" ng-show="battleForm.error">{{battleForm.error}}</div>
        <ng-form name="battleForm" role="form">
            <div>
                <div class="form-group input-group">
                    <label for="battle_time">Дата</label>
                    <p class="input-group"
                       ng-class="{true: 'has-error'}[(battleForm.time.$dirty && battleForm.time.$invalid)]">
                        <input type="date" placeholder="Например: 09.05.1945" name="battle_time" class="form-control"
                               ng-model="battle.time" ng-required="true"/>
                    </p>
                </div>
                <label>Задание</label>
                <div class="form-group input-group" style="width:450px"
                     ng-class="{true: 'has-error'}[(battleForm.mission.$dirty && rosterForm.mission.$invalid)]">
                    <input type="text"
                           name="mission"
                           class="form-control"
                           ng-model="battle.mission"
                           required/>
                </div>
                <label>Итог вылета</label>
                <div class="form-group input-group" style="width:450px"
                     ng-class="{true: 'has-error'}[(battleForm.result.$dirty && battleForm.result.$invalid)]">
                    <input type="text"
                           name="result"
                           style="margin-bottom: 10px"
                           class="form-control"
                           ng-model="battle.result"
                           required/>
                </div>
                <label>Время полёта (мин)</label>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(battleForm.flight_time.$dirty && battleForm.flight_time.$invalid)]">
                    <input type="text"
                           name="flight_time"
                           class="form-control"
                           ng-model="battle.flight_time"
                           required/>
                </div>
                <label>Воздущные победы</label>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(battleForm.air_targets.$dirty && battleForm.air_targets.$invalid)]">
                    <input type="text"
                           name="air_targets"
                           class="form-control"
                           ng-model="battle.air_targets"
                           required/>
                </div>
                <label>Наземные цели</label>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(battleForm.ground_targets.$dirty && battleForm.ground_targets.$invalid)]">
                    <input type="text"
                           name="ground_targets"
                           class="form-control"
                           ng-model="battle.ground_targets"
                           required/>
                </div>
                <label>Штрафные очки</label>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(battleForm.fine_points.$dirty && battleForm.fine_points.$invalid)]">
                    <input type="text"
                           name="fine_points"
                           class="form-control"
                           ng-model="battle.fine_points"
                           required/>
                </div>
                <label>Отрабртанные штрафные очки</label>
                <div class="form-group input-group"
                     ng-class="{true: 'has-error'}[(battleForm.fine_points_times.$dirty && battleForm.fine_points_times.$invalid)]">
                    <input type="text"
                           name="fine_points_times"
                           class="form-control"
                           ng-model="battle.fine_points_times"
                           required/>
                </div>
        </ng-form>
        <div class="alert alert-danger" ng-show="battleForm.error">{{battleForm.error}}</div>
        <div>
            <p class="well">
                <button type="button" ng-click="save()"
                        ng-disabled="battleForm.isSubmitting" class="btn btn-primary">Сохранить</button>
            </p>
        </div>
    </div>
</script>

<script type="text/ng-template" id="userMarksTmpl">
    <div class="big-spinner" ng-if="!user.id">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="user.id">
        <h2>Оценочный лист пилота {{user.nickname}}</h2>
        <ul class="nav nav-tabs" role="tablist">
            <li ng-class="{'active':(tabs.activeTab=='fighter')}" ng-if="user.programs.fighter"><a href="" ng-click="tabs.activeTab='fighter'">Истребитель</a></li>
            <li ng-class="{'active':(tabs.activeTab=='bomber')}" ng-if="user.programs.bomber"><a href="" ng-click="tabs.activeTab='bomber'">Бомбардировщик</a></li>
            <li ng-class="{'active':(tabs.activeTab=='shturmovik')}" ng-if="user.programs.shturmovik"><a href="" ng-click="tabs.activeTab='shturmovik'">Штурмовик</a></li>
        </ul>
        <div ng-repeat="program in user.programs"  ng-if="tabs.activeTab==program.id">
            <div class="panel panel-{{course.complete ? 'success' : 'primary'}}" ng-repeat="course in program.courses">
                <div class="panel-heading">{{course.name}}<span class="label label-danger pull-right"
                                                                style="font-size: 14px;">{{course.average | number : 1}}</span>
                </div>
                <table class="table">
                    <tr ng-repeat="subject in course.subjects">
                        <td bind-compiled-html="subject.name"></td>
                        <td style="width:80px;text-align: right;vertical-align: middle">
                            <button type="button" ng-if="UserIdentity.isInstructor"
                                    ng-click="mark(subject.id,course.id)"
                                    class="btn btn-xs btn-default"><span
                                    class="glyphicon glyphicon-pencil"></span></button>
                        <span class="label label-primary" style="font-size: 14px;"
                              ng-show="user.marks[course.id][subject.id]">{{user.marks[course.id][subject.id].mark}}</span>
                        </td>
                    </tr>
                </table>
                <button type="button" ng-click="promote(course.id)"
                        ng-if="(course.rank_order == user.rank_order) && course.complete" class="btn btn-sm btn-success"
                        style="width: 100%"><span class="glyphicon glyphicon-hand-down"></span> Перевести на следующий
                    курс
                </button>
            </div>
        </div>
    </div>
</script>


<script type="text/ng-template" id="AwardsTmpl">
    <h2>Награды</h2>
    <div style="min-height: 550px">
        <div class="big-spinner" ng-if="isLoading">
            <div class="spinner-icon"></div>
        </div>
        <div class="well">
            <a class="btn btn-success" ui-sref="editaward({awardId:award.id})">Добавить награду</a>
        </div>
        <div class="" ng-repeat="award in awards" style="width: 45%;float:left;margin-left:10px;margin-right:10px">
            <a style="height: 90px;margin-bottom: 10px" class="thumbnail isRelative" ui-sref="editaward({awardId:award.id})"
               title="{{award.name}}">
                <img style="width:80px; height:80px; float:left;background: url('/img/awards/{{award.id}}.png') center center no-repeat"/>
                <div class="caption" style="padding-top: 20px;padding-left:95px">{{award.name}}</div>
            </a>
        </div>
    </div>
</script>


<script type="text/ng-template" id="AwardTmpl">
    <div class="big-spinner" ng-if="!award">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="award">
        <h2>{{award.id ? 'Редактировать' : 'Добавить'}} награду</h2>
        <br>
        <div class="alert alert-danger" ng-show="awardForm.error">{{awardForm.error}}</div>

        <div style="margin-right:10px;position: relative;float:right">
            <div style="width: 200px;height:100px;display:block;background:url('{{award.temp_image ? '/img/temp/' + award.temp_image : ('/img/awards/'+award.id + '.png')}}') center center no-repeat"></div>
            <div award-upload-box></div>
        </div>

        <ng-form name="awardForm" role="form">
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(awardForm.name.$dirty && awardForm.name.$invalid)]">
                <label>Название</label>
                <input type="text"
                       style="margin-bottom: 10px" name="name" class="form-control" ng-model="award.name"
                       required/>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(awardForm.sub_name.$dirty && awardForm.sub_name.$invalid)]">
                <label>Название в творительном падеже</label>
                <input type="text"
                       style="margin-bottom: 10px" name="sub_name" class="form-control" ng-model="award.sub_name"
                       required/>
            </div>
            <label>Можно иметь только одну</label>
            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="award.only_one_allowed" btn-radio="'1'"
                            required>
                        Да
                    </button>
                    <button type="button" class="btn btn-default" ng-model="award.only_one_allowed" btn-radio="'0'">
                        Нет
                    </button>
                </p>
            </div>
            <div class="form-group input-group">
                <label>Расположение на форме по умолчанию</label>
                <div style="width: 100px;"> Сверху:
                    <input type="text"
                           style="margin-bottom: 10px;" name="top" class="form-control" ng-model="award.top"
                           required/>
                </div>
                <div style="width: 100px;">
                    Слева:
                    <input type="text"
                           style="margin-bottom: 10px;" name="left" class="form-control" ng-model="award.left"
                           required/>
                </div>
            </div>
            <div class="form-group input-group">
                <label>Замещает собой:</label>
                <div>
                <select data-placeholder="Выберите Награду"
                        ui-select2="awardSelect2Options"
                        class="form-control"
                        style="width: 512px;vertical-align: top;"
                        ng-model="award.award_replace_id">
                    <option ng-repeat="award in awards" value="{{award.id}}">{{award.name}}</option>
                </select>
                </div>
            </div>
        </ng-form>

        <div class="alert alert-danger" ng-show="awardForm.error">{{awardForm.error}}</div>
        <div>
            <p class="well">
                <button type="button" ng-click="save()"
                        ng-disabled="awardForm.isSubmitting" class="btn btn-primary">Сохранить</button>
            </p>
        </div>
    </div>
</script>

<script type="text/ng-template" id="awardUploadBoxTemplate">
    <div ng-if="!uploadItem.isUploading" style="max-width: 200px;overflow: hidden">
        <input type="file" ng-file-select style="height: 22px;max-height: 22px">

        <div>
            <a href="" class="btn btn-xs btn-default" style="width: 100%;margin-top: -45px;pointer-events:none">Поменять
                изображение</a>
        </div>
    </div>
    <div class="big-spinner" ng-if="uploadItem.isUploading">
        <div class="spinner-icon"></div>
        <div class="text">{{uploadItem.progress}}%</div>
    </div>
</script>



<script type="text/ng-template" id="markDialogTmpl">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><span class="glyphicon glyphicon-plane"></span>
                <span>Поставить оценку</span></h4>
        </div>
        <div class="modal-body">
            <ng-form name="markDialog" novalidate role="form">
                <div class="btn-group btn-group-lg btn-group-justified">
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'1'" class="btn btn-primary">1
                        </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'2'" class="btn btn-primary">2
                        </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'3'" class="btn btn-primary">3
                        </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'4'" class="btn btn-primary">4
                        </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'5'" class="btn btn-primary">5
                        </button>
                    </div>
                </div>
            </ng-form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-disabled="userMark.isSubmitting" ng-click="cancel()">
                Отмена
            </button>
            <button type="button" class="btn btn-primary" ng-disabled="userMark.isSubmitting" ng-click="saveMark()">
                Сохранить
            </button>
        </div>
    </div>
</script>

<script type="text/ng-template" id="eventDialogTmpl">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><span class="glyphicon glyphicon-plane"></span>
                <span>Редактировать запись книжки</span></h4>
        </div>
        <div class="modal-body">
            <ng-form name="eventDialog" novalidate role="form">
                <textarea style="width: 100%;resize: none;" rows="5" ng-model="event.text"
                          class="form-control" placeholder="Событие"></textarea>
                <br>
                <input type="date" ng-model="event.dateString" class="form-control" placeholder="Дата">
            </ng-form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-disabled="event.isSubmitting" ng-click="cancel()">
                Отмена
            </button>
            <button type="button" class="btn btn-primary" ng-disabled="event.isSubmitting" ng-click="saveEvent()">
                Сохранить
            </button>
        </div>
    </div>
</script>

<script type="text/ng-template" id="promoteDialogTmpl">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><span class="glyphicon glyphicon-plane"></span>
                <span>Окончание школы пилотов</span></h4>
        </div>
        <div class="modal-body">
            Курсант <span style="color:#f00" ng-if="is_clanner">(в чужом полку)</span> заканчивает школу пилотов, куда
            его определить?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-click="cancel()">
                Отмена
            </button>
            <button type="button" class="btn btn-primary" ng-click="promote(false)">
                Выпускник
            </button>
            <button type="button" class="btn btn-danger" ng-click="promote(true)">
                Офицер
            </button>
        </div>
    </div>
</script>

<script type="text/ng-template" id="rejectDialogTmpl">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><span class="glyphicon glyphicon-plane"></span>
                <span>Укажите причину {{reject.header}}</span></h4>
        </div>
        <div class="modal-body">
            <ng-form name="rejectDialog" novalidate role="form">
                <textarea style="width: 100%;resize: none;" rows="5" ng-model="reject.text"
                          class="form-control" placeholder="Причина"></textarea>
            </ng-form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-disabled="reject.isSubmitting" ng-click="cancel()">
                Отмена
            </button>
            <button type="button" class="btn btn-primary" ng-disabled="reject.isSubmitting" ng-click="saveReject()" ng-bind="reject.buttonText"></button>
        </div>
    </div>
</script>

<script type="text/ng-template" id="acquitDialogTmpl">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><span class="glyphicon glyphicon-plane"></span>
                <span>Оправдание пилота "{{pilot.nickname}}"</span></h4>
        </div>
        <div class="modal-body">
        <ng-form name="acquitForm" role="form">
            <div>
                <label>Даты отпуска</label>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                            <span class="input-group-addon">Начало</span>
                            <input type="date" class="form-control" required ng-model="acquit.dateFrom">
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                            <span class="input-group-addon">Конец</span>
                            <input type="date" class="form-control" required ng-model="acquit.dateTo">
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- /.row -->
            </div>
            <br>
            <label>Отмазка</label>
            <textarea style="width: 100%;resize: none;" ng-model="acquit.reason" rows="5" class="form-control" required></textarea>
        </ng-form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-disabled="acquitForm.isSubmitting" ng-click="cancel()">
                Отмена
            </button>
            <button type="button" class="btn btn-primary" ng-disabled="(acquitForm.$dirty && acquitForm.$invalid) || acquitForm.$pristine || acquitForm.isSubmitting" ng-click="saveAcquit()">
                Оправдать
            </button>
        </div>
    </div>
</script>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-57603782-1', 'auto');
    if (!UserLoginData.isGuest && UserLoginData.id) {
        ga('set', '&uid', UserLoginData.id);
    }

</script>
<script type="text/javascript">
    window.___gcfg = {lang: 'ru'};
    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/platform.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
</script>
</body>
</html>