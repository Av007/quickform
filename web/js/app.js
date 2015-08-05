// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

angular.module('quickFormApp', [])
    .controller('FormCtrl', ['$scope', function($scope) {
        $scope.message = {};
        $scope.jsValidation = [];
        $scope.display = false;

        $scope.submit = function ($event) {
            var form = Object.keys($scope.jsValidation)[0];

            // verify form
            $scope.display = $scope[form].$invalid;

            if ($scope.display) {
                $event.preventDefault();
            }
        };

        $scope.getMessage = function(data, name) {
            var message = '';

            if (data[name] != undefined) {
                var item = $scope.jsValidation[data.$name][name];

                if (item != undefined) {
                    for (var i = 0; i < item.length; i++) {

                        var validationItem = item[i];
                        if ((validationItem.validation == 'required') && show(data[name]) && data[name].$error.required) {
                            $scope.message[name] = {};
                            $scope.message[name].show = true;

                            message = validationItem.message;
                            break;
                        }

                        if ((validationItem.validation == 'email') && show(data[name]) && data[name].$error.email) {
                            $scope.message[name] = {};
                            $scope.message[name].show = true;

                            message = validationItem.message;
                            break;
                        }

                        $scope.message[name] = {};
                        $scope.message[name].show = false;
                    }
                }
            }

            return message;
        };

        function show(field) {
            return $scope.display || field.$touched
        }

        $scope.init = function(data) {
            $scope.jsValidation = angular.fromJson(data);
        };
    }]);