var lwsDirectives = angular.module('app.directives', []);
lwsDirectives.directive("rank", function ()
{
    return function (scope, element, attrs)
    {
        console.log("rank!");
    }
});
lwsDirectives.directive("pilot", function ()
{
    return function (scope, element, attrs)
    {
        element.attr('href','/#/user/view/'+attrs.pilot);
    }
});

lwsDirectives.directive("award", function ()
{
    return function (scope, element, attrs)
    {
        console.log("award!");
    }
});


lwsDirectives.directive('fileUploadBox', ['$fileUploader', function ($fileUploader)
{
    return {
        restrict: 'A',
        replace: false,
        templateUrl: 'fileUploadBoxTemplate',
        link: function ($scope, element, attrs)
        {
            var uploader = $fileUploader.create({
                scope: $scope,
                autoUpload: true,
                url: '/user/upload/',
                removeAfterUpload: true
            });
            $scope.uploadItem = {};

            $scope.$watch('user',function(){
                if ($scope.user)
                  uploader.formData = [{userId:$scope.user.id}]
            });

            uploader.bind('progress', function (event, item, progress)
            {
                $scope.uploadItem.progress = progress;
            });
            uploader.bind('beforeupload', function (event, item)
            {
                $scope.uploadItem.progress = 0;
                $scope.uploadItem.isUploading = true;
                $scope.uploadItem.isError = false;
            });
            uploader.bind('complete', function (event, xhr, item, response)
            {
                if (item.isError)
                    alert("Ошибка при загрузке файла: " +  response.message);
                $scope.uploadItem.isUploading = false;
                $scope.user.img_src = response.data;
                $scope.$apply();
            });
        }
    }
}]);

lwsDirectives.directive('sceditor', [function ()
{
    return {
        restrict: 'A',
        replace: false,
        link: function ($scope, element, attrs)
        {
            $(element).sceditor({
                plugins: "bbcode",
                resizeEnabled:false,
                style: "/scripts/sceditor/minified/jquery.sceditor.default.min.css"
            });
            var editor = $(element).sceditor('instance');

            $scope.$watch('sceditor.text', function (newVal, oldVal)
            {
                if (newVal == editor.val()) return;
                editor.val(newVal);
            });

            var refreshScope = function (e)
            {
                $scope.sceditor.text = editor.val();
                $scope.$apply();
            }
            editor.selectionChanged(refreshScope);
            editor.bind('nodechanged blur', refreshScope);
        }
    }
}]);

lwsDirectives.directive("bindCompiledHtml", ['$compile',function($compile) {
    return {
        template: '<div></div>',
        scope: {
            rawHtml: '=bindCompiledHtml'
        },
        link: function(scope, elem, attrs) {
            scope.$watch('rawHtml', function(value) {
                if (!value) return;
                // we want to use the scope OUTSIDE of this directive
                // (which itself is an isolate scope).
                var newElem = $compile('<span>'+value+'</span>')(scope.$parent);
                elem.contents().remove();
                elem.append(newElem);
            });
        }
    };
}]);