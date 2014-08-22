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

lwsServices.factory('School', ['$resource', function ($resource)
{
    return $resource('/news/item', {}, {
        materials: {
            url: '/school/materials',
            method: 'get'
        }
    });
}]);

lwsServices.factory('Material', ['$resource', function ($resource)
{
    return $resource('/school/material', {}, {});
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
        reject:{
            url: '/user/reject',
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
        },
        saveEvent:{
            url: '/user/saveEvent',
            method: 'post'
        },
        deleteEvent:{
            url: '/user/deleteEvent',
            method: 'post'
        },
        sync:{
            url: '/user/sync',
            method: 'post'
        },
        update:{
            url: '/user/update',
            method: 'post'
        },
        saveMedalPosition:{
            url: '/user/saveMedalPosition',
            method: 'post'
        },
        getIdFromUid:{
            url: '/user/getIdFromUid',
            method: 'get'
        },
        checkRecoveryToken:{
            url: '/user/checkRecoveryToken',
            method: 'get'
        },
        recoverPassword:{
            url: '/user/recoverPassword',
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

lwsServices.factory('Text', ['$resource', function ($resource)
{
    return $resource('/text/item', {}, {
        edit : {
            method:'GET',
            url:'/text/edit'
        },
        save : {
            method:'POST',
            url:'/text/edit'
        }
    });
}]);

lwsServices.factory('Flood', ['$resource', function ($resource)
{
    return $resource('/flood/list', {}, {
        query : {
            method:'GET'
        },
        save : {
            method:'POST'
        }
    });
}]);

lwsServices.factory('Topic', ['$resource', function ($resource)
{
    return $resource('/flood/topic', {}, {
        page : {
            method:'GET',
            url:'/flood/topicPage'
        },
        post : {
            method:'POST',
            url:'/flood/postMessage'
        }
    });
}]);


lwsServices.factory('Messenger', ['$resource', function ($resource)
{
    return $resource('/messenger/list', {}, {
        query : {
            method:'GET'
        },
        get : {
            method:'GET',
            url:'/messenger/conversation'
        },
        page : {
            method:'GET',
            url:'/messenger/conversationPage'
        },
        post : {
            method:'POST',
            url:'/messenger/postMessage'
        }
    });
}]);

lwsServices.factory('Vacation', ['$resource', function ($resource)
{
    return $resource('/user/vacation', {}, {});
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
