// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

angular.module('quickFormApp', [])
    .controller('FormCtrl', ['$scope', function($scope) {
        $scope.message = {};
        $scope.jsValidation = [];

        $scope.submit = function ($event) {
            var form = $event.target.name;
            // verify form
            $scope.display = $scope[form].$invalid;

            if ($scope.display) {
                $event.preventDefault();
            }
        };

        $scope.getMessage = function(data, name) {
            var message = '';
            var element = angular.element('form[name="' + data.$name + '"] input');

            if (data[name] == undefined) {
                return message;
            }

            if ($scope[data.$name][name] != undefined) {
                var validations = element.data('validation');

                for (var i = 0; i < validations.length; i++) {
                    if (show(data[name]) && (
                        (((validations[i].validation == 'required')) && data[name].$error.required) ||
                        ((validations[i].validation == 'email') && data[name].$error.email) ||
                        ((validations[i].validation == 'min') && data[name].$error.minlength) ||
                        ((validations[i].validation == 'max') && data[name].$error.maxlength) ||
                        ((validations[i].validation == 'regexp') && data[name].$error.pattern)
                        )
                    ) {
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
