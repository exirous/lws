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
                                <a href="" ng-repeat="pilot in roster" class="list-group-item">{{pilot.nickname}} ({{pilot.firstname}})</a>
                            </div>
                        </div>
                    </div>
                    <div class="main_menu"><a href="ts3server://lws.exirous.com/?nickname={{UserIdentity.fullname}}">TeamSpeak</a></div>
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
    <div ng-repeat="newsRec in news" class="news_record {{newsRecord.type}}">
        <h4>{{newsRec.title}}</h4>
        <div ng-bind-html="newsRec.text"></div>
        <div><span>{{newsRec.time}}</span> <img ng-src="{{newsRec.issuer.id | avatarUrl}}"
                                                style="max-width: 15px;max-height: 15px;"> <a
                href="#/user/view/{{newsRec.issuer.id}}">{{newsRec.issuer.name}}</a></div>
    </div>
</script>

<script type="text/ng-template" id="UserTmpl">
    <div ng-show="user">
        <h1>Пилот "{{user.nickname}}" {{user.name}}</h1>
        <div><img ng-src="{{user.id | avatarUrl}}"></div>
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
            <textarea style="resize: none;width: 100%" rows="3" placeholder="Впишите Событие" ng-model="updatedData.event"></textarea>
            </div>
            <div style="resize: none;display:inline-block;width: 49%;padding:5px;border:1px solid #aaa;min-height:107px;background: #eee;color:#555" rows="5" id="completeData" data-ng-bind-html="updatedData.complete"></div>
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
                            <option ng-repeat="rank in initialData.rankArray | orderBy:'order'" value="{{rank.id}}">{{rank.name}}</option>
                        </select>
                    </div>
                    <div>
                        <select data-placeholder="Выберите Инструкторскую Категорию"
                                ui-select2="instructorSelect2Options"
                                style="width: 300px;"
                                ng-model="pilot.instructor">
                            <option></option>
                            <option ng-repeat="rank in initialData.instructorsArray | orderBy:'order'" value="{{rank.id}}">{{rank.name}}</option>
                        </select>
                    </div>
                </div>
                <select data-placeholder="Выберите Награды"
                        ui-select2="awardSelect2Options"
                        style="width: 535px;vertical-align: top;"
                        multiple
                        ng-model="pilot.awards">
                    <option ng-repeat="award in initialData.awards" value="{{award.id}}"
                            ng-disabled="{{(initialData.pilots[pilot.id].awards.indexOf(award.id)>=0) && award.only_one_allowed ? true : false}}">{{award.name}}
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

</body>
</html>