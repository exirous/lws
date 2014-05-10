angular.module('app.controllers',
    ['ui.router',
        'app.directives',
        'ui.bootstrap.datepicker',
        'ui.bootstrap.buttons',
        'ui.bootstrap.accordion',
        'ui.bootstrap.timepicker',
        'ui.select2']);
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
            };
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
                        $dialogs.notify('Something Happened!', 'Something happened that I need to tell you.');
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
    ['$scope', 'News',
        function ($scope, News)
        {
            News.last({}, function (res)
            {
                $scope.news = res.data;
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
    ['$scope', 'User', '$stateParams',
        function ($scope, User, $stateParams)
        {
            User.get({id: $stateParams.userId},
                function (resource)
                {
                    $scope.user = resource.data;
                });
        }]);


lwsControllers.controller('BarracksCtrl',
    ['$scope', 'User', '$stateParams','$timeout',
        function ($scope, User, $stateParams, $timeout)
        {
            $scope.filters = {name:null};
            var firstLoad = true;

            $scope.loadData = function ()
            {
                $scope.isLoading = true
                User.query({filters:$scope.filters}, function (resource)
                {
                    $scope.pilots = resource.data;
                    firstLoad = false;
                    $scope.isLoading = false;
                });
            };
            var timeoutPromise = null;
            $scope.$watch('filters',function(){
                if (firstLoad) return;
                $timeout.cancel(timeoutPromise);
                timeoutPromise = $timeout(function(){
                    $scope.loadData();
                },600);
            },true);
            $scope.loadData();
        }]);

lwsControllers.controller('RosterUserCtrl',
    ['$scope', 'User', '$stateParams', '$rootScope', '$location',
        function ($scope, User, $stateParams, $rootScope, $location)
        {
            User.getRoster({userId: $stateParams.userId},
                function (resource)
                {
                    $scope.pilot = resource.data;
                });
            $scope.accept = function ()
            {
                $scope.rosterForm.isSubmitting = true;
                User.accept({userId: $stateParams.userId, uid: $scope.rosterForm.tsId}, function ()
                {
                    $scope.rosterForm.isSubmitting = false;
                    $rootScope.$broadcast('refreshRosterList');
                    $scope.pilot.rank = 7;
                });
            }
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

lwsControllers.controller('OrderCreatorCtrl',
    ['$scope', 'OrderGenerator', '$stateParams', '$sce', '$compile' ,'$filter',
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

                $scope.updatedData = {pilots: {}, time:time, customText:''};

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
                            awards.push('<a award="' + awardId + '">' + $scope.initialData.awards[awardId].name)
                            + '</a>';
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
                text += $scope.updatedData.customText ? ' '+$scope.updatedData.customText : '';
                $scope.orderData.complete = text;
                var linkingFunction = $compile('<div>' + text + '</div>');
                var elem = linkingFunction($scope);
                var rootElem = document.getElementById('completeData');
                rootElem.innerHTML = null;
                if (elem[0])
                    rootElem.appendChild(elem[0]);
            }, true);

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

lwsControllers.controller("NewsCreatorCtrl", ['$scope','News', function ($scope, News)
{
    $scope.save = function()
    {
        $scope.newsRecord.newsAdded = false;
        $scope.newsRecord.isSubmitting = true;
        News.save($scope.newsRecord, function (resource)
        {
            $scope.newsRecord.text = '';
            $scope.newsRecord.newsAdded = true;
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
            rosterTimeout = $timeout(refreshRosterView, 60 * 1000);
        });
    };
    refreshRosterView();
    $scope.$on('refreshRosterList', function (event, args)
    {
        refreshRosterView();
    });
    $scope.tree = [];

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
    refreshBirthdayView();
    $scope.$on('refreshRosterList', function (event, args)
    {
        refreshBirthdayView();
    });
    $scope.tree = [];

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
                angular.extend($scope.user, resource.data);
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
                $dialogs.confirm('Подтвердите', 'Продвинуть курсанта на следующий курс?')
                    .result.then(function (btn)
                    {
                        User.promote({userId: $scope.user.id, courseId: courseId}, function (resource)
                        {
                            angular.extend($scope.user, resource.data);
                        });
                    }, function (btn)
                    {
                    });
            };

            $scope.$watch('user.marks', function ()
            {
                if (!$scope.user) return;
                angular.forEach($scope.user.courses, function (course, key)
                {
                    var count = 0;
                    var average = 0;
                    angular.forEach($scope.user.marks[course.id], function (mark, key2)
                    {
                        count++;
                        average += parseInt(mark.mark);
                    }, this);
                    $scope.user.courses[key].average = count ? average / count : 0;
                    $scope.user.courses[key].complete = (count == course.subjects.length);
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
