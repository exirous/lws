<?
/* @var $this Controller */
/* @var $content String */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title><?= $this->pageTitle ?></title>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.12/angular.min.js"></script>
    <script src="/scripts/angular-ui-router.js"></script>
    <script src="/scripts/app.js"></script>
    <link rel="stylesheet" href="/css/style.css" type="text/css">
    <? if ($this->id == 'admin'): ?>
        <link rel="stylesheet" href="/css/admin.css" type="text/css">
    <? endif ?>
    <!--[if lt IE 8]>
    <script type="text/javascript">
        alert('Ваш броузер не поддерживается, пожалуйста обновите! :)');
    </script>
    <![endif]-->
</head>
<body ng-app="lws">
<div id="main_content">
    <div class="main_wrapper">
        <div class="photostack" id="main_header">
        </div>
        <a ui-sref="news" id="logo"></a>
        <table class="contentTable" cellpadding=0 cellspacing=0>
            <tr>
                <td class="ml"></td>
                <td class="mm">
                    <div class="main_menu"><a ui-sref="news">Главная</a></div>
                    <div class="content" style="min-height:200px">
                        <div class="left_content">
                            <h2>Онлайн</h2>
                            <ul ng-controller="TSViewCtrl">
                                <li ng-repeat="channel in tree" ng-include="'TreeItemTmpl'">
                                </li>
                            </ul>
                        </div>
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
            </tr>
        </table>
    </div>
</div>

<script type="text/ng-template" id="NewsTmpl">
    <h1>Новости</h1>
    <div ng-repeat="newsRec in news" class="news_record {{newsRecord.type}}">
        <div>{{newsRec.issuer.name}}</div><div>{{newsRec.text}}</div>
    </div>
</script>
<script type="text/ng-template" id="TreeItemTmpl">
    {{channel.name}}
    <ul>
        <li ng-repeat="channel in channel.channels" ng-include="'TreeItemTmpl'"></li>
        <li ng-repeat="client in channel.clients"><img class="ts_group_icon" ng-repeat="group in client.groups" ng-src="/img/groups/{{group.id}}.png" title="{{group.name}}"> {{client.name}}
        </li>
    </ul>
</script>
</body>
</html>