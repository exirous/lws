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
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.12/angular-route.js"></script>


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
        <a href="/" id="logo"></a>
        <table class="contentTable" cellpadding=0 cellspacing=0>
            <tr>
                <td class="ml"></td>
                <td class="mm">
                    <div class="main_menu"></div>
                    <div class="content" style="min-height:200px">
                        <div class="left_content">
                        </div>
                        <div class="center_content" ng-controller="ContentCtrl">
                            <div ng-controller="NewsCtrl">

                            </div>
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
</body>
</html>