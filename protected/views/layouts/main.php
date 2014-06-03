<?
/* @var $this Controller */
/* @var $content String */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title><?= $this->pageTitle ?></title>
    <!--<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.12/angular.min.js"></script>-->
    <script type="text/javascript">var UserLoginData = <?=json_encode(Yii::app()->user->privateAttributes)?>;</script>
    <script src="/scripts/jquery.js"></script>
    <!--<script src="/scripts/chosen.jquery.js"></script>-->
    <script src="/scripts/lib/select2.min.js"></script>
    <script src="/scripts/lib/angular.min.js"></script>
    <script src="/scripts/lib/angular-ui-router.js"></script>
    <script src="/scripts/lib/angular-resource.js"></script>
    <script src="/scripts/lib/angular-sanitize.js"></script>
    <script src="/scripts/lib/animate.js"></script>
    <script src="/scripts/lib/select2.js"></script>
    <script src="/scripts/lib/ui-bootstrap-tpls.js"></script>
    <script src="/scripts/lib/statehelper.js"></script>
    <script src="/scripts/lib/dialogs.js"></script>
    <script src="/scripts/app.js"></script>
    <script src="/scripts/services/services.js"></script>
    <script src="/scripts/filters/filters.js"></script>
    <script src="/scripts/directives/directives.js"></script>
    <script src="/scripts/controllers/controllers.js"></script>
    <script src="/scripts/lib/socket.io.js"></script>
    <script src="/scripts/loading-bar.js"></script>
    <script src="/scripts/lib/angular-file-upload.js"></script>
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.css" type="text/css">
    <!--<link rel="stylesheet" href="/css/bootstrap.css" type="text/css">-->
    <link rel="stylesheet" href="/css/style.css" type="text/css">
    <link rel="stylesheet" href="/css/select2.css" type="text/css">
    <link rel="stylesheet" href="/css/select2-bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/css/loading-bar.css" type="text/css">

    <link rel="stylesheet" href="/scripts/sceditor/minified/themes/default.min.css" type="text/css" media="all"/>
    <script type="text/javascript" src="/scripts/sceditor/minified/jquery.sceditor.bbcode.min.js"></script>

    <!--[if lt IE 8]>
    <script type="text/javascript">
        alert('Ваш броузер не поддерживается, пожалуйста обновите! :)');
    </script>
    <![endif]-->
</head>
<body ng-app="app">
<div id="main_content">
    <div class="main_wrapper" ng-controller="AppCtrl">
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
                                   class="list-group-item">{{pilot.nickname}} ({{pilot.birthday | date : "dd.MM"}})</a>
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
                            <li ng-repeat="channel in tree" ng-include="'TreeItemTmpl'">
                            </li>
                            <li ng-show="!tree.length">
                                Никого нету :)
                            </li>
                        </ul>
                    </div>
                </td>
                <td class="mr"></td>
                <td class="ml"></td>
                <td class="mm">
                    <div class="main_menu">
                        <a ui-sref="news">Новости</a>
                        <a ui-sref="orders" ng-if="!UserIdentity.canMakeOrders">Приказы</a>
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
                            <li><a ui-sref="texts({id:3})" ng-if="UserIdentity.canMakeOrders">Настваление
                                    инструктору</a></li>
                        </ul>
                        </span>
                        <a ui-sref="roster" ng-if="UserIdentity.isGuest">Вступить в школу</a>
                        <span class="dropdown dropdown-hover" ng-if="!UserIdentity.isGuest">
                        <a href="">Учебный класс</a>
                        <ul class="dropdown-menu" style="top: 20px;left: -7px;">
                            <li><a ui-sref="school">Наставление по лётной подготовке</a></li>
                            <li><a>Расписание занятий</a></li>
                            <li><a>Программа курсового обучения</a></li>
                        </ul>
                        </span>


                        <a ui-sref="pilots">Казарма</a>
                        <a ui-sref="flood">Курилка</a>

                        <a href="" style="float:right" ng-click="login()" ng-if="UserIdentity.isGuest">Вход</a>

                        <span ng-if="!UserIdentity.isGuest" class="dropdown dropdown-hover" style="float:right">
                        <a href="">{{UserIdentity.nickname}} <span class="glyphicon glyphicon-user"></span></a>
                        <ul class="dropdown-menu" style="top: 20px;left: -7px;">
                            <li><a ng-click="vacation()" href="">Рапорт на отпуск</a></li>
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
</div>

