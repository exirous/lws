<?
/* @var $this Controller */
/* @var $content String */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Школа виртуального пилотирования LuftwaffeSchule</title>
    <!--<script src="/iframe_scripts.js"></script>-->
    <script src="/scripts/lib/angular.min.js"></script>
    <script src="/scripts/controllers/iframe.js"></script>
    <script src="http://luftwaffeschule.ru:3000/socket.io/socket.io.js"></script>
    <link rel="stylesheet" href="/css/iframe.css" type="text/css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,700,600italic,600,400italic,300italic,300&subset=latin,cyrillic" type="text/css">
</head>
<body ng-app="app">
<div class="left_content ts_channels">
    <ul ng-controller="AppCtrl">
        <li ng-show="tree.length" ng-repeat="channel in tree" ng-include="'TreeItemTmpl'">
        </li>
        <li ng-show="!tree.length && !tree.empty" class="ts_empty">
            TeamSpeak пуст
        </li>
        <li ng-show="tree.empty" class="ts_empty">
            TeamSpeak пуст
        </li>
    </ul>
</div>
<script type="text/ng-template" id="TreeItemTmpl">
    <img src="/img/design/bullets/channel_icon.png"><span> {{channel.name}}</span>
    <ul>
        <li ng-repeat="channel in channel.channels" ng-include="'TreeItemTmpl'"></li>
        <li ng-repeat="client in channel.clients">
            <img class="ts_group_icon" ng-repeat="group in client.groups" ng-if="group != 6" ng-src="/img/groups/{{group}}{{client.is_clanner ? '_clanner' : ''}}.png"/>
            <a href="" ng-if="client.uid"> {{client.name | clearNickname}}</a>
        </li>
    </ul>
</script>
</body>
</html>