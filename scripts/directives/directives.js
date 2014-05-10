var lwsDirectives = angular.module('app.directives', []);
lwsDirectives.directive("rank", function(){
    return function(scope, element, iAttrs) {
        //console.log('!!');
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
                    autoUpload:true,
                    url:'/user/upload/',
                    removeAfterUpload:true
                });
                $scope.uploadItem = {};
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
                      alert("Ошибка при загрузке файла:"+response.error);
                    $scope.uploadItem.isUploading = false;
                    $scope.user.img_src = response.data;
                    $scope.$apply();
                });
            }
        }
    }]);