<?
/* @var $this Controller */
/* @var $content String */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Школа виртуального пилотирования LuftwaffeSchule</title>
    <script type="text/javascript">var UserLoginData = <?=json_encode(Yii::app()->user->privateAttributes)?>;</script>
    <script src="/scripts.js"></script>
    <script src="http://lws.exirous.com:3000/socket.io/socket.io.js"></script>
    <link rel="stylesheet" href="/style.css" type="text/css">
    <!--[if lt IE 8]>
    <script type="text/javascript">
        alert('Ваш броузер не поддерживается, пожалуйста обновите! :)');
    </script>
    <![endif]-->
</head>
<body ng-app="app">
<div id="main_content">
    <div class="main_wrapper" ng-controller="AppCtrl">

        <div class="notification_bank">
            <div ng-repeat="notification in notifications" class="alert alert-{{notification.type}} alert-notification shadowed" ng-click="closeNotification($index)">
                <div bind-compiled-html="notification.text"></div>
            </div>
        </div>
        <div class="photostack" id="main_header">
            <img ng-repeat="image in headerImages" class="header-image" ng-src="{{image}}">
        </div>
        <a ui-sref="news" id="logo"></a>
        <table class="contentTable" cellpadding=0 cellspacing=0>
            <tbody>
            <tr>
                <td class="ml"></td>
                <td class="mm">
                    <div ng-controller="BirthdayViewCtrl" style="padding:0">
                        <div ng-if="birthdays.length">
                            <div class="main_menu">
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
                            <div class="main_menu">
                                <a href="">Новобранцы</a>
                            </div>
                            <div class="left_content">
                                <a ui-sref="rosterUser({userId:pilot.id})" ng-repeat="pilot in roster"
                                   class="list-group-item">{{pilot.nickname}} ({{pilot.firstname}})</a>
                            </div>
                        </div>
                    </div>
                    <div class="main_menu"><a href="ts3server://lws.exirous.com/?nickname={{UserIdentity.fullname}}">TeamSpeak</a>
                    </div>
                    <div class="left_content ts_channels">
                        <ul ng-controller="TSViewCtrl" style="padding:0">
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
                    <div class="main_menu"><a href="">Социалки</a>
                    </div>
                    <div class="left_content">
                        <div class="g-page" data-width="215" data-href="//plus.google.com/u/0/108457946413274910426" data-showtagline="false" data-showcoverphoto="false" data-rel="publisher"></div>
                    </div>
                </td>
                <td class="mr"></td>
                <td class="ml"></td>
                <td class="mm">
                    <div class="main_menu">
                        <a ui-sref="news">Новости</a>
                        <a ui-sref="orders" ng-if="!UserIdentity.canMakeOrders && !UserIdentity.isGuest" >Приказы</a>
                        <span class="dropdown dropdown-hover" ng-if="UserIdentity.canMakeOrders">
                        <a href="">Приказы</a>
                        <ul class="dropdown-menu" style="top: 20px;left: -7px;">
                            <li><a ui-sref="orders">Архив</a></li>
                            <li class="divider"></li>
                            <li><a ui-sref="makeorder">Отдать приказ</a></li>
                            <li><a ui-sref="makenews">Добавить новость</a></li>
                        </ul>
                        </span>
                        <span class="dropdown dropdown-hover">
                        <a href="">Штаб</a>
                        <ul class="dropdown-menu" style="top: 20px;left: -7px;">
                            <li><a ui-sref="texts({id:1})">Устав</a></li>
                            <li><a ui-sref="texts({id:2})">Приложения к уставу</a></li>
                            <li role="presentation" ng-if="UserIdentity.isInstructor" class="divider"></li>
                            <li><a ui-sref="materials({slug:'instructor_training'})" ng-if="UserIdentity.isInstructor">Настваление инструктору</a></li>
                        </ul>
                        </span>
                        <a ui-sref="roster" ng-if="UserIdentity.isGuest">Вступить в школу</a>
                        <span class="dropdown dropdown-hover" ng-if="!UserIdentity.isGuest">
                        <a href="">Учебный класс</a>
                        <ul class="dropdown-menu" style="top: 20px;left: -7px;">
                            <li><a>Расписание занятий</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ui-sref="materials({slug:'flight_basics'})">Наставление по лётной подготовке</a></li>
                            <li><a ui-sref="materials({slug:'fighter_course'})">Программа обучения истребителей</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ui-sref="materials({slug:'bomber_basics'})">Наставление по лётной подготовке бомбардировщиков</a></li>
                            <li><a ui-sref="materials({slug:'bomber_course'})">Программа обучения бомбардировщиков</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ui-sref="materials({slug:'war_basics'})">Боевой устав</a></li>
                            <!--<li role="presentation" class="divider"></li>
                            <li><a ui-sref="texts({id:'6'})">Техническая эксплуатационная часть</a></li>-->
                        </ul>
                        </span>
                        <a ui-sref="pilots">Казарма</a>
                        <a ui-sref="flood" ng-if="!UserIdentity.isGuest">Курилка</a>
                        <a ui-sref="texts({id:5})">О школе</a>
                        <a href="" style="float:right" ng-click="login()" ng-if="UserIdentity.isGuest">Вход</a>
                        <span ng-if="!UserIdentity.isGuest" class="dropdown dropdown-hover" style="float:right">
                        <a href="">{{UserIdentity.nickname}} <span class="glyphicon glyphicon-user"></span></a>
                        <ul class="dropdown-menu" style="top: 27px;left: -10px;">
                            <li><a ui-sref="user({userId:UserIdentity.id})">Посмотреть профиль</a></li>
                            <li><a ui-sref="reportvacation">Рапорт на отпуск</a></li>
                            <li><a ui-sref="messenger">Личные сообщения</a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a ng-click="logout()" href="">Выход</a></li>
                        </ul>
                        </span>
                    </div>
                    <div class="content" style="min-height:400px">
                        <div class="center_content" ui-view>
                        </div>
                    </div>
                </td>
                <td class="mr"></td>
            </tr>
            <tr>
                <td class="bl"></td>
                <td class="bm"></td>
                <td class="br"></td>
                <td class="bl"></td>
                <td class="bm"></td>
                <td class="br"></td>
            </tr>
            </tbody>
        </table>
    </div>
    <a href="https://plus.google.com/108457946413274910426" rel="publisher"></a>
