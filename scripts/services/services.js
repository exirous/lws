var lwsServices = angular.module('app.services', ['ngResource'])

lwsServices.factory('News', ['$resource', function ($resource)
{
    return $resource('/news/item', {}, {
        last: {
            url: '/news/last',
            method: 'get'
        },
        orders: {
            url: '/news/lastOrders',
            method: 'get'
        }

    });
}]);

lwsServices.factory('TeamSpeak', ['$resource', function ($resource)
{
    return $resource('/teamSpeak/list', {}, {
        tree: {
            url: '/teamSpeak/viewTree',
            method: 'get'
        }
    });
}]);

lwsServices.factory('User', ['$resource', function ($resource)
{
    return $resource('/user/item', {}, {
        getbirthdays:
        {
            url: '/user/birthdays',
            method: 'get'
        },
        query: {
            method: 'get'
        },
        login: {
            url: '/user/login',
            method: 'post'
        },
        logout: {
            url: '/user/logout',
            method: 'post'
        },
        roster:{
            url: '/user/roster',
            method: 'post'
        },
        getRoster:{
            url: '/user/getRoster',
            method: 'get'
        },
        accept:{
            url: '/user/accept',
            method: 'post'
        },
        recover:{
            url: '/user/recover',
            method: 'post'
        },
        getMarks:{
            url: '/user/getMarks',
            method: 'get'
        },
        saveMark:{
            url: '/user/saveMark',
            method: 'post'
        },
        promote:{
            url: '/user/promote',
            method: 'post'
        }
    });
}]);

lwsServices.factory('OrderGenerator', ['$resource', function ($resource)
{
    return $resource('/order/item', {}, {

    });
}]);
lwsServices.factory('Roster', ['$resource', function ($resource)
{
    return $resource('/user/getRoster', {}, {

    });
}]);

lwsServices.factory('socket', function ($rootScope) {
    var socket = io.connect('http://lws.exirous.com:3000');
    return {
        on: function (eventName, callback) {
            socket.on(eventName, function () {
                var args = arguments;
                $rootScope.$apply(function () {
                    callback.apply(socket, args);
                });
            });
        },
        emit: function (eventName, data, callback) {
            socket.emit(eventName, data, function () {
                var args = arguments;
                $rootScope.$apply(function () {
                    if (callback) {
                        callback.apply(socket, args);
                    }
                });
            })
        }
    };
});
