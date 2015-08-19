// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

angular.module('quickFormApp', [])
    .controller('FormCtrl', ['$scope', function($scope) {
        $scope.message = {};

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

            if (data[name] == undefined) {
                return message;
            }

            if ($scope[data.$name][name] != undefined) {
                var selector = 'form[name="' + data.$name + '"] input[name="' + name + '"], ' +
                                'form[name="' + data.$name + '"] textarea[name="' + name + '"]';

                var element = angular.element(selector);
                var validations = element.data('validation');

                if (element.data('mask')) {
                    element.mask(element.data('mask'));
                }

                if (validations != undefined) {
                    for (var i = 0; i < validations.length; i++) {

                        if (show(data[name]) && (
                            (((validations[i].validation == 'required')) && data[name].$error.required) ||
                            ((validations[i].validation == 'email') && data[name].$error.email) ||
                            ((validations[i].validation == 'min') && data[name].$error.minlength) ||
                            ((validations[i].validation == 'max') && data[name].$error.maxlength) ||
                            ((validations[i].validation == 'regexp') && data[name].$error.pattern) ||
                            ((validations[i].validation == 'phone') && data[name].$error.pattern)
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
            }

            return message;
        };

        $scope.showError = function(data, name) {
            $scope.display = true;
            $scope.getMessage(data, name);

            return $scope.message[name].show;
        };

        function show(field) {
            return $scope.display || field.$touched
        }
    }]
);
