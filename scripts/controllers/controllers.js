angular.module('app.controllers', ['ui.router', 'app.directives', 'ui.bootstrap.datepicker', 'ui.bootstrap.buttons', 'ui.bootstrap.timepicker']);
var lwsControllers = angular.module('app.controllers');

lwsControllers.controller('AppCtrl',
    ['$scope', '$dialogs', '$stateParams', 'User',
        function ($scope, $dialogs, $stateParams, User)
        {
            $scope.UserIdentity = UserLoginData;

            $scope.login = function ()
            {
                var dlg = $dialogs.create('loginDialogTmpl', 'UserLoginCtrl', {}, {key: false, back: 'static'});
                dlg.result.then(function (user)
                {
                    $scope.UserIdentity = user;
                }, function ()
                {

                });
            }
            $scope.logout = function ()
            {
                dlg = $dialogs.confirm('Подтвердите', 'Вы хотите выйти из системы?');
                dlg.result.then(function (btn)
                {
                    User.logout({}, function (resource)
                    {
                        $scope.UserIdentity = {isGuest: true, fullname: 'Неизвестный Гость'};
                    });
                }, function (btn)
                {
                });
            }


        }]);


lwsControllers.controller('UserLoginCtrl',
    ['$scope', '$modalInstance', 'data', 'User','$dialogs',
        function ($scope, $modalInstance, data, User, $dialogs)
        {
            $scope.user = {email: '', password: ''};
            $scope.userForm = {forgotPass:false};
            $scope.cancel = function ()
            {
                $modalInstance.dismiss('canceled');
            }; // end cancel

            $scope.save = function ()
            {
                $scope.user.error = '';
                User.login({email: $scope.user.email, password: $scope.user.password}, function (resource)
                    {
                        $modalInstance.close(resource.data);
                    },
                    function (resource)
                    {
                        $scope.user.error = resource.data.message;
                    });
                $scope.user.password = '';
            }; // end save

            $scope.sendPass = function ()
            {
                $scope.userForm.errorPass = '';
                User.recover({email: $scope.user.email}, function (resource)
                    {
                        $dialogs.notify('Something Happened!','Something happened that I need to tell you.');
                        $modalInstance.close(resource.data);
                    },
                    function (resource)
                    {
                        $scope.user.errorPass = resource.data.message;
                    });

            }; // end save


            $scope.hitEnter = function (evt)
            {
                if (angular.equals(evt.keyCode, 13) && !(angular.equals($scope.email, null) || angular.equals($scope.email, '')) && !(angular.equals($scope.password, null) || angular.equals($scope.password, '')))
                    $scope.save();
            }; // end hitEnter

            $scope.hitEnterForgot = function (evt)
            {
                if (angular.equals(evt.keyCode, 13) && !(angular.equals($scope.email, null) || angular.equals($scope.email, '')) && !(angular.equals($scope.password, null) || angular.equals($scope.password, '')))
                    $scope.sendPass();
            }; // end hitEnter

        }]);

lwsControllers.controller('NewsCtrl',
    ['$scope', 'News',
        function ($scope, News)
        {
            News.last({}, function (res)
            {
                $scope.news = res.data;
            });
        }]);

lwsControllers.controller('UserCtrl',
    ['$scope', 'User', '$stateParams',
        function ($scope, User, $stateParams)
        {
            User.get({id: $stateParams.userId},
                function (resource)
                {
                    $scope.user = resource.data;
                });
        }]);

lwsControllers.controller('RosterCtrl',
    ['$scope', 'User', '$stateParams',
        function ($scope, User, $stateParams)
        {

            var dfrom = new Date();
            dfrom.setHours(20);
            dfrom.setMinutes(0);
            var dto = new Date();
            dto.setHours(21);
            dto.setMinutes(0);

            $scope.user = {
                craft: {
                    bf109: false,
                    p36g: false
                },
                onlineFrom: dfrom,
                onlineTo: dto,
                birthdate: '1945-05-09'
            };

            $scope.userForm = {isSubmitting: false};

            $scope.open = function ($event)
            {
                $event.preventDefault();
                $event.stopPropagation();
                $scope.opened = true;
            };
            $scope.dateOptions = {
                'year-format': "'yyyy'",
                'starting-day': 1
            };

            $scope.send = function ()
            {
                $scope.userForm.isSubmitting = true;
                $scope.userForm.error = false;
                User.roster({user: $scope.user}, function (resource)
                {
                    $scope.userForm.isSubmitting = false;
                    angular.extend($scope.UserIdentity, resource.data);
                    setTimeout(function ()
                    {
                        document.location.href = "/#/afterroster"
                    }, 100);
                }, function (resource)
                {
                    $scope.userForm.isSubmitting = false;
                    $scope.userForm.error = resource.data.message
                });
            }

        }]);

lwsControllers.controller("TSViewCtrl", ['$scope', 'TeamSpeak', '$timeout', function ($scope, TeamSpeak, $timeout)
{
    var refreshTsView = function ()
    {
        TeamSpeak.tree({}, function (res)
        {
            $scope.tree = res.data;
            $timeout(refreshTsView, 60 * 1000);
        });
    };
    refreshTsView();
    $scope.tree = [];
}]);


lwsControllers.controller("AfterRosterCtrl", ['$scope', function ($scope)
{

}]);