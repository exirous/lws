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
    <script src="/scripts/lib/angular.js"></script>
    <script src="/scripts/lib/angular-ui-router.js"></script>
    <script src="/scripts/lib/angular-resource.js"></script>
    <script src="/scripts/lib/angular-sanitize.js"></script>
    <script src="/scripts/lib/select2.js"></script>
    <script src="/scripts/lib/ui-bootstrap-tpls.js"></script>
    <script src="/scripts/lib/statehelper.js"></script>
    <script src="/scripts/lib/dialogs.js"></script>
    <script src="/scripts/app.js"></script>
    <script src="/scripts/services/services.js"></script>
    <script src="/scripts/filters/filters.js"></script>
    <script src="/scripts/directives/directives.js"></script>
    <script src="/scripts/controllers/controllers.js"></script>
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.css" type="text/css">
    <!--<link rel="stylesheet" href="/css/bootstrap.css" type="text/css">-->
    <link rel="stylesheet" href="/css/style.css" type="text/css">
    <link rel="stylesheet" href="/css/select2.css" type="text/css">
    <link rel="stylesheet" href="/css/select2-bootstrap.css" type="text/css">
    <!--<link rel="stylesheet" href="/css/chosen.css" type="text/css">-->
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
        </div>
        <a ui-sref="news" id="logo"></a>
        <table class="contentTable" cellpadding=0 cellspacing=0>
            <tbody>
            <tr>
                <td class="ml"></td>
                <td class="mm">
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
                        <a ui-sref="news">Главная</a>
                        <a ui-sref="roster" ng-if="UserIdentity.isGuest">Вступить в школу</a>
                        <a ui-sref="makeorder" ng-if="UserIdentity.canMakeOrders">Отдать приказ</a>
                        <a ui-sref="makenews" ng-if="UserIdentity.canMakeOrders">Добавить новость</a>
                        <a href="" style="float:right" ng-click="login()" ng-if="UserIdentity.isGuest">Вход</a>
                        <a href="" style="float:right" ng-click="logout()" ng-if="!UserIdentity.isGuest">Выход</a>
                    </div>
                    <div class="content" style="min-height:200px">
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
    <h2>Приказы и объявления</h2>
    <div ng-repeat="newsRec in news" class="panel panel-{{newsRec.type == 'order' ? 'primary' : 'default'}}">
        <div class="panel-heading">{{newsRec.title}}</div>
        <div class="panel-body" ng-bind-html="newsRec.text"></div>
        <div class="panel-footer">
            <span>{{newsRec.time}}</span> <img ng-src="{{newsRec.issuer.id | avatarUrl}}" style="max-width: 15px;max-height: 15px;">
            <a href="#/user/view/{{newsRec.issuer.id}}">{{newsRec.issuer.name}}</a>
        </div>
    </div>
</script>

<script type="text/ng-template" id="UserTmpl">
    <div ng-show="user">
        <h1>{{user.rank.name}} "{{user.nickname}}" {{user.firstname}}</h1>
        <br>
        <table>
            <tr>
                <td style="height: 120px;">
                    <div style="margin-right:10px"><img ng-src="{{user.id | avatarUrl}}"></div>
                </td>
                <td>
                    <table class="table" style="width: auto">
                        <tbody>
                        <tr><th>Родился</th><td>{{user.birthDate | date : "dd.MM.yyyy"}}</td></tr>
                        <tr><th>Втсупил в школу</th><td>{{user.joinDate | date : "dd.MM.yyyy"}}</td></tr>
                        <tr ng-if="user.rank"><th>{{user.rank.order < 5 ? 'Курс' : 'Звание'}}</th><td>{{user.rank.name}}</td></tr>
                        <tr ng-if="user.instructor"><th>Степень</th><td>{{user.instructor.name}}</td></tr>
                        <tr ng-if="UserIdentity.isInstructor"><th>Заявка</th><td><a ui-sref="rosterUser({userId:user.id})">Посмотреть</a></td></tr>
                        <tr ng-if="UserIdentity.isInstructor"><th>Оценки</th><td><a ui-sref="userMarks({userId:user.id})">Посмотреть</a></td></tr>
                        </tbody>
                    </table>
                </td>
                <td ng-if="user.rank" rowspan="2">
                    <div class="uniform">
                        <div class="unform_rank"
                             style="background: url(/img/uniform/{{user.rank.id}}.png) no-repeat" title="{{user.rank.name}}"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-right:10px">
                    <div class="panel panel-default">
                        <div class="panel-heading">Лётная книга</div>
                        <table class="table">
                            <tbody>
                            <tr ng-repeat="event in user.events">
                                <td ng-bind-html="event.text"></td>
                                <td>{{event.date | date : "dd.MM.yyyy"}}</td>
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
                                                       ng-src="/img/groups/{{group.id}}.png" title="{{group.name}}"/><a
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
            <label for="user_birthdate">Когда вы родились?</label>

            <p class="input-group"
               ng-class="{true: 'has-error'}[(rosterForm.birthdate.$dirty && rosterForm.birthdate.$invalid)]">
                <input type="date" placeholder="Например: 09.05.1945" name="birthdate" class="form-control"
                       ng-model="user.birthdate" ng-required="true"/>
            </p>
            <label>Насколько усердно готовы изучать учебный материал? (выберите цифру по пятибальной шкале)</label>

            <div>
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

            <p class="form-group input-group-lng">
                <input type="text" placeholder="Например: Друг рассказал, Вычитал в журнале, и.т.д" id="user_reason"
                       class="form-control" ng-model="user.reason" required/>
            </p>
            <label>Состоите ли в каком-нибудь СКВАДе, Клане или Полку)?</label>

            <div>
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.squad" btn-radio="'yes'" required>Да
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.squad" btn-radio="'no'">Нет</button>
                </p>
            </div>
            <label>Наличие в ангаре самолетов Bf-109E-3 и/или Р-36G для истребителей</label>

            <div>
                <p class="btn-group">
                    <button type="button" class="btn btn-default" ng-model="user.craft.bf109" btn-checkbox>Bf-109E-3
                        (Эмиль)
                    </button>
                    <button type="button" class="btn btn-default" ng-model="user.craft.p36g" btn-checkbox>P-36G Hawk
                    </button>
                </p>
            </div>
            <label>Технику какой нации предпочитаете больше всего пилотировать?</label>

            <div>
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
            <label>Время вашего онлайна</label>

            <div>
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

            <div>
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

