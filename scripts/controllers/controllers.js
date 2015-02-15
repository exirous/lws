angular.module('app.controllers',
    ['ui.router',
        'app.directives',
        'ui.bootstrap.datepicker',
        'ui.bootstrap.buttons',
        'ui.bootstrap.accordion',
        'ui.bootstrap.timepicker',
        'ui.bootstrap.pagination',
        'ui.select2']);
var lwsControllers = angular.module('app.controllers');

lwsControllers.controller('AppCtrl',
    ['$scope', '$dialogs', '$stateParams', 'User', '$rootScope', '$state', '$filter',

        function ($scope, $dialogs, $stateParams, User, $rootScope, $state, $filter)
        {
            $scope.UserIdentity = UserLoginData;

            $scope.notifications = [];

            $scope.addNotification = function(data, callback)
            {
                var notification = {};
                notification.text = data.text;
                notification.type = data.type;
                notification.callback = callback;

                if (data.timeout)
                {
                    setTimeout(function(){
                        var index = $scope.notifications.indexOf(notification);
                        if (index >= 0)
                          $scope.notifications.splice(index, 1);
                    },data.timeout*1000);
                }
                $scope.$apply(function(){
                    $scope.notifications.push(notification);
                });
            };

            $scope.closeNotification = function(index)
            {
                if ($scope.notifications[index].callback)
                    $scope.notifications[index].callback();
                $scope.notifications.splice(index, 1);
            };


            $scope.headerImages = ['/img/header/news.png'];
            var stateAliases = {
                'orders': 'news',
                'makeorder': 'news',
                'makenews': 'news',
                'editnews': 'news',
                'roster': 'pilots',
                'inactiveUsers':'pilots',
                'rosterUser': 'user',
                'userMarks': 'user',
                'afterroster': 'user',
                'edittext': 'news',
                'editmaterial': 'school',
                'materials': 'school',
                'flood': 'news',
                'topic': 'news',
                'newtopic': 'news',
                'topic.page': 'news',
                'reportvacation': 'news',
                'vacation': 'news',
                'editUser': 'user',
                'conversation': 'user',
                'conversation.page': 'user',
                'messenger': 'user'
            };

            $scope.unRegisterForNotifications = function(){};
            $scope.registerForNotifications = function(){};
            $scope.io_socket = false;
            if (typeof io != 'undefined') {
                $scope.registerForNotifications = function(){
                    $scope.io_socket.on('new_message', function (data) {
                        SoundAlert.play();
                        if (!$state.is('conversation.page', {
                                senderId: data.sender.id + '',
                                page: '1'
                            }) && !$state.is('messenger', {})) {

                            NotificationsAllowed = false;
                            if (NotificationsAllowed) {
                                var messageNotification = new Notification(data.sender.nickname, {
                                    tag: false,
                                    body: $('<i>' + data.text + '</i>').text(),
                                    icon: '/img/users/' + (data.sender.img_src ? data.sender.id + '_' + data.sender.img_src + '.jpg' : (data.sender.is_clanner ? 'no_image_clanner.png' : 'no_image.png'))
                                });
                                messageNotification.onclick = function () {
                                    $state.go('conversation.page', {senderId: data.sender.id + '', page: '1'});
                                };
                            }
                            else {
                                var html = '<img  class="avatar" ng-src="/img/users/' + (data.sender.img_src ? data.sender.id + '_' + data.sender.img_src + '.jpg' : (data.sender.is_clanner ? 'no_image_clanner.png' : 'no_image.png')) +
                                    '"><div class="message-content"><a class="user" >' + data.sender.nickname + '</a><span class="text">' + data.text + '</span></div>';
                                $scope.addNotification({type: 'message', text: html}, function(){
                                    $state.go('conversation.page', {senderId: data.sender.id + '', page: '1'});
                                });
                            }
                        }
                        else
                            $scope.$broadcast('new_message', data);
                    });
                    $scope.io_socket.emit('register', {token: $scope.UserIdentity.broadcast_token, uid:$scope.UserIdentity.uid});
                };
                $scope.unRegisterForNotifications = function()
                {
                    $scope.io_socket.emit('unregister', {token: $scope.UserIdentity.broadcast_token});
                }

                $scope.io_socket = io.connect('http://luftwaffeschule.ru:3000');
                $scope.io_socket.on('ready', function (data) {
                    if (!$scope.UserIdentity.isGuest)
                        $scope.registerForNotifications();
                });
            }

            function checkIfDisabled() {
                if ($scope.UserIdentity.isDisabled) {
                    var dlg = $dialogs.error('Внимание!',
                        'Вы были исключенны из школы по причине:<br><b>'
                        + $scope.UserIdentity.disableReason +
                        '</b><br>Немедленно свяжитесь с Руководителем полка <a href="/#/conversation/14/page-1">RekruT-ом!</a>');
                }
            }

            $rootScope.$on('refreshUserLogin', function () {
                checkIfDisabled()
            });
            checkIfDisabled();


            $scope.$on('$stateChangeStart',
                function (event, toState, toParams, fromState, fromParams)
                {
                    var stateName = toState.name;
                    var fromStateName = fromState.name;

                    if (fromState.abstract && stateName == 'news')
                        return;

                    if (stateAliases[stateName])
                        stateName = stateAliases[stateName];

                    if (stateAliases[fromStateName])
                        fromStateName = stateAliases[fromStateName];

                    if (fromStateName == stateName)
                        return;
                    $scope.headerImages.shift();
                    $scope.headerImages.push('/img/header/' + stateName + '.png');
                });

            $scope.login = function ()
            {
                var dlg = $dialogs.create('loginDialogTmpl', 'UserLoginCtrl', {}, {key: false, back: 'static'});
                dlg.result.then(function (user)
                {
                    if (user && user.id)
                    {
                        $scope.UserIdentity = user;
                        $rootScope.$broadcast('refreshUserLogin');
                        $scope.registerForNotifications();
                    }
                }, function ()
                {

                });
            };
            $scope.logout = function ()
            {
                var dlg = $dialogs.confirm('Подтвердите', 'Вы хотите выйти из системы?');
                dlg.result.then(function (btn)
                {

                    User.logout({}, function (resource)
                    {
                        $scope.unRegisterForNotifications();
                        $rootScope.$broadcast('refreshUserLogin');
                        $scope.UserIdentity = {isGuest: true, fullname: 'Неизвестный Гость'};
                    });
                }, function (btn)
                {
                });
            };

            $scope.vacation = function()
            {

            }

            $rootScope.expel = function (pilot) {
                var dlg = $dialogs.create('rejectDialogTmpl', 'RejectDialogCtrl', {
                    text: 'Отсутствие на тренировках в школе пилотов Luftwaffe сроком ' + $filter('timeAgo')(pilot.lastOnline, true) + '  без указания причины',
                    header: 'исключения из школы',
                    buttonText: 'Исключить'
                }, {key: false, back: 'static'});
                dlg.result.then(function (reason)
                {
                    User.expel({userId: pilot.id, reason: reason}, function (resource) {
                        pilot.expelled = true;
                        pilot.isDisabled = true;
                        setTimeout(function () {
                            $rootScope.$broadcast('pilotExpelled');
                        }, 200);
                    });
                }, function (reason)
                {

                });
            }

            $rootScope.reenlist = function (pilot) {
                var dlg = $dialogs.create('rejectDialogTmpl', 'RejectDialogCtrl', {
                    text: '',
                    header: 'восстановления в школе',
                    buttonText: 'Восстановить'
                }, {key: false, back: 'static'});
                dlg.result.then(function (reason)
                {
                    User.reenlist({userId: pilot.id, reason: reason}, function (resource) {
                        pilot.expelled = false;
                        pilot.isDisabled = false;
                        setTimeout(function () {
                            $rootScope.$broadcast('pilotReenlisted');
                        }, 200);
                    });
                }, function (reason)
                {

                });
            }



        }]);

