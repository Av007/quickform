// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

angular.module('quickFormApp', [])
    .controller('FormCtrl', ['$scope', '$parse', function($scope, $parse) {
        $scope.message = {};
        $scope.jsValidation = [];
        $scope.display = false;

        $scope.submit = function ($event) {
            var form = $event.target.name;
            // verify form
            $scope.display = $scope[form].$invalid;

            if ($scope.display) {
                $event.preventDefault();
            }
        };

        $scope.getMessage = function(data, name) {
            /*var model = $parse('form1');
            model.assign($scope, $scope.formData);*/

            var message = '';

            if ($scope[data.$name][name] != undefined) {
                var validations = angular.element('form[name="' + data.$name + '"] input').data('validation');

                for (var i = 0; i < validations.length; i++) {

                    if ((validations[i].validation == 'required') && show(data[name]) && data[name].$error.required) {
                        $scope.message[name] = {};
                        $scope.message[name].show = true;

                        message = validations[i].message;
                        break;
                    }

                    if ((validations[i].validation == 'email') && show(data[name]) && data[name].$error.email) {
                        $scope.message[name] = {};
                        $scope.message[name].show = true;

                        message = validations[i].message;
                        break;
                    }

                    $scope.message[name] = {};
                    $scope.message[name].show = false;
                }
            }

            return message;
        };

        function show(field) {
            return $scope.display || field.$touched
        }
    }]);
