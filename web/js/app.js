// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

angular.module('quickFormApp', [])
    .controller('FormCtrl', ['$scope', function($scope) {
        $scope.message = {};
        $scope.jsValidation = [];

        $scope.getMessage = function(data, name) {
            if (data[name] != undefined) {
                var item = $scope.jsValidation[data.$name][name];

                if (item != undefined) {
                    for (var i = 0; i < item.length; i++) {

                        var validationItem = item[i];
                        if ((validationItem.validation == 'required') && data[name].$touched && data[name].$error.required) {
                            $scope.message[name] = {};
                            $scope.message[name].show = true;

                            return validationItem.message;
                        }

                        if ((validationItem.validation == 'email') && data[name].$touched && data[name].$error.email) {
                            $scope.message[name] = {};
                            $scope.message[name].show = true;

                            return validationItem.message;
                        }

                        $scope.message[name] = {};
                        $scope.message[name].show = false;
                    }
                }
            }

            return '';
        };

        $scope.init = function(data) {
            $scope.jsValidation = angular.fromJson(data);
            console.log($scope.jsValidation);
        };

        //form.$setPristine();
        //form.$setUntouched();
    }]);