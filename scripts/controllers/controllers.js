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
    ['$scope', '$dialogs', '$stateParams', 'User','$rootScope',


        function ($scope, $dialogs, $stateParams, User, $rootScope)
        {
            $scope.UserIdentity = UserLoginData;
            $scope.headerImages = ['/img/header/news.png'];
            var stateAliases = {
                'orders': 'news',
                'makeorder': 'news',
                'makenews': 'news',
                'roster': 'pilots',
                'rosterUser': 'user',
                'userMarks': 'user',
                'afterroster': 'user',
                'edittext': 'news',
                'editmaterial': 'school',
                'flood': 'news',
                'topic': 'news',
                'newtopic': 'news',
                'topic.page': 'news',
                'reportvacation': 'news',
                'vacation': 'news',
                'editUser': 'user'
            };
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
                    $rootScope.$broadcast('refreshUserLogin');
                    $scope.UserIdentity = user;
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
                    var date = new Date();
                    date.setTime($scope.user.birthDate);
                    $scope.user.birthDate = dateFilter($scope.user.birthDate, 'yyyy-MM-dd')
                });

            $scope.sync = function()
            {
                User.sync({id:$scope.user.id},function(){

                });
            };

            $scope.save = function()
            {
                User.update({user:$scope.user}, function(res){

                })
            }

        }]);


lwsControllers.controller('BarracksCtrl',
    ['$scope', 'User', '$stateParams', '$timeout',
        function ($scope, User, $stateParams, $timeout)
        {
            $scope.filters = {name: null};
            var firstLoad = true;

            $scope.loadData = function ()
            {
                $scope.isLoading = true
                User.query({filters: $scope.filters}, function (resource)
                {
                    $scope.pilots = resource.data;
                    firstLoad = false;
                    $scope.isLoading = false;
                });
            };
            var timeoutPromise = null;
            $scope.$watch('filters', function ()
            {
                if (firstLoad) return;
                $timeout.cancel(timeoutPromise);
                timeoutPromise = $timeout(function ()
                {
                    $scope.loadData();
                }, 600);
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
                });
            $scope.accept = function ()
            {
                $dialogs.confirm('Подтвердите', 'Принять данную заявку?')
                    .result.then(function (btn)
                    {
                        $scope.rosterForm.isSubmitting = true;
                        User.accept({userId: $stateParams.userId, uid: $scope.rosterForm.tsId}, function ()
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
                $dialogs.confirm('Подтвердите', 'Отклонить данную заявку?')
                    .result.then(function (btn)
                    {
                        $scope.rosterForm.isSubmitting = true;
                        User.reject({userId: $stateParams.userId}, function ()
                        {
                            $scope.rosterForm.isSubmitting = false;
                            $rootScope.$broadcast('refreshRosterList');
                            $state.go('news');
                        });
                    }, function (btn)
                    {
                    });
            };
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
    $timeout(refreshTsView, 20);
    $scope.tree = [];
}]);

lwsControllers.controller("AfterRosterCtrl", ['$scope', function ($scope)
{

}]);

lwsControllers.controller("NewsCreatorCtrl", ['$scope', 'News', function ($scope, News)
{
    $scope.save = function ()
    {
        $scope.newsRecord.newsAdded = false;
        $scope.newsRecord.isSubmitting = true;
        News.save($scope.newsRecord, function (resource)
        {
            $scope.newsRecord.text = '';
            $scope.newsRecord.title = '';
            $scope.newsRecord.onlyRegistered = false;
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

    $timeout(refreshRosterView, 20);

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
    $timeout(refreshBirthdayView, 20);
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

lwsControllers.controller('SchoolCtrl',
    ['$scope', 'School', '$location', '$anchorScroll',
        function ($scope, School, $location, $anchorScroll)
        {
            $scope.scrollTo = function (id)
            {
                $location.hash(id);
                $anchorScroll();
            };


            $scope.sceditor = {text: ''};
            School.materials({}, function (res)
            {
                $scope.materials = res.data;
                setTimeout($anchorScroll, 500);
            });
        }]);


lwsControllers.controller('EditMaterialCtrl',
    ['$scope', 'Material', '$stateParams',
        function ($scope, Material, $stateParams)
        {
            $scope.sceditor = {text: ''};
            $scope.material = {};
            if ($stateParams.materialId)
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
