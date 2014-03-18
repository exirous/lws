var lwsModule = angular.module('lws', ['ngRoute']);

function mainRouteConfig($routeProvider) {
    $routeProvider.
        when('/', {
            controller: 'ContentCtrl'
        }).
        otherwise({
            redirectTo: '/'
        });
}

lwsModule
    .config(mainRouteConfig)
    .controller('ContentCtrl',
    function($scope) {
        $scope.hello = "asdasd";
    })
    .controller('NewsCtrl',
    function($scope) {

    });