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
    'ui.router.stateHelper'
]);

function mainRouteConfig($stateProvider, $urlRouterProvider)
{
    //
    // For any unmatched url, redirect to /state1
    $urlRouterProvider.otherwise("/");
    //
    // Now set up the states
    $stateProvider
        .state('news', {
            url: "/",
            templateUrl: 'NewsTmpl',
            controller: "NewsCtrl"
        })
        .state('user', {
            url: "/user/view/:userId",
            templateUrl: 'UserTmpl',
            controller: "UserCtrl"
        }).state('roster', {
            url: "/roster",
            templateUrl: 'RosterTmpl',
            controller: "RosterCtrl"
        })
        .state('rosterUser', {
            url: "/user/roster/:userId",
            templateUrl: 'RosterUserTmpl',
            controller: "RosterUserCtrl"
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