lwsControllers.controller('UserLoginCtrl',
    ['$scope', '$modalInstance', 'data', 'User', '$dialogs',
        function ($scope, $modalInstance, data, User, $dialogs)
        {
            $scope.user = {email: '', password: ''};
            $scope.userForm = {forgotPass: false};
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
                        $dialogs.notify('Восстановление пароля', 'Скоро к вам прийдёт письмо по э-почте что вы указали<br>Следуйте инструкциям в письме для смены пароля');
                        $modalInstance.close(resource.data);
                    },
                    function (resource)
                    {
                        $scope.user.errorPass = resource.data.message;
                    });

            }; // end save

            $scope.hitEnter = function (evt)
            {
                if (angular.equals(evt.keyCode, 13) && !(angular.equals($scope.email, null)
                    || angular.equals($scope.email, '')) && !(angular.equals($scope.password, null)
                    || angular.equals($scope.password, '')))
                    $scope.save();
            }; // end hitEnter

            $scope.hitEnterForgot = function (evt)
            {
                if (angular.equals(evt.keyCode, 13) && !(angular.equals($scope.email, null)
                    || angular.equals($scope.email, '')) && !(angular.equals($scope.password, null)
                    || angular.equals($scope.password, '')))
                    $scope.sendPass();
            }; // end hitEnter

        }]);

lwsControllers.controller('NewsCtrl',
    ['$scope', 'News', '$rootScope',
        function ($scope, News, $rootScope)
        {
            function reloadNews()
            {
                News.last({}, function (res)
                {
                    $scope.news = res.data;
                });
            }
            reloadNews();
            $rootScope.$on('refreshUserLogin', function(){
                reloadNews();
            });
        }]);

lwsControllers.controller('OrdersCtrl',
    ['$scope', 'News',
        function ($scope, News)
        {
            News.orders({}, function (res)
            {
                $scope.news = res.data;
            });
        }]);

lwsControllers.controller('UserCtrl',
    ['$scope', 'User', '$stateParams', '$dialogs',
        function ($scope, User, $stateParams, $dialogs)
        {
            User.get({id: $stateParams.userId},
                function (resource)
                {
                    $scope.user = resource.data;
                });

            $scope.medalDrop = function (element, medal)
            {
                medal.top = element[0].offsetTop +3;
                medal.left = element[0].offsetLeft;
                medal.userId = $scope.user.id;
                User.saveMedalPosition(medal, function(res){
                });
            };

            $scope.addEvent = function (userId)
            {
                var event = {date: '', text: '', id: -1, userId: userId, isNew: true}
                $scope.editEvent(event);
            };

            $scope.editEvent = function (event)
            {
                var dlg = $dialogs.create('eventDialogTmpl', 'EventDialogCtrl', event,
                    {key: false, back: 'static'})
                    .result.then(function (newEvent)
                    {
                        angular.extend(event, newEvent);
                        if (newEvent && event.isNew)
                        {
                            $scope.user.events.push(newEvent);
                        }
                    }, function ()
                    {

                    });
            };


            $scope.deleteEvent = function (event)
            {
                $dialogs.confirm('Подтвердите', 'Удалить событие "' + event.text + '"?')
                    .result.then(function (btn)
                    {
                        $scope.user.events.splice($scope.user.events.indexOf(event), 1);
                        User.deleteEvent({eventId: event.id}, function (resource)
                        {

                        });
                    });
            }

            $scope.sync = function()
            {
                User.sync({id:$scope.user.id},function(){

                });
            }
        }]);