</div>

<script type="text/ng-template" id="NewsTmpl">
    <h2>Объявления</h2>
    <div class="big-spinner" ng-if="!news.length">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="newsRec in news" class="news-row panel panel-default">
        <div class="panel-heading">{{newsRec.title}}</div>
        <div class="panel-body" ng-bind-html="newsRec.text | to_trusted"></div>
        <div class="panel-footer">
            <span>{{newsRec.time}}</span> <img
                ng-src="/img/users/{{newsRec.issuer.img_src ? newsRec.issuer.id+'_'+newsRec.issuer.img_src+'.jpg' : (newsRec.issuer.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{newsRec.issuer.id}}">{{newsRec.issuer.nickname}}</a>
        </div>
    </div>
</script>

<script type="text/ng-template" id="FloodTmpl">
    <h2>Курилка</h2>
    <div class="well">
        <a class="btn btn-success" ui-sref="newtopic()">Добавить тему</a>
    </div>
    <div class="big-spinner" ng-if="!topics.length">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="topic in topics | orderBy:'lastMessageTime':'true'">
        <div class="forumTopicHeader">
            <img
                ng-src="/img/users/{{topic.author.img_src ? topic.author.id+'_'+topic.author.img_src+'.jpg' : (topic.author.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                style=""><a href="#/user/view/{{topic.author.id}}">{{topic.author.nickname}}</a>
        </div>
        <div class="news-row panel panel-primary">
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
    <div ng-repeat="conversation in conversations | orderBy:'lastMessageTime':'true'">
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
    <div ng-repeat="message in conversation.messages | orderBy:'time'" class="media news-row" ng-class="{'alert-success':message.sender.id!=UserIdentity.id && !message.is_read}" style="position: relative">
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
        <div class="panel-heading">{{material.title}}<a ng-if="UserIdentity.canMakeOrders" title="Редактировать"
                                                        class="btn btn-xs btn-default pull-right"
                                                        ui-sref="editmaterial({materialId:material.id, slug:subject.slug})"><span
                    class="glyphicon glyphicon-pencil"></span></a></div>
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

