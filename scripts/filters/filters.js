var lwsFilters = angular.module('app.filters', []);
lwsFilters.filter('clearNickname', function ()
{
    return function (input)
    {
        return input.replace(/\((.*?)\)/, '');
    };
});
lwsFilters.filter('avatarUrl', function ()
{
    return function (input)
    {
        return input ? '/img/users/' + input + '.jpg' : '';// input.replace(/\((.*?)\)/, '');
    };
});
