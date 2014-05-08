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

lwsFilters.filter('age', function() {
    var time = new Date();

    return function(input) {
        input = parseInt(input);
        var out = 0;
        out = (time.getTime()-input) / 1000 / 3600 / 24 / 365;
        out = parseInt(out);
        return out;
    };
});

lwsFilters.filter('daysleft', function() {
    var time = new Date();
    return function(input) {
        //input = parseInt(input);
        //if (!input)
        //return '';
        var time2 = new Date();
        time2.setTime(input);
        time2.setYear(time.getFullYear());
        if (time2 < time)
          time2.setYear(time.getFullYear()+1);
        var out = 0;
        out = (time2.getTime()- time.getTime()) / 1000 / 3600 / 24;
        out = parseInt(out);
        return out;
    };
});
