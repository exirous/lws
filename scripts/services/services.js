var lwsServices = angular.module('app.services', ['ngResource'])

lwsServices.factory('News', ['$resource', function ($resource)
{
    return $resource('/news/item', {}, {
        last: {
            url: '/news/last',
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
        recover:{
            url: '/user/recover',
            method: 'post'
        }
    });
}]);

lwsServices.factory('OrderGenerator', ['$resource', function ($resource)
{
    return $resource('/order/item', {}, {

    });
}]);
