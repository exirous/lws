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

lwsServices.factory('BattleLog', ['$resource', function ($resource)
{
    return $resource('/battlelog/list', {}, {
        query:
        {
            url: '/battlelog/list',
            method: 'get'
        },
        get:
        {
            url: '/battlelog/get',
            method: 'get'
        },
        add:
        {
            url: '/battlelog/add',
            method: 'post'
        },
        save:
        {
            url: '/battlelog/save',
            method: 'post'
        },
        delete:
        {
            url: '/battlelog/delete',
            method: 'post'
        }
    });
}]);

lwsServices.factory('User', ['$resource', function ($resource)
{
    return $resource('/user/item', {}, {
        getInactiveCount:
        {
            url: '/user/inactiveCount',
            method: 'get'
        },
        queryInactive:
        {
            url: '/user/inactive',
            method: 'get'
        },
        acquit:
        {
            url: '/user/acquit',
            method: 'post'
        },
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
        expel:{
            url: '/user/expel',
            method: 'post'
        },
        reenlist:{
            url: '/user/reenlist',
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
        },
        clearUpdate:{
            url: '/user/clearUpdate',
            method: 'post'
        },
        getPersonalFile:{
            url: '/user/personalFile',
            method: 'get'
        },
        savePersonalFile:{
            url: '/user/personalFile',
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
        },
        deleteTopic : {
            method:'POST',
            url:'/flood/deleteTopic'
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
        },
        deleteConversation : {
            method:'POST',
            url:'/messenger/deleteConversation'
        }
    });
}]);

lwsServices.factory('Vacation', ['$resource', function ($resource)
{
    return $resource('/user/vacation', {}, {});
}]);

lwsServices.factory('Awards', ['$resource', function ($resource)
{
    return $resource('/awards/list', {}, {
        query : {
            method:'GET'
        },
        get : {
            method:'GET',
            url:'/awards/get'
        },
        save: {
            method:'POST',
            url:'/awards/save'
        },
    });
}]);


lwsServices.factory('socket', function ($rootScope) {
    var socket = io.connect('/socket.io');
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