<script type="text/ng-template" id="OrdersTmpl">
    <h2>Приказы</h2>
    <div class="big-spinner" ng-if="!news.length">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="newsRec in news" class="news-row panel panel-default">
        <div class="panel-heading">{{newsRec.title}}</div>
        <div class="panel-body" bind-compiled-html="newsRec.text"></div>
        <div class="panel-footer">
            <span>{{newsRec.time}}</span> <img
                ng-src="/img/users/{{newsRec.issuer.img_src ? newsRec.issuer.id+'_'+newsRec.issuer.img_src+'.jpg' : (newsRec.issuer.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{newsRec.issuer.id}}">{{newsRec.issuer.nickname}}</a>
        </div>
    </div>
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
        <button type="button" ui-sref="editUser({userId:user.id})" class="btn btn-sm btn-default pull-right" ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)"><span class="glyphicon glyphicon-pencil"></span></button>
        <!--<button type="button" ng-click="sync()" class="btn btn-sm btn-default pull-right" ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)"><span class="glyphicon glyphicon-refresh"></span></button>-->
        <h2>{{user.rank.name}} "{{user.nickname}}" {{user.firstname}}</h2>
        <br>
        <table style="width: 100%">
            <tr>
                <td style="height: 210px;width: 210px">
                    <div style="margin-right:10px;position: relative">
                        <img ng-src="/img/users/{{user.img_src ? user.id+'_'+user.img_src+'.jpg' : (user.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}"
                             style="width: 200px;display:block">
                        <div ng-if="user.activeVacation" class="vacation" title="{{user.activeVacation.reason}}, с {{user.activeVacation.date_from | date : 'dd.MM.yyyy'}} по {{user.activeVacation.date_to | date : 'dd.MM.yyyy'}}">В Отпуске!</div>
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
                        <tr ng-if="UserIdentity.isInstructor || UserIdentity.id == user.id">
                            <th>Оценки</th>
                            <td><a ui-sref="userMarks({userId:user.id})">Посмотреть</a></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td ng-if="user.rank" rowspan="2" style="width: 380px;padding-left: 5px;max-width: 380px;">
                    <div class="uniform {{user.is_clanner ? 'clanner' : ''}}" dnd-container="true">
                        <div class="unform_rank"
                             style="background: url(/img/uniform/{{user.is_clanner ? 'clanner/' : ''}}{{user.rank.id}}.png) no-repeat"
                             title="{{user.rank.name}}">
                        </div>
                        <img title="{{medal.name}}" ng-repeat="medal in user.medals"
                             style="top:{{medal.top}}px;left:{{medal.left}}px;" ng-src="/img/awards/{{medal.id}}.png"
                             dnd-draggable="UserIdentity.id == 1 || UserIdentity.id == 14"
                             dnd-on-dragend="medalDrop($element, medal)">
                        <img ng-if="user.instructor" title="{{user.instructor.name}}"
                             class="identifier_{{user.instructor.id}}"
                             ng-src="/img/identifiers/ident_{{user.instructor.id}}.png">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-right:10px">
                    <div class="panel panel-default">
                        <div class="panel-heading"><span>Книжка пилота</span>
                            <button type="button" ng-if="UserIdentity.canMakeOrders" ng-click="addEvent(user.id)"
                                    class="btn btn-xs btn-success pull-right">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </div>
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
                           class="form-control" ng-model="user.reason" required/>
                </p>
            </div>

            <label>Состоите-ли в другом СКВАДе, Клане или Полку?</label>

            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.in_squad" btn-radio="true" required>
                        Да
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.in_squad" btn-radio="false">
                        Нет
                    </button>
                </p>
            </div>
            <div class="form-group input-group" ng-if="user.in_squad">
                <label>В каком?</label>

                <div>
                    <p>
                        <input type="text" placeholder="Например: Lws, Heer, DerAdler, и.т.д" id="user_squad"
                               class="form-control" ng-model="user.squad"/>
                    </p>
                </div>
            </div>
            <label>Наличие в ангаре самолетов Bf-109E-3 и/или Р-36G для истребителей</label>

            <div class="form-group input-group">
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.craft.bf109" btn-checkbox>Bf-109E-3
                        (Эмиль)
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.craft.p36g" btn-checkbox>P-36G Hawk
                    </button>
                </p>
            </div>
            <label>Предпочитаете технику:</label>

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
                <input type="text"
                       style="margin-bottom: 10px" name="nickname" class="form-control" ng-model="user.nickname"
                       required/>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.firstname.$dirty && rosterForm.firstname.$invalid)]">
                <label>Ваше имя?</label>
                <input type="text"
                       name="firstname"
                       style="margin-bottom: 10px"
                       placeholder="Например: Женя, Паша, Виталий, и.т.д" class="form-control"
                       ng-model="user.firstname"
                       required/>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.email.$dirty && rosterForm.email.$invalid)]">
                <label>Укажите вашу электронную почту (будет использоватся в качестве Логина)</label>
                <input type="email"
                       style="margin-bottom: 10px"
                       placeholder="Укажите свою электроннную почту"
                       class="form-control"
                       name="email"
                       id="email"
                       ng-model="user.private.email"
                       required>
            </div>
            <div class="form-group input-group"
                 ng-class="{true: 'has-error'}[(rosterForm.password.$dirty && rosterForm.password.$invalid)]">
                <label>Укажите пароль который будет использоватся для входа в систему</label>
                <input type="password"
                       style="margin-bottom: 10px"
                       placeholder="Укажите новый пароль"
                       class="form-control"
                       name="password"
                       id="password"
                       ng-model="user.private.password"
                       required>
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
                        </p>
                    </div>
                </div>
                <div class="form-group input-group">
                    <label>Тимспик</label><br>
                    <select ng-model="user.ts_id" style="width:350px"
                            required
                            data-placeholder="Привязка к TeamSpeak"
                            ui-select2>
                        <option></option>
                        <option ng-repeat="option in user.possibleUsers" value="{{option.uid}}">[{{$index+1}}] {{option.name}}</option>
                    </select>
                </div>
        </ng-form>
        <div class="alert alert-danger" ng-show="userForm.error">{{userForm.error}}</div>
        <div>
            <p class="well">
                <button type="button" ng-click="save()"
                        ng-disabled="userForm.isSubmitting || (!user.qualifications.bomber && !user.qualifications.fighter)" class="btn btn-primary">Сохранить</button>
            </p>
        </div>
    </div>