lwsControllers.controller('EditUserCtrl',
    ['$scope', 'User', '$stateParams', '$dialogs','dateFilter',
        function ($scope, User, $stateParams, $dialogs, dateFilter)
        {
            User.get({id: $stateParams.userId, noMedals:true},
                function (resource)
                {
                    $scope.user = resource.data;
                    $scope.user.birthDate = dateFilter($scope.user.birthDate, 'yyyy-MM-dd');
                    angular.extend($scope.tsSelect2Options.data, $scope.user.possibleUsers);
                });

            function formatTs(pilot)
            {
                var out = '';
                $.each(pilot.serverGroups, function (i, val) {
                    out+='<img src="/img/groups/'+val+'.png"> '
                });
                out += pilot.name;
                if (pilot.isOnline)
                    out +=' <b>[онлайн]</b>';
                if (pilot.byIp)
                    out +=' <b>[по ip]</b>';
                else if (!pilot.isOnline && pilot.byName)
                    out +=' <b>[по имени]</b>';
                return out;
            }

            $scope.tsSelect2Options = {
                data:[],
                formatResult: formatTs,
                formatSelection: formatTs,
                escapeMarkup: function (m) { return m; }
            };



            $scope.sync = function()
            {
                User.sync({id:$scope.user.id},function(){

                });
            };

            $scope.save = function()
            {
                var user = {};
                angular.extend(user, $scope.user);
                if (user.ts_id.hasOwnProperty('id'))
                  user.ts_id = user.ts_id.id;
                User.update({user:user}, function(res){

                })
            }

        }]);


lwsControllers.controller('BarracksCtrl',
    ['$scope', 'User', '$stateParams', '$timeout',
        function ($scope, User, $stateParams, $timeout)
        {
            $scope.dataSize = 1;
            $scope.filters = {name: null, which: 0};
            $scope.headings = [
                'на службе',
                'в отпуске',
                'дезертиры',
                'исключены'
            ];
            var firstLoad = true;

            $scope.loadData = function ()
            {
                $scope.isLoading = true;
                User.query({filters: $scope.filters}, function (resource)
                {
                    $scope.pilots = resource.data;
                    firstLoad = false;
                    $scope.isLoading = false;
                });
            };
            var timeoutPromise = null;

            $scope.$watch('filters', function (oldValue, newValue)
            {
                if (firstLoad)
                    return;
                if ((oldValue.name != newValue.name))
                {
                    $timeout.cancel(timeoutPromise);
                    timeoutPromise = $timeout(function ()
                    {
                        $scope.pilots = {};
                        $scope.loadData();
                    }, 600);
                }
                else {
                    $scope.pilots = {};
                    $scope.loadData();
                }
            }, true);
            $scope.loadData();
        }]);

lwsControllers.controller('RosterUserCtrl',
    ['$scope', 'User', '$stateParams', '$rootScope', '$dialogs', '$state',
        function ($scope, User, $stateParams, $rootScope, $dialogs, $state)
        {
            User.getRoster({userId: $stateParams.userId},
                function (resource)
                {
                    $scope.pilot = resource.data;
                    angular.extend($scope.tsSelect2Options.data, $scope.pilot.possibleUsers);
                });

            function formatTs(pilot)
            {
                var out = '';
                $.each(pilot.serverGroups, function (i, val) {
                    out+='<img src="/img/groups/'+val+'.png"> '
                });
                out += pilot.name;
                if (pilot.isOnline)
                    out +=' <b>[онлайн]</b>';
                if (pilot.byIp)
                    out +=' <b>[по ip]</b>';
                else if (!pilot.isOnline && pilot.byName)
                    out +=' <b>[по имени]</b>';
                return out;
            }

            $scope.tsSelect2Options = {
                data:[],
                formatResult: formatTs,
                formatSelection: formatTs,
                escapeMarkup: function (m) { return m; }
            };

            $scope.accept = function ()
            {
                $dialogs.confirm('Подтвердите', 'Принять данную заявку?')
                    .result.then(function (btn)
                    {
                        $scope.rosterForm.isSubmitting = true;
                        var uid = ($scope.rosterForm.tsId.hasOwnProperty('id')) ? $scope.rosterForm.tsId.id : $scope.rosterForm.tsId;
                        User.accept({userId: $stateParams.userId, uid: uid}, function ()
                        {
                            $scope.rosterForm.isSubmitting = false;
                            $rootScope.$broadcast('refreshRosterList');
                            $scope.pilot.rank = 7;
                        });
                    }, function (btn)
                    {
                    });
            };
            $scope.reject = function ()
            {
                var dlg = $dialogs.create('rejectDialogTmpl', 'RejectDialogCtrl', {}, {key: false, back: 'static'});
                dlg.result.then(function (reason)
                {
                    $scope.rosterForm.isSubmitting = true;
                    User.reject({userId: $stateParams.userId, reason:reason}, function ()
                    {
                        $scope.rosterForm.isSubmitting = false;
                        $rootScope.$broadcast('refreshRosterList');
                        $state.go('news');
                    });
                }, function (reason)
                {

                });
            };
        }]);

