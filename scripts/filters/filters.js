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

lwsFilters.filter('timeAgo', function () {
    var time = new Date().getTime();
    var textRules = {
        'many':/(\d*[5-90])|11|12|13/,
        'one':/\d*[1]/,
        'some':/\d*[2-4]/
    };
    function i18(number, strings)
    {
        for (var i in textRules) {
            if (textRules[i].test(number))
                return strings[i];
        }
    }
    return function (input, noago) {
        var timediff = time - parseInt(input);
        var ago = !noago && timediff > 0;
        timediff = Math.abs(timediff);
        timediff = Math.round(timediff / 1000 / 3600);
        var timeString = '';
        var days = Math.round(timediff / 24 % 30)+'';
        var months = Math.round(timediff / 24 / 30)+'';
        if (months !='0') {
            timeString = months + ' '+i18(months,{'one':'месяц','some':'месяца','many':'месяцев'})+' и ';
        }
        if (days == '0' && months == '0')
            return 'сегодня';
        if (days == '1' && months == '0')
            return ago ? 'вчера' : 'завтра';
        timeString += days + ' ' + i18(days, {'one': 'день', 'some': 'дня', 'many': 'дней'});
        return timeString + (ago ? ' назад' : '');
    };
});

lwsFilters.filter('to_trusted', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}]);

lwsFilters.filter('simpleTime', function(){
    return function(text) {
        var date = new Date(parseInt(text));
        var day = date.getDate();
        var month = date.getMonth()+1;
        var year = date.getFullYear();
        if (day < 10)
            day = "0" + day;
        if (month < 10)
            month = "0" + month;
        return year + '-' + month + '-' + day;
    };
});