</script>

<script type="text/ng-template" id="AfterRosterTmpl">
    <h2>Заявка отправлена</h2>
    <br>
    <div class="alert alert-warning" style="font-size:20px">
        Что-бы вас приняли, пожалуйста зайдите на наш <a href="ts3server://lws.exirous.com/?nickname={{UserIdentity.fullname}}">сервер
            TeamSpeak</a> для собеседования и ожидайте в приёмной. Кто-то из инструкторов к вам обязательно подойдёт.
        <br>
        TeamSpeak можно скачать пройдя по <a href="http://www.teamspeak.com/?page=downloads" target="_blank">этой</a>
        ссылке.
    </div>
</script>


<script type="text/ng-template" id="BarracksTmpl">
    <h2>Казарма<span ng-show="pilots.length"> ({{pilots.length}})</span></h2>
    <div class="well well-sm">
        <div class="input-group">
            <span class="input-group-addon glyphicon glyphicon glyphicon-search" style="top:0"></span>
            <input type="text" class="form-control" placeholder="Найти по имени или никнейму" ng-model="filters.name" style="width:740px">
                <p class="btn-group" style="float:right">
                    <button type="button" class="btn btn-default" ng-model="dataSize" btn-radio="2">
                        <span class="glyphicon glyphicon-list"></span>
                    </button>
                    <button type="button" class="btn btn-default" ng-model="dataSize" btn-radio="1">
                        <span class="glyphicon glyphicon-th-large"></span>
                    </button>
                </p>
        </div>
    </div>
    <div style="min-height: 550px">
        <div class="big-spinner" ng-if="isLoading">
            <div class="spinner-icon"></div>
        </div>
        <div ng-if="(dataSize == 2)" class="" ng-repeat="pilot in pilots">
            <a style="height: 90px;margin-bottom: 10px" class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:80px; height:80px; float:left">
                <div class="floating_rank small"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor || pilot.isBomber" class="floating_rank small" style="left:23px"><img
                        ng-src="/img/groups/{{pilot.instructor ? pilot.instructor : 36}}.png"></div>
                <div class="caption" style="padding-left: 90px;padding-top:18px">
                    <b>{{pilot.nickname}}</b><br><span>{{pilot.firstname}}</span>
                </div>
            </a>
        </div>
        <div ng-if="(dataSize == 1)" class="col-sm-6 col-md-3 user-cell" ng-repeat="pilot in pilots">
            <a class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : (pilot.is_clanner ? 'no_image_clanner.png' : 'no_image.png')}}" alt=""
                     style="width:182px; height:182px">
                <div ng-if="pilot.activeVacation" class="vacation" title="{{pilot.activeVacation.reason}}, c {{pilot.activeVacation.date_from | date : 'dd.MM.yyyy'}} по {{pilot.activeVacation.date_to | date : 'dd.MM.yyyy'}}">В Отпуске!</div>
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
</script>

