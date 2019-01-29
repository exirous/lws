var lwsApp = angular.module('app', [
    'ui.router',
    'ui.bootstrap.tpls',
    'ui.bootstrap.tabs',
    'ui.bootstrap.modal',
    'ui.bootstrap.dropdownToggle',
    'ui.router.stateHelper',
    'app.controllers',
    'app.services',
    'app.directives',
    'app.filters',
    'dialogs',
    'ui.router.stateHelper',
    'ngAnimate',
    'chieffancypants.loadingBar',
    'angularFileUpload',
    'dnd'
]);

lwsApp.run(['$rootScope', '$location', '$window', function($rootScope, $location, $window){
    $rootScope
        .$on('$stateChangeSuccess',
        function(event){
            if (!$window.ga)
                return;
            console.log("page view!", $location.path());
            $window.ga('send', 'pageview', { page: $location.path() });
        });
}]);


function mainRouteConfig($stateProvider, $urlRouterProvider)
{
    //
    // For any unmatched url, redirect to /state1
    $urlRouterProvider.otherwise("/news/");
    //
    // Now set up the states
    $stateProvider
        .state('news', {
            url: "/news/:page",
            templateUrl: 'NewsTmpl',
            controller: "NewsCtrl"
        })
        .state('orders', {
            url: "/orders/:page",
            templateUrl: 'OrdersTmpl',
            controller: "OrdersCtrl"
        })
        .state('user', {
            url: "/user/view/:userId",
            templateUrl: 'UserTmpl',
            controller: "UserCtrl"
        })
        .state('battleLog', {
            url: "/user/battlelog/:userId",
            templateUrl: 'BattleLogTmpl',
            controller: "BattleLogCtrl"
        })
        .state('addBattleLog', {
            url: "/user/battlelog/:userId/new",
            templateUrl: 'EditBattleLogTmpl',
            controller: "NewBattleLogCtrl"
        })
        .state('editBattleLog', {
            url: "/user/battlelog/:userId/:logId",
            templateUrl: 'EditBattleLogTmpl',
            controller: "EditBattleLogCtrl"
        })
        .state('personalFile', {
            url: "/user/personalfile/:id",
            templateUrl: 'PersonalFileTmpl',
            controller: "PersonalFileCtrl"
        })
        .state('editpersonalFile', {
            url: "/user/editpersonalfile/:id",
            templateUrl: 'EditPersonalFileTmpl',
            controller: "EditPersonalFileCtrl"
        })
        .state('inactiveUsers', {
            url: "/inactive",
            templateUrl: 'InactiveTmpl',
            controller: "InactiveCtrl"
        })
        .state('editUser', {
            url: "/user/edit/:userId",
            templateUrl: 'EditUserTmpl',
            controller: "EditUserCtrl"
        })
        .state('texts', {
            url: "/text/:id",
            templateUrl: 'TextTmpl',
            controller: "TextCtrl"
        })
        .state('roster', {
            url: "/roster",
            templateUrl: 'RosterTmpl',
            controller: "RosterCtrl"
        })
        .state('rosterUser', {
            url: "/user/roster/:userId",
            templateUrl: 'RosterUserTmpl',
            controller: "RosterUserCtrl"
        })
        .state('userMarks', {
            url: "/user/marks/:userId",
            templateUrl: 'userMarksTmpl',
            controller: "UserMarksCtrl"
        })
        .state('recoverUser', {
            url: "/user/recover/:token",
            templateUrl: 'RecoverUserTmpl',
            controller: "RecoverUserCtrl"
        })
        .state('afterroster', {
            url: "/afterroster",
            templateUrl: 'AfterRosterTmpl',
            controller: "AfterRosterCtrl"
        })
        .state('makeorder', {
            url: "/makeorder",
            templateUrl: 'OrderCreatorTmpl',
            controller: "OrderCreatorCtrl"
        })
        .state('makenews', {
            url: "/makenews",
            templateUrl: 'NewsCreatorTmpl',
            controller: "NewsCreatorCtrl"
        })
        .state('editnews', {
            url: "/editnews/:id",
            templateUrl: 'NewsCreatorTmpl',
            controller: "NewsCreatorCtrl"
        })
        .state('school', {
            url: "/school",
            templateUrl: 'SchoolTmpl',
            controller: "SchoolCtrl"
        })
        .state('materials', {
            url: "/school/:slug",
            templateUrl: 'SchoolTmpl',
            controller: "SchoolCtrl"
        })
        .state('editmaterial', {
            url: "/school/:slug/edit/:materialId",
            templateUrl: 'EditMaterialTmpl',
            controller: "EditMaterialCtrl"
        })
        .state('edittext', {
            url: "/text/edit/:id",
            templateUrl: 'EditTextTmpl',
            controller: "EditTextCtrl"
        })
        .state('pilots', {
            url: "/pilots",
            templateUrl: 'BarracksTmpl',
            controller: "BarracksCtrl"
        })
        .state('awards', {
            url: "/awards",
            templateUrl: 'AwardsTmpl',
            controller: "AwardsCtrl"
        })
        .state('editaward', {
            url: "/editaward/:awardId",
            templateUrl: 'AwardTmpl',
            controller: "AwardCtrl"
        })
        .state('flood', {
            url: "/flood",
            templateUrl: 'FloodTmpl',
            controller: "FloodCtrl"
        })
        .state('newtopic', {
            url: "/newtopic",
            templateUrl: 'NewTopicTmpl',
            controller: "NewTopicCtrl"
        })
        .state('topic', {
            url: "/flood/:topicId",
            templateUrl: 'TopicTmpl',
            controller: "TopicCtrl"
        })
        .state('topic.page', {
            url: "/page-:page",
            templateUrl: 'TopicMessagesTmpl',
            controller: "TopicPageCtrl"
        })
        .state('reportvacation', {
            url: "/vacation/report",
            templateUrl: 'ReportVacationTmpl',
            controller: "ReportVacationCtrl"
        })
        .state('messenger', {
            url: "/messenger",
            templateUrl: 'MessengerTmpl',
            controller: "MessengerCtrl"
        })
        .state('conversation', {
            url: "/conversation/:senderId",
            templateUrl: 'ConversationTmpl',
            controller: "ConversationCtrl"
        })
        .state('conversation.page', {
            url: "/page-:page",
            templateUrl: 'ConversationMessagesTmpl',
            controller: "ConversationPageCtrl"
        })

}
lwsApp.config(mainRouteConfig);
lwsApp.config( [
        '$compileProvider',
        function( $compileProvider )
        {
            $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|ts3server):/);
            // Angular before v1.2 uses $compileProvider.urlSanitizationWhitelist(...)
        }
    ]);

var SoundAlert = {
    _sound: new Audio("/alert.mp3"),
    play: function () {
        this._sound.currentTime = 0;
        this._sound.play();
    }
};

var NotificationsAllowed = false;
if (typeof Notification != 'undefined' && Notification.requestPermission)
    Notification.requestPermission(function (state) {
        NotificationsAllowed = (state == 'granted')
    });


$(function(){
    var navBarDetached = false;
    function checkScroll() {
        if (!navBarDetached && document.body.scrollTop > 309) {
            navBarDetached = true;
            $('.main_menu_cover').addClass('detached');
        } else if(navBarDetached && document.body.scrollTop < 310){
            navBarDetached = false;
            $('.main_menu_cover').removeClass('detached');
        }
    }
    $(document).on('scroll', function(){
        checkScroll();
    })
    checkScroll();
})