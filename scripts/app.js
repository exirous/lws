var lwsModule = angular.module('lws', ['ui.router']);

function mainRouteConfig($stateProvider, $urlRouterProvider)
{
    //
    // For any unmatched url, redirect to /state1
    $urlRouterProvider.otherwise("/");
    //
    // Now set up the states
    $stateProvider
        .state('news', {
            url:         "/",
            templateUrl: 'NewsTmpl',
            controller:  "NewsCtrl"
        })
        .state('state2', {
            url:         "/state2",
            templateUrl: 'NewsTmpl'
        })
        .state('state2.list', {
            url:         "/list",
            templateUrl: 'NewsTmpl',
            controller:  function($scope) {
                $scope.things = ["A", "Set", "Of", "Things"];
            }
        })
}

lwsModule
    .config(mainRouteConfig)
    .controller('NewsCtrl',
    ['$scope', '$http',
     function($scope, $http) {
         $http.get('/news/last', {})
             .success(function(data) {
                 $scope.news = data.news;
             });
     }])
    .controller("TSViewCtrl", ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
        var refreshTsView = function() {
            $http.get('/teamSpeak/viewTree', {})
                .success(function(data) {
                    $scope.tree = data.tree;
                    $timeout(refreshTsView, 60 * 1000);
                });
        }
        refreshTsView();
        $scope.tree = [];
    }]);