<script type="text/ng-template" id="RosterUserTmpl">
    <div ng-show="!pilot.id">
        <h2>Загрузка....</h2>
    </div>
    <div ng-show="pilot.id">
        <h2>Заявка на вступление от {{pilot.nickname}} ({{pilot.firstname}})</h2>
        <br>
        <label>Родился:</label><br>
        <span>{{pilot.roster.birthdate}}</span><br>
        <label>Оценка готовности стремления обучатся:</label><br>
        <span>{{pilot.roster.scale}}</span><br>
        <label>Попал в школу посредством:</label><br>
        <span>{{pilot.roster.reason}}</span><br>
        <label>Состоит в скваде:</label><br>
        <span ng-bind="pilot.roster.squad=='yes' ? 'Да' : 'Нет'"></span><br>
        <label>Наличие в ангаре самолетов Bf-109E-3 и/или Р-36G для истребителей:</label><br>
        <span ng-show="pilot.roster.craft.bf109">Bf 109E-3</span>
        <span ng-show="pilot.roster.craft.p36g">P-36G Hawk</span>
        <br>
        <label>Предпочитает пилотировать технику:</label><br>
        <span>{{pilot.roster.nation}}</span><br>
        <label>Время онлайна:</label><br>
        <span>C </span><span>{{pilot.roster.onlineFrom | date : "HH:mm"}}</span><span> По </span><span>{{pilot.roster.onlineTo | date : "HH:mm"}}</span>
        <br><br>

        <div ng-show="pilot.rank" class="alert alert-success">Пилот принят</div>
        <div ng-form="rosterForm" ng-show="!pilot.rank">
            <p class="well">
                <select ng-model="rosterForm.tsId"
                        required
                        data-placeholder="Привязка к TeamSpeak"
                        ui-select2>
                    <option></option>
                    <option ng-repeat="option in pilot.possibleUsers" value="{{option.uid}}">{{option.name}}</option>
                </select>&nbsp;
                <button type="button" ng-click="accept()"
                        ng-disabled="(rosterForm.$dirty && rosterForm.$invalid) || rosterForm.$pristine || rosterForm.isSubmitting"
                        class="btn btn-primary">Принять
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
                        ng-model="orderData.pilots"
                        required>
                    <option ng-repeat="pilot in initialData.pilots" value="{{pilot.id}}" data-rankid="{{pilot.rank}}">
                        {{pilot.nickname}}
                    </option>
                </select>
                <textarea style="resize: none;width: 100%" rows="3" placeholder="Впишите Событие"
                          ng-model="updatedData.event"></textarea>
            </div>
            <div
                style="resize: none;display:inline-block;width: 49%;padding:5px;border:1px solid #aaa;min-height:107px;background: #eee;color:#555"
                rows="5" id="completeData" data-ng-bind-html="updatedData.complete"></div>
        </div>
        <div class="form-group">
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
                <button type="button" ng-click="save()"
                        ng-disabled="(orderForm.$dirty && orderForm.$invalid) || orderForm.$pristine || orderData.isSubmitting"
                        class="btn btn-primary">Отдать
                </button>
            </p>
        </div>
    </ng-form>
</script>
<script type="text/ng-template" id="userMarksTmpl">
    <div ng-show="user.id">
       <h2>Оценочный лист пилота {{user.nickname}}</h2>
        <div class="panel panel-{{course.complete ? 'success' : 'primary'}}" ng-repeat="course in user.courses" ng-if="(course.rank_order <= user.rank_order)">
            <div class="panel-heading">{{course.name}}<span class="label label-danger pull-right" style="font-size: 14px;">{{course.average}}</span></div>
            <table class="table">
                <tr ng-repeat="subject in course.subjects">
                    <td>{{subject.name}}</td>
                    <td style="width:80px;text-align: right;vertical-align: middle">
                        <button type="button" ng-click="mark(subject.id,course.id)" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></button>
                        <span class="label label-primary" style="font-size: 14px;" ng-show="user.marks[course.id][subject.id]">{{user.marks[course.id][subject.id].mark}}</span>
                    </td>
                </tr>
            </table>
            <button type="button" ng-click="promote(course.id)" ng-if="(course.rank_order == user.rank_order) && course.complete" class="btn btn-sm btn-success" style="width: 100%"><span class="glyphicon glyphicon-hand-down"></span> Перевести на следующий курс</button>
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
                        <button type="button" ng-model="userMark.mark" btn-radio="'1'" class="btn btn-primary">1</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'2'" class="btn btn-primary">2</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'3'" class="btn btn-primary">3</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'4'" class="btn btn-primary">4</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" ng-model="userMark.mark" btn-radio="'5'" class="btn btn-primary">5</button>
                    </div>
                </div>
            </ng-form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-disabled="userMark.isSubmitting" ng-click="cancel()">Отмена</button>
            <button type="button" class="btn btn-primary" ng-disabled="userMark.isSubmitting" ng-click="saveMark()">Сохранить</button>
        </div>
    </div>
</script>

</body>
</html>