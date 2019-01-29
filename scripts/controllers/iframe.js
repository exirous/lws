var lwsApp = angular.module('app',[]);

lwsApp.filter('clearNickname', function ()
{
    return function (input)
    {
        return input.replace(/\((.*?)\)/, '');
    };
});

lwsApp.controller('AppCtrl',
    ['$scope',
        function ($scope) {
            $scope.io_socket = false;
            if (typeof io != 'undefined') {
                $scope.io_socket = io.connect('/socket.io');
                $scope.io_socket.on('connect', function (data) {
                    $scope.tree = [];
                    if ($scope.io_socket)
                        $scope.io_socket.on('ts_clients', function (data) {
                            if (data) {
                                $scope.tree = data;
                                $scope.$apply();
                            }
                        });
                });
            }
        }]);