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

// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="add_tag_link button tiny success"><i class="fi-plus"></i> Add a tag</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $('ul.attachment');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see code block below)
        addTagForm($collectionHolder, $newLinkLi);
    });


});

function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);

    // also add a remove button, just for this example
    $newFormLi.append('<a href="#" class="remove-tag"><i class="fi-x"></i></a>');

    $newLinkLi.before($newFormLi);

    // handle the removal, just for this example
    $('.remove-tag').click(function(e) {
        e.preventDefault();

        $(this).parent().remove();

        return false;
    });
}