<script type="text/ng-template" id="RosterUserTmpl">
    <div class="big-spinner" ng-if="!pilot.id">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="pilot.id">
        <h2>Заявка на вступление от {{pilot.nickname}} ({{pilot.firstname}})</h2>
        <br>
        <a href="http://warthunder.ru/ru/community/userinfo/?nick={{pilot.nickname}}"><img src="http://warthunder.ru/favicon.ico" height="40px">Профиль WT</a><br>
        <label>Дата рождения:</label><br>
        <span>{{pilot.roster.birthdate | date : "dd.MM.yyyy"}}</span>
        (<span>{{pilot.roster.birthdate | age}}</span> лет)<br>
        <label>Готовность к обучению:</label><br>
        <span>{{pilot.roster.scale}}</span><br>
        <label>Попал в школу посредством:</label><br>
        <span>{{pilot.roster.reason}}</span><br>
        <label>Состоит в скваде:</label><br>
        <span ng-bind="pilot.roster.squad ? pilot.roster.squad : 'Нет'"></span><br>
        <label>Наличие в ангаре самолетов Bf-109E-3 и/или Р-36G для истребителей:</label><br>
        <span ng-show="pilot.roster.craft.bf109">Bf 109E-3</span>
        <span ng-show="pilot.roster.craft.p36g">P-36G Hawk</span>
        <br>
        <label>Предпочитает технику:</label><br>
        <span>{{pilot.roster.nation}}</span><br>
        <label>Время онлайна:</label><br>
        <span>C </span><span>{{pilot.roster.onlineFrom | date : "HH:mm"}}</span><span> По </span><span>{{pilot.roster.onlineTo | date : "HH:mm"}}</span>
        <br><br>

        <div ng-show="pilot.rank" class="alert alert-success">Пилот принят</div>
        <div ng-form="rosterForm" ng-show="!pilot.rank">
            <p class="well">
                <select ng-model="rosterForm.tsId" style="width:350px"
                        required
                        data-placeholder="Привязка к TeamSpeak"
                        ui-select2>
                    <option></option>
                    <option ng-repeat="option in pilot.possibleUsers" value="{{option.uid}}">{{option.name}}</option>
                </select>&nbsp;
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
            <div style="display: inline-block;width: 435px;vertical-align: top;">
                <select data-placeholder="Выберите пилотов"
                        multiple
                        ui-select2="pilotSelect2Options"
                        style="width: 435px;margin-bottom: 6px"
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
                style="resize: none;display:inline-block;width: 49%;padding:5px;border:1px solid #aaa;min-height:114px;background: #eee;color:#555"
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
                        style="width: 535px;vertical-align: top;"
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
    <h2>Добавить новость</h2>
    <ng-form name="newsForm" novalidate role="form">
        <div ng-if="newsRecord.newsAdded" class="alert alert-success">Новость успешно добавленна</div>
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
                        class="btn btn-primary">Опубликовать
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

<script type="text/ng-template" id="userMarksTmpl">
    <div class="big-spinner" ng-if="!user.id">
        <div class="spinner-icon"></div>
    </div>
    <div ng-show="user.id">
        <h2>Оценочный лист пилота {{user.nickname}}</h2>
        <ul class="nav nav-tabs" role="tablist">
            <li ng-class="{'active':(tabs.activeTab=='fighter')}" ng-if="user.programs.fighter"><a href="" ng-click="tabs.activeTab='fighter'">Истребитель</a></li>
            <li ng-class="{'active':(tabs.activeTab=='bomber')}" ng-if="user.programs.bomber"><a href="" ng-click="tabs.activeTab='bomber'">Бомбардировщик</a></li>
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
                <span>Укажите причину отклонения заявки</span></h4>
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
            <button type="button" class="btn btn-primary" ng-disabled="reject.isSubmitting" ng-click="saveReject()">
                Отклонить заявку
            </button>
        </div>
    </div>
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