<script type="text/ng-template" id="NewsTmpl">
    <h2>Объявления</h2>
    <div class="big-spinner" ng-if="!news.length">
        <div class="spinner-icon"></div>
    </div>
    <div ng-repeat="newsRec in news" class="news-row panel panel-default">
        <div class="panel-heading">{{newsRec.title}}</div>
        <div class="panel-body" ng-bind-html="newsRec.text"></div>
        <div class="panel-footer">
            <span>{{newsRec.time}}</span> <img
                ng-src="/img/users/{{newsRec.issuer.img_src ? newsRec.issuer.id+'_'+newsRec.issuer.img_src+'.jpg' : 'no_image.png'}}"
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
                ng-src="/img/users/{{topic.author.img_src ? topic.author.id+'_'+topic.author.img_src+'.jpg' : 'no_image.png'}}"
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
                 ng-src="/img/users/{{topic.lastMessage.author.img_src ? topic.author.id+'_'+topic.lastMessage.author.img_src+'.jpg' : 'no_image.png'}}"
                 style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{topic.lastMessage.author.id}}">{{topic.author.nickname}}</a> <span>{{topic.lastMessage.time | date:'dd.MM.yyyy, hh:mm'}}</span>
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
    <textarea sceditor="1" style="width: 100%;resize: none" rows="10" required></textarea>
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
                 ng-src="/img/users/{{message.author.img_src ? message.author.id+'_'+message.author.img_src+'.jpg' : 'no_image.png'}}">
        </a>
        <div class="media-body">
            <span class="pull-right">{{message.time | date:'dd.MM.yyyy, hh:mm'}}</span>
            <h4 class="media-heading with-underline">{{message.author.nickname}}</h4>
            <div ng-bind-html="message.text"></div>
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
    <h2>Наставление по лётной подготовке</h2>
    <div class="well" ng-if="UserIdentity.canMakeOrders">
        <a class="btn btn-success" ui-sref="editmaterial(0)">Добавить новый материал <span
                class="glyphicon glyphicon-plus-sign"></span></a>
    </div>
    <div class="big-spinner" ng-if="!materials.length">
        <div class="spinner-icon"></div>
    </div>
    <div class="news-row panel panel-danger">
        <div class="panel-heading">Содержание</div>
        <div class="panel-body">
            <ul>
                <li ng-repeat="material in materials"><a ng-click="scrollTo('material_'+material.id)" href="">{{material.title}}</a>
                </li>
            </ul>
        </div>
    </div>
    <div ng-repeat="material in materials" id="material_{{material.id}}" class="panel panel-primary">
        <div class="panel-heading">{{material.title}}<a ng-if="UserIdentity.canMakeOrders" title="Редактировать"
                                                        class="btn btn-xs btn-default pull-right"
                                                        ui-sref="editmaterial({materialId:material.id})"><span
                    class="glyphicon glyphicon-pencil"></span></a></div>
        <div class="panel-body" ng-bind-html="material.text"></div>
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
        <div class="panel-body" ng-bind-html="newsRec.text"></div>
        <div class="panel-footer">
            <span>{{newsRec.time}}</span> <img
                ng-src="/img/users/{{newsRec.issuer.img_src ? newsRec.issuer.id+'_'+newsRec.issuer.img_src+'.jpg' : 'no_image.png'}}"
                style="width:16px;height:16px;border-radius: 50%">
            <a href="#/user/view/{{newsRec.issuer.id}}">{{newsRec.issuer.nickname}}</a>
        </div>
    </div>
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
        <h2>{{user.rank.name}} "{{user.nickname}}" {{user.firstname}}</h2>
        <br>
        <table style="width: 100%">
            <tr>
                <td style="height: 210px;width: 210px">
                    <div style="margin-right:10px;position: relative">
                        <img ng-src="/img/users/{{user.img_src ? user.id+'_'+user.img_src+'.jpg' : 'no_image.png'}}"
                             style="width: 200px;display:block">
                        <div ng-if="(UserIdentity.id == 14) || (UserIdentity.id == 1)" file-upload-box></div>
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
                    <div class="uniform {{user.is_clanner ? 'clanner' : ''}}">
                        <div class="unform_rank"
                             style="background: url(/img/uniform/{{user.is_clanner ? 'clanner/' : ''}}{{user.rank.id}}.png) no-repeat"
                             title="{{user.rank.name}}">
                        </div>
                        <img title="{{medal.name}}" ng-repeat="medal in user.medals"
                             style="top:{{medal.top}}px;left:{{medal.left}}px;" ng-src="/img/awards/{{medal.id}}.png">
                        <img title="{{cross.name}}" ng-repeat="cross in user.crosses"
                             style="top: {{cross.top}}px;left:{{cross.left}}px;" ng-src="/img/awards/{{cross.id}}.png">
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
                                <td ng-bind-html="event.text"></td>
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
        <li ng-repeat="client in channel.clients"><img class="ts_group_icon" ng-repeat="group in client.groups"
                                                       ng-src="/img/groups/{{group.id}}{{client.is_clanner ? '_clanner' : ''}}.png"
                                                       title="{{group.name}}"/><a
                ng-if="client.id" href="#/user/view/{{client.id}}"> {{client.name | clearNickname}}</a><span
                ng-if="!client.id"> {{client.name | clearNickname}}</span>
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