lwsControllers.controller('RecoverUserCtrl',
    ['$scope', 'User', '$stateParams', '$rootScope', '$dialogs', '$state',
        function ($scope, User, $stateParams, $rootScope, $dialogs, $state)
        {
            $scope.recoverForm = {};
            $scope.recovery = {token:$stateParams.token, password:null};
            $scope.recoverForm.isSubmitting = true;
            $scope.recoverForm.error = false;

            User.checkRecoveryToken({token:$stateParams.token}, function(res){
                    if (res.data.result != 'OK')
                        $state.go('news');
                },
            function(){
                $state.go('news');
            });

            $scope.recover = function(){
                $scope.recoverForm.isSubmitting = true;
                User.recoverPassword({token:$scope.recovery.token,password:$scope.recovery.password}, function(res){
                    if (res.data && res.data.id) {
                        $rootScope.$broadcast('refreshUserLogin');
                        $scope.UserIdentity = res.data;
                        $scope.registerForNotifications();
                        $state.go('news');
                    }
                }, function(res){
                    $scope.recoverForm.isSubmitting = false;
                    $scope.recoverForm.error = res.data.message
                });
                $scope.recovery.password = '';
            };
        }]);

lwsControllers.controller('RosterCtrl',
    ['$scope', 'User', '$stateParams' ,'$rootScope',
        function ($scope, User, $stateParams, $rootScope)
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
                    $rootScope.$broadcast('refreshUserLogin');
                    $scope.UserIdentity = resource.data;
                    $scope.registerForNotifications();
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

lwsControllers.controller('OrderCreatorCtrl',
    ['$scope', 'OrderGenerator', '$stateParams', '$sce', '$compile' , '$filter',
        function ($scope, OrderGenerator, $stateParams, $sce, $compile, $filter)
        {
            $scope.initialData = {pilots: {}};
            $scope.orderData = {pilots: []};
            $scope.updatedData = {pilots: {}};

            function formatPilot(state)
            {
                if (!state.id) return state.text; // optgroup
                var rank = $(state.element[0]).data('rankid');
                return '<img class="ts_group_icon" src="/img/groups/' + rank + '.png"><span> ' + state.text + "</span>";
            }

            $scope.$watch('orderData.pilots', function (current, last)
            {
                angular.forEach($scope.updatedData.pilots, function (value, key)
                {
                    key += '';
                    if (!current)
                        $scope.updatedData.pilots = {};

                    if (current && current.indexOf(key) == -1)
                        delete $scope.updatedData.pilots[key];
                }, this);
                angular.forEach(current, function (value, key)
                {
                    value += '';
                    if (!$scope.updatedData.pilots.hasOwnProperty(value))
                    {
                        $scope.updatedData.pilots[value] = {};
                        angular.extend($scope.updatedData.pilots[value], $scope.initialData.pilots[value]);
                        $scope.updatedData.pilots[value].awards = [];
                    }
                }, this);
            }, true);

            function formatRank(state)
            {
                if (!state.id) return state.text; // optgroup
                return '<img class="ts_group_icon" src="/img/groups/'
                + state.id
                + '.png"> '
                + "<span> "
                + state.text
                + "</span>";
            }

            function formatAward(state)
            {
                if (!state.id) return state.text; // optgroup
                return '<img class="micro_award" src="/img/awards/'
                + state.id
                + '.png"> '
                + "<span> "
                + state.text
                + "</span>";
            }

            $scope.pilotSelect2Options = {
                formatResult: formatPilot,
                formatSelection: formatPilot
            };

            $scope.rankSelect2Options = {
                formatResult: formatRank,
                formatSelection: formatRank
            };

            $scope.instructorSelect2Options = {
                formatResult: formatRank,
                formatSelection: formatRank,
                allowClear: true
            };

            $scope.awardSelect2Options = {
                formatResult: formatAward,
                formatSelection: formatAward
            };

            function reloadData()
            {
                $scope.initialData = {pilots: {}};
                $scope.orderData = {pilots: []};
                var time = new Date();
                time = $filter('date')(time, "yyyy-MM-dd");

                $scope.updatedData = {pilots: {}, time: time, customText: ''};

                OrderGenerator.get({}, function (resource)
                {
                    angular.extend($scope.initialData, resource.data);
                    var intArray = [];
                    angular.forEach($scope.initialData.pilots, function (value, key)
                    {
                        intArray.push(value);
                    });
                    $scope.initialData.pilotsArray = intArray;
                    intArray = [];

                    angular.forEach($scope.initialData.ranks, function (value, key)
                    {
                        intArray.push(value);
                    })
                    $scope.initialData.rankArray = intArray;
                    intArray = [];
                    angular.forEach($scope.initialData.instructors, function (value, key)
                    {
                        intArray.push(value);
                    })
                    $scope.initialData.instructorsArray = intArray;
                });
            }

            reloadData();

            $scope.save = function ()
            {
                $scope.orderData.isSubmitting = true;
                OrderGenerator.save({data: angular.extend({complete: $scope.orderData.complete}, $scope.updatedData)},
                    function (resource)
                    {
                        reloadData();
                    });
            };

            $scope.$watch('updatedData', function ()
            {
                var pilotTexts = [];
                angular.forEach($scope.updatedData.pilots, function (pilot, key)
                {
                    var pilotname = '';

                    var ranktext = '';
                    var afterranktext = '';

                    var instrtext = '';

                    var awardtext = '';

                    if (pilot.old_rank == 7 || pilot.old_rank == 11 || pilot.old_rank == 12)
                    {
                        pilotname =
                            '<a rank="'
                            + pilot.old_rank
                            + '">Курсант '
                            + pilot.rank_name
                            + '</a> <a pilot="'
                            + pilot.id
                            + '">'
                            + pilot.nickname
                            + '</a>';
                        afterranktext = ' в связи с успешной сдачей экзаменов';
                    }
                    else
                        pilotname =
                            '<a rank="'
                            + pilot.old_rank
                            + '">'
                            + pilot.rank_name
                            + '</a> <a pilot="'
                            + pilot.id
                            + '">'
                            + pilot.nickname
                            + '</a>';

                    if (pilot.old_rank != pilot.rank)
                    {
                        var oldOrder = $scope.initialData.ranks[pilot.old_rank].order;
                        var newOrder = $scope.initialData.ranks[pilot.rank].order;
                        var rankuptext = '';
                        if (oldOrder > newOrder)
                            rankuptext = ' понижен до ';
                        else
                            rankuptext =
                                ' присвоено ' + (newOrder - oldOrder > 1 ? 'внеочередное' : 'очередное') + ' звание ';

                        if (pilot.old_rank == 5)
                            rankuptext = ' принят на';

                        if (pilot.rank == 7 || pilot.rank == 11 || pilot.rank == 12)
                            rankuptext = ' переведен на ';
                        if (pilot.old_rank == 12)
                            rankuptext = ' переведён в офицерский состав и' + rankuptext;
                        ranktext =
                            rankuptext
                            + '<a rank="'
                            + pilot.rank
                            + '">'
                            + $scope.initialData.ranks[pilot.rank].name
                            + '</a>';
                    }

                    if (pilot.old_instructor != pilot.instructor)
                    {
                        var instruptext = ' присвоена должность ';
                        instrtext =
                            instruptext + $sce.trustAsHtml('<a rank="'
                            + pilot.instructor
                            + '">'
                            + $scope.initialData.instructors[pilot.instructor].name
                            + '</a>');
                    }

                    if (pilot.awards.length)
                    {
                        var awards = [];
                        angular.forEach(pilot.awards, function (awardId, key)
                        {
                            var name = $scope.initialData.awards[awardId].sub_name;
                            //name = name.replace('Медаль','медалью').replace('Крест','крестом').replace('Знак','знаком').replace('Шпанга','шпангой').replace('Лента','лентой');
                            awards.push('<a award="' + awardId + '">' + name);
                            +'</a>';
                        });
                        awardtext = 'награждается ' + awards.join(', ');
                    }

                    if (ranktext || awardtext || instrtext || $scope.updatedData.event || $scope.updatedData.customText)
                    {
                        var pilotText = '';
                        pilotText += pilotname;
                        if (ranktext)
                            pilotText += ranktext + afterranktext;
                        if (instrtext)
                            pilotText += ((ranktext) ? ' и ' : ' ') + instrtext;
                        if (awardtext)
                            pilotText += ((ranktext || instrtext) ? ' и ' : ' ') + awardtext;

                        pilotTexts.push('<p>' + pilotText + '</p>');
                    }
                });
                var text = ($scope.updatedData.event ? $scope.updatedData.event + '\n' : '') + pilotTexts.join('');
                text += $scope.updatedData.customText ? ' ' + $scope.updatedData.customText : '';
                $scope.orderData.complete = text;
                var linkingFunction = $compile('<div>' + text + '</div>');
                var elem = linkingFunction($scope);
                var rootElem = document.getElementById('completeData');
                rootElem.innerHTML = null;
                if (elem[0])
                    rootElem.appendChild(elem[0]);
            }, true);

        }]);

lwsControllers.controller("TSViewCtrl", ['$scope','User','$state','$dialogs', function ($scope, User, $state, $dialogs)
{
    $scope.tree = [];
    if ($scope.io_socket)
        $scope.io_socket.on('ts_clients', function (data) {
            if (data) {
                $scope.tree = data;
                $scope.$apply();
            }
        });

    $scope.getByUid = function (uid) {
        User.getIdFromUid({uid: uid}, function (res) {
            if (res.data.id) {
                $state.go('user', {userId: res.data.id});
            }
            else
                $dialogs.notify('Этого пользователя нету в базе','Наверное его ТС ещё не подключен к сайту');
        })
    }

}]);

lwsControllers.controller("AfterRosterCtrl", ['$scope', function ($scope)
{


}]);

lwsControllers.controller("NewsCreatorCtrl", ['$scope', 'News', '$stateParams', function ($scope, News, $stateParams)
{
    $scope.newsRecord = {};
    if ($stateParams.id)
    {
        $scope.newsRecord.id = $stateParams.id;
        $scope.newsRecord.isSubmitting = true;
        News.get({id:$stateParams.id}, function(res){
            $scope.newsRecord = res.data;
            $scope.newsRecord.isSubmitting = false;
            $scope.newsRecord.onlyRegistered = !!$scope.newsRecord.onlyRegistered;
        });
    }
    $scope.save = function ()
    {
        $scope.newsRecord.isSubmitting = true;
        News.save($scope.newsRecord, function (res)
        {
            $scope.newsRecord = res.data;
            $scope.newsRecord.isSubmitting = false;
        });
    }
}]);

lwsControllers.controller("RosterViewCtrl", ['$scope', '$timeout', 'Roster', function ($scope, $timeout, Roster)
{
    var rosterTimeout = null;
    var refreshRosterView = function ()
    {
        $timeout.cancel(rosterTimeout);
        Roster.get({}, function (res)
        {
            $scope.roster = res.data;
            rosterTimeout = $timeout(refreshRosterView, 60* 5 * 1000);
        });
    };

    $timeout(refreshRosterView, 20);

    $scope.$on('refreshRosterList', function (event, args)
    {
        refreshRosterView();
    });

}]);

lwsControllers.controller("BirthdayViewCtrl", ['$scope', '$timeout', 'User', function ($scope, $timeout, User)
{
    var rosterTimeout = null;
    var refreshBirthdayView = function ()
    {
        $timeout.cancel(rosterTimeout);
        User.getbirthdays({}, function (res)
        {
            $scope.birthdays = res.data;
            rosterTimeout = $timeout(refreshBirthdayView, 720 * 1000);
        });
    };
    $timeout(refreshBirthdayView, 20);
    $scope.$on('refreshRosterList', function (event, args)
    {
        refreshBirthdayView();
    });
}]);


lwsControllers.controller("UserMarksCtrl",
    ['$scope',
        '$stateParams',
        'User',
        '$dialogs',
        function ($scope, $stateParams, User, $dialogs)
        {
            $scope.user = {};

            User.getMarks({userId: $stateParams.userId}, function (resource)
            {
                $scope.tabs = {activeTab: 'fighter'};
                angular.extend($scope.user, resource.data);
                if (!$scope.user.programs.fighter)
                    $scope.tabs.activeTab = 'bomber';
            });

            $scope.mark = function (subjectId, courseId)
            {
                var mark = ($scope.user.marks[courseId] && $scope.user.marks[courseId][subjectId]) ?
                    $scope.user.marks[courseId][subjectId].mark :
                    0;
                var dlg = $dialogs.create('markDialogTmpl', 'MarkDialogCtrl', {
                    mark: mark,
                    subjectId: subjectId,
                    userId: $scope.user.id
                }, {key: false, back: 'static'});
                dlg.result.then(function (mark)
                {
                    if (!$scope.user.marks[courseId])
                        $scope.user.marks[courseId] = {};
                    if (!$scope.user.marks[courseId][subjectId])
                        $scope.user.marks[courseId][subjectId] = {};
                    $scope.user.marks[courseId][subjectId].mark = mark.mark
                }, function ()
                {

                });
            };

            $scope.promote = function (courseId)
            {
                if (courseId == 3)
                {
                    $dialogs.create('promoteDialogTmpl', 'PromoteDialogCtrl', {is_clanner: $scope.user.is_clanner}, {key: false, back: 'static'})
                        .result.then(function (toOfficer)
                        {
                            User.promote({userId: $scope.user.id, courseId: courseId, promoteToOfficer: toOfficer}, function (resource)
                            {
                                angular.extend($scope.user, resource.data);
                            });
                        }, function ()
                        {

                        });
                }
                else
                {
                    $dialogs.confirm('Подтвердите', 'Продвинуть курсанта на следующий курс?')
                        .result.then(function (btn)
                        {
                            User.promote({userId: $scope.user.id, courseId: courseId, promoteToOfficer: false}, function (resource)
                            {
                                angular.extend($scope.user, resource.data);
                            });
                        }, function (btn)
                        {
                        });
                }
            };


            $scope.$watch('user.marks', function ()
            {
                if (!$scope.user) return;
                angular.forEach($scope.user.programs, function (program, programKey)
                {
                    angular.forEach(program.courses, function (course, key)
                    {
                        var count = 0;
                        var average = 0;
                        angular.forEach($scope.user.marks[course.id], function (mark, key2)
                        {
                            count++;
                            average += parseInt(mark.mark);
                        }, this);

                        $scope.user.programs[programKey].courses[key].average = count ? average / count : 0;
                        $scope.user.programs[programKey].courses[key].complete = (count == course.subjects.length);
                    }, this);
                }, this);
            }, true);

        }]);

lwsControllers.controller('MarkDialogCtrl',
    ['$scope', '$modalInstance', 'data', 'User', '$dialogs',
        function ($scope, $modalInstance, data, User, $dialogs)
        {
            $scope.userMark = {};
            angular.extend($scope.userMark, data);

            $scope.cancel = function ()
            {
                $modalInstance.dismiss('canceled');
            }; // end cancel

            $scope.saveMark = function ()
            {
                $scope.userMark.isSubmitting = true;
                User.saveMark($scope.userMark, function (resource)
                    {
                        $modalInstance.close($scope.userMark);
                    },
                    function (resource)
                    {
                        $modalInstance.close($scope.userMark);
                    });
            }; // end save
        }]);


lwsControllers.controller('EventDialogCtrl',
    ['$scope', '$modalInstance', 'data', 'User',
        function ($scope, $modalInstance, data, User)
        {
            $scope.event = {};
            angular.extend($scope.event, data);

            $scope.cancel = function ()
            {
                $modalInstance.dismiss('canceled');
            }; // end cancel

            $scope.saveEvent = function ()
            {
                $scope.event.isSubmitting = true;
                User.saveEvent($scope.event, function (resource)
                    {
                        $scope.event.isSubmitting = false;
                        $modalInstance.close(resource.data);
                    },
                    function (resource)
                    {
                        $scope.event.isSubmitting = false;
                        $modalInstance.close(resource.data);
                    });
            }; // end save
        }]);


lwsControllers.controller('PromoteDialogCtrl',
    ['$scope', '$modalInstance', 'data',
        function ($scope, $modalInstance, data)
        {
            $scope.is_clanner = data.is_clanner;
            $scope.cancel = function ()
            {
                $modalInstance.dismiss('canceled');
            }; // end cancel

            $scope.promote = function (promoteToOfficer)
            {
                $modalInstance.close(promoteToOfficer);
            }; // end save
        }]);

lwsControllers.controller('RejectDialogCtrl',
    ['$scope', '$modalInstance', 'data',
        function ($scope, $modalInstance, data)
        {
            $scope.reject = {
                text: data.text || '',
                header: data.header || 'отклонения заявки',
                buttonText: data.buttonText || 'Отклонить заявку'
            };
            $scope.cancel = function ()
            {
                $modalInstance.dismiss('canceled');
            }; // end cancel

            $scope.saveReject = function ()
            {
                $modalInstance.close($scope.reject.text);
            }; // end save
        }]);


lwsControllers.controller('SchoolCtrl',
    ['$scope', 'School', '$location', '$anchorScroll','$stateParams',
        function ($scope, School, $location, $anchorScroll, $stateParams)
        {
            $scope.scrollTo = function (id)
            {
                $location.hash(id);
                $anchorScroll();
            };

            $scope.sceditor = {text: ''};
            School.materials({slug:$stateParams.slug}, function (res)
            {
                $scope.subject = res.data;
                setTimeout($anchorScroll, 500);
            });
        }]);


lwsControllers.controller('EditMaterialCtrl',
    ['$scope', 'Material', '$stateParams',
        function ($scope, Material, $stateParams)
        {
            $scope.sceditor = {text: ''};
            $scope.material = {};
            if ($stateParams.materialId > 0)
            {
                Material.get({id: $stateParams.materialId}, function (res)
                {
                    angular.extend($scope.material, res.data);
                    $scope.sceditor.text = $scope.material.text;
                    $scope.material.isLoaded = true;
                });
            }
            else
            {
                $scope.material.id = -1;
                $scope.material.isLoaded = true;
            }
            $scope.save = function ()
            {
                $scope.material.text = $scope.sceditor.text;
                $scope.material.isLoaded = false;
                $scope.material.slug = $stateParams.slug;
                Material.save($scope.material, function (res)
                {
                    angular.extend($scope.material, res.data);
                    $scope.sceditor.text = $scope.material.text;
                    $scope.material.isLoaded = true;
                });
            }
        }]);


lwsControllers.controller('TextCtrl',
    ['$scope', 'Text', '$location', '$stateParams',
        function ($scope, Text, $location, $stateParams)
        {
            Text.get({id: $stateParams.id}, function (res)
            {
                $scope.text = res.data;
            });
        }]);


lwsControllers.controller('EditTextCtrl',
    ['$scope', 'Text', '$stateParams',
        function ($scope, Text, $stateParams)
        {
            $scope.sceditor = {text: ''};
            $scope.material = {};
            if ($stateParams.id)
            {
                Text.edit({id: $stateParams.id}, function (res)
                {
                    angular.extend($scope.material, res.data);
                    $scope.sceditor.text = $scope.material.text;
                    $scope.material.isLoaded = true;
                });
            }
            else
            {
                $scope.material.id = -1;
                $scope.material.isLoaded = true;
            }
            $scope.save = function ()
            {
                $scope.material.text = $scope.sceditor.text;
                $scope.material.isLoaded = false;
                Text.save($scope.material, function (res)
                {
                    angular.extend($scope.material, res.data);
                    $scope.sceditor.text = $scope.material.text;
                    $scope.material.isLoaded = true;
                });
            }
        }]);

lwsControllers.controller('FloodCtrl',
    ['$scope', 'Flood',
        function ($scope, Flood)
        {
            Flood.query({}, function (res)
            {
                $scope.topics = res.data;
            });
        }]);

lwsControllers.controller('TopicCtrl',
    ['$scope', 'Topic', '$location', '$stateParams', '$state',
        function ($scope, Topic, $location, $stateParams, $state)
        {
            $scope.topic = {currentPage: 1};
            var lastNewId = 1;
            $scope.sceditor = {text: ''};
            Topic.get({topicId: $stateParams.topicId}, function (res)
            {
                angular.extend($scope.topic, res.data);
            });
            $scope.$watch('topic.currentPage', function (newValue, oldValue)
            {
                if (oldValue != newValue)
                    $state.go('topic.page', {topicId: $stateParams.topicId, page: newValue});
            });
            $scope.post = function ()
            {
                var message = {
                    id: 'new_' + lastNewId,
                    isNew: true,
                    author: $scope.UserIdentity
                };
                lastNewId++;

                $scope.topic.messages.push(message);
                Topic.post({topicId: $scope.topic.id, text: $scope.sceditor.text}, function (res)
                {
                    message.isNew = false;
                    angular.extend(message, res.data);
                    $scope.topic.itemCount++;
                }, function (res)
                {
                    $scope.topic.messages.splice($scope.topic.messages.indexOf(message), 1);
                });


                $scope.sceditor.text = '';
            }

        }]);

lwsControllers.controller('TopicPageCtrl',
    ['$scope', 'Topic', '$location', '$stateParams',
        function ($scope, Topic, $location, $stateParams)
        {
            $scope.isLoading = true;
            $scope.topic.currentPage = parseInt($stateParams.page);
            Topic.page({topicId: $stateParams.topicId, page: $stateParams.page}, function (res)
            {
                $scope.isLoading = false;
                $scope.topic.messages = res.data;
            });
        }]);

lwsControllers.controller("NewTopicCtrl", ['$scope', 'Topic', '$state', function ($scope, Topic, $state)
{
    $scope.sceditor = {text: ''};

    $scope.save = function ()
    {

        $scope.topicRecord.isSubmitting = true;
        Topic.save({title: $scope.topicRecord.title, text: $scope.sceditor.text}, function (resource)
        {
            $state.go('topic.page', {topicId: resource.data.id, page: 1});
        }, function (res)
        {
            $scope.topicRecord.isSubmitting = false;
        });
    }
}]);

lwsControllers.controller("ReportVacationCtrl", ['$scope', 'Vacation', '$state', function ($scope, Vacation, $state)
{
    $scope.vacation = {};
    $scope.save = function ()
    {
        Vacation.save($scope.vacation, function (resource)
        {
            $scope.vacationForm.isSubmitting = true;
            $scope.vacation = {isSaved:true};

        }, function (res)
        {
            $scope.vacationForm.isSubmitting = false;
        });
    }
}]);


lwsControllers.controller('MessengerCtrl',
    ['$scope', 'Messenger',
        function ($scope, Messenger)
        {
            Messenger.query({}, function (res)
            {
                $scope.conversations = res.data;
            });
            $scope.$on('new_message',function(event, data){


                $scope.conversations.messages.unshift(data);
                $scope.$apply();
            });

        }]);

lwsControllers.controller('ConversationCtrl',
    ['$scope', 'Messenger', '$location', '$stateParams', '$state',
        function ($scope, Messenger, $location, $stateParams, $state)
        {
            $scope.conversation = {currentPage: 1};
            var lastNewId = 1;
            $scope.sceditor = {text: ''};
            Messenger.get({senderId: $stateParams.senderId}, function (res)
            {
                angular.extend($scope.conversation, res.data);
            });
            $scope.$watch('conversation.currentPage', function (newValue, oldValue)
            {
                if (oldValue != newValue)
                    $state.go('conversation.page', {senderId: $stateParams.senderId, page: newValue});
            });


            function trimMessages()
            {
                var toTrim = $scope.conversation.messages.length - $scope.conversation.limit;
                if (toTrim > 0)
                    $scope.conversation.messages.splice($scope.conversation.messages.length-toTrim,toTrim);
            }

            $scope.post = function ()
            {
                var message = {
                    id: 'new_' + lastNewId,
                    isNew: true,
                    sender: $scope.UserIdentity
                };
                lastNewId++;

                $scope.conversation.messages.unshift(message);
                Messenger.post({recieverId: $scope.conversation.sender.id, text: $scope.sceditor.text}, function (res)
                {
                    message.isNew = false;
                    angular.extend(message, res.data);
                    $scope.conversation.itemCount++;
                    trimMessages()

                }, function (res)
                {
                    $scope.conversation.messages.splice($scope.conversation.messages.indexOf(message), 1);
                });
                $scope.sceditor.text = '';
            };
            $scope.$on('new_message',function(event, data){
                $scope.conversation.messages.unshift(data);
                $scope.conversation.itemCount++;
                trimMessages();
                $scope.$apply();
            });
        }]);

lwsControllers.controller('ConversationPageCtrl',
    ['$scope', 'Messenger', '$location', '$stateParams',
        function ($scope, Messenger, $location, $stateParams)
        {
            $scope.isLoading = true;
            $scope.conversation.currentPage = parseInt($stateParams.page);
            Messenger.page({senderId: $stateParams.senderId, page: $stateParams.page}, function (res)
            {
                $scope.isLoading = false;
                $scope.conversation.messages = res.data;
            });
        }]);


lwsControllers.controller("InactiveCountCtrl", ['$scope', '$timeout', 'User', function ($scope, $timeout, User)
{
    var inactiveCountTimeout = null;
    var refreshInactiveCount = function () {
        $timeout.cancel(inactiveCountTimeout);
        User.getInactiveCount({}, function (res) {
            $scope.count = res.data.count;
            inactiveCountTimeout = $timeout(refreshInactiveCount, 60 * 24 * 1000);
        });
    };
    $timeout(refreshInactiveCount, 70);
}]);

lwsControllers.controller("InactiveCtrl", ['$scope', 'User', '$dialogs', '$rootScope',
    function ($scope, User, $dialogs, $rootScope) {
        $scope.filters = {name: null};
        $scope.loadData = function () {
            $scope.isLoading = true;
            User.queryInactive({}, function (resource) {
                $scope.pilots = resource.data;
                $scope.isLoading = false;
            });
        };

        $scope.acquit = function(pilot){
            var dlg = $dialogs.create('acquitDialogTmpl', 'AcquitDialogCtrl', {pilot:pilot}, {key: false, back: 'static'});
            dlg.result.then(function (acquitConditions)
            {
                acquitConditions.pilotId = pilot.id;
                User.acquit(acquitConditions, function(resource){
                    $scope.loadData();
                });
            }, function ()
            {

            });
        };

        $scope.$on('pilotExpelled',function(event, data){
            $scope.loadData();
        });
        $scope.loadData();
    }]);

lwsControllers.controller('AcquitDialogCtrl',
    ['$scope', '$modalInstance', '$filter', 'data',
        function ($scope, $modalInstance, $filter, data) {
            $scope.pilot = data.pilot;
            $scope.acquit = {dateFrom: $filter("simpleTime")($scope.pilot.lastWarning), dateTo: $filter("simpleTime")(parseInt($scope.pilot.lastWarning) + 1000*60*60*24*30)};

            $scope.cancel = function () {
                $modalInstance.dismiss('canceled');
            }; // end cancel

            $scope.saveAcquit = function () {
                $modalInstance.close($scope.acquit);
            }; // end save
        }]);