<script type="text/ng-template" id="AfterRosterTmpl">
    <h2>Заявка принята</h2>
    <br>
    <div class="alert alert-success">
        Пожалуйста зайдите на наш <a href="ts3server://lws.exirous.com/?nickname={{UserIdentity.fullname}}">сервер
            TeamSpeak</a> и ожидайте в приёмной.
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
            <input type="text" class="form-control" placeholder="Найти по имени или никнейму" ng-model="filters.name">
        </div>
    </div>
    <div style="min-height: 550px">
        <div class="big-spinner" ng-if="isLoading">
            <div class="spinner-icon"></div>
        </div>
        <div class="col-sm-6 col-md-3 user-cell" ng-repeat="pilot in pilots">
            <a class="thumbnail isRelative" ui-sref="user({userId:pilot.id})" title="{{pilot.rank_name}}">
                <img ng-src="/img/users/{{pilot.img_src ? pilot.id+'_'+pilot.img_src+'.jpg' : 'no_image.png'}}" alt=""
                     style="width:182px; height:182px">

                <div class="floating_rank"><img
                        ng-src="/img/groups/{{pilot.rank}}{{pilot.is_clanner ? '_clanner' : ''}}.png"></div>
                <div ng-if="pilot.instructor" class="floating_rank" style="left:40px"><img
                        ng-src="/img/groups/{{pilot.instructor}}.png"></div>
                <div class="caption">
                    <b>{{pilot.nickname}}</b><br>{{pilot.firstname}}
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
            <ng-form name="nameDialog" novalidate role="form" ng-show="!userForm.forgotPass">
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
                           style="margin-top: 10px"
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
            <ng-form name="forgotDialog" novalidate role="form" ng-show="userForm.forgotPass">
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
        <div class="modal-footer" ng-show="!userForm.forgotPass">
            <button type="button" class="btn btn-default" ng-click="cancel()">Отмена</button>
            <button type="button" class="btn btn-primary" ng-click="save()"
                    ng-disabled="(nameDialog.$dirty && nameDialog.$invalid) || nameDialog.$pristine">Вход
            </button>
        </div>
        <div class="modal-footer" ng-show="userForm.forgotPass">
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

        <div class="panel panel-{{course.complete ? 'success' : 'primary'}}" ng-repeat="course in user.courses">
            <!-- ng-if="(course.rank_order <= user.rank_order)"-->
            <div class="panel-heading">{{course.name}}<span class="label label-danger pull-right"
                                                            style="font-size: 14px;">{{course.average | number : 1}}</span></div>
            <table class="table">
                <tr ng-repeat="subject in course.subjects">
                    <td>{{subject.name}}</td>
                    <td style="width:80px;text-align: right;vertical-align: middle">
                        <button type="button" ng-if="UserIdentity.isInstructor" ng-click="mark(subject.id,course.id)"
                                class="btn btn-xs btn-default"><span
                                class="glyphicon glyphicon-pencil"></span></button>
                        <span class="label label-primary" style="font-size: 14px;"
                              ng-show="user.marks[course.id][subject.id]">{{user.marks[course.id][subject.id].mark}}</span>
                    </td>
                </tr>
            </table>
            <button type="button" ng-click="promote(course.id)"
                    ng-if="(course.rank_order == user.rank_order) && course.complete" class="btn btn-sm btn-success"
                    style="width: 100%"><span class="glyphicon glyphicon-hand-down"></span> Перевести на следующий курс
            </button>
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
                          class="form-control"></textarea>
                <br>
                <input type="date" ng-model="event.dateString" class="form-control">
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

</body>
</html>