(function($) {



    $.fn.numericOnly = function(options) {
        return this.each(function(index, elem) {
            var settings = $.extend({
                target: elem,
                useFloat: false,
                minNumber: '',
                maxNumber: '',
                step: 1,
                addClass: '',
                onSelect: null
            }, options);
            filterNumbers(settings);
            changeNumbers(settings);
            otherChanges(settings);
        });
    };

    function filterNumbers(numericOnly) {
        var numInput = $(numericOnly.target).val(); // Main selector

        var numDecRgx = /[^-?0-9\.]/g; // Only numbers and floats are accepted
        $(numInput).on("keyup", function(event) {
            /**
             *  numpad 0  - event.which = 48
             *  numpad 9  - event.which = 57
             *  numpad 0  - event.which = 97
             *  numpad 9  - event.which = 105
             *  backspace  - event.which = 109
             *  dot  - event.which = 110
             *  $(this).val().indexOf('.') != -1 // allow dot to be typed
             **/
            console.log('type of  ', typeof $(numInput).val());
            $(this).val($(numInput).val().replace(numDecRgx, ''));
            if (event.which != 8 && event.which != 109 && event.which != 110 && event.which != 189
                && (event.which != 46 || $(this).val().indexOf('.') != -1)

                && (event.which < 48 || event.which > 57) && (event.which < 96 || event.which > 105)) {
                event.preventDefault();
            }

        });
    }

    function changeNumbers(numericOnly) {
        var numInput = numericOnly.target;
        $(numInput).siblings('.arrows').find('.no-arrow_top').on('click', function() {
            var numInput = $(this).parent('span').siblings('input');
            if (numInput.val().length > 0) {
                if (numericOnly.useFloat == false) {
                    if (numericOnly.maxNumber == '' && typeof numericOnly.maxNumber === "string") {
                        incrementByStep(numInput, numericOnly.step, '');
                    } else if (typeof numericOnly.maxNumber === "number") {
                        incrementByStep(numInput, numericOnly.step, numericOnly.maxNumber);
                        if (numInput.val() > numericOnly.maxNumber) {
                            maxNumberMessage(numInput, numericOnly.maxNumber);
                        }
                        if (numInput.val() < numericOnly.minNumber) {
                            minNumberMessage(numInput, numericOnly.minNumber);
                        }
                    }
                } else if (numericOnly.useFloat == true) {
                    if (numericOnly.maxNumber == '' && typeof numericOnly.maxNumber === "string") {
                        incrementFloatByStep(numInput, numericOnly.step, '');
                    } else if (typeof numericOnly.maxNumber === "number") {
                        incrementFloatByStep(numInput, numericOnly.step, numericOnly.maxNumber);
                        if (numInput.val() > numericOnly.maxNumber) {
                            maxNumberFloatMessage(numInput, numericOnly.maxNumber);
                        }
                        if (typeof numericOnly.minNumber === "number" && numInput.val() < numericOnly.minNumber) {
                            minNumberFloatMessage(numInput, numericOnly.minNumber);
                        }
                    }
                }
                if (numericOnly.onSelect && typeof numericOnly.onSelect === "function") {
                    numericOnly.onSelect(numInput.val());
                }
            } else {
                emptyFieldMessage();
            }
        });

        $(numInput).siblings('.arrows').find('.no-arrow_bottom').on('click', function() {
            var numInput = $(this).parent('span').siblings('input');
            if (numInput.val().length > 0) {
                if (numericOnly.useFloat == false) {
                    if (numericOnly.minNumber == '' && typeof numericOnly.minNumber === "string") {
                        decrementByStep(numInput, numericOnly.step, '');
                    } else if (typeof numericOnly.minNumber === "number") {
                        decrementByStep(numInput, numericOnly.step, numericOnly.minNumber);
                        if (numInput.val() < numericOnly.minNumber) {
                            minNumberMessage(numInput, numericOnly.minNumber);
                        }
                        if (typeof numericOnly.maxNumber === "number" && numInput.val() > numericOnly.maxNumber) {
                            maxNumberMessage(numInput, numericOnly.maxNumber);
                        }
                    }
                } else if (numericOnly.useFloat == true) {
                    if (numericOnly.minNumber == '' && typeof numericOnly.minNumber === "string") {
                        decrementFloatByStep(numInput, numericOnly.step, '');
                    } else if (typeof numericOnly.minNumber === "number") {
                        decrementFloatByStep(numInput, numericOnly.step, numericOnly.minNumber);
                        if (numInput.val() > numericOnly.maxNumber) {
                            maxNumberFloatMessage(numInput, numericOnly.maxNumber);
                        }
                        if (numInput.val() < numericOnly.minNumber) {
                            minNumberFloatMessage(numInput, numericOnly.minNumber);
                        }
                    }
                }
                if (numInput.val().length > 0 && numericOnly.onSelect && typeof numericOnly.onSelect === "function") {
                    numericOnly.onSelect(numInput.val());
                }
            } else {
                emptyFieldMessage();
            }
        });
    }
    function incrementByStep(numInput, step, maxNumber) {
        var diff = parseInt(numInput.val()) + step;
        if (typeof maxNumber == "number" && diff <= maxNumber) {
            numInput.val(diff);
        } else if (typeof maxNumber == "string" && maxNumber == '') {
            numInput.val(diff);
        }
    }

    function decrementByStep(numInput, step, minNumber) {
        var diff = parseInt(numInput.val()) - step;
        if (typeof minNumber == "number" && diff >= minNumber) {
            numInput.val(diff);
        } else if (typeof minNumber == "string" && minNumber == '') {
            numInput.val(diff);
        }
    }
    function maxNumberMessage(numInput, data) {
        numInput.val(data);
        console.log('%c Value cannot exceed maximum number limit', 'background: red; color: white; display: block;');
    }
    function minNumberMessage(numInput, data) {
        numInput.val(data);
        console.log('%c Value cannot exceed minimum number limit', 'background: red; color: white; display: block;');
    }
    function maxNumberFloatMessage(numInput, data) {
        var floatInput = parseFloat(data);
        numInput.val(floatInput.toFixed(2));
        console.log('%c Value cannot exceed maximum FLOAT number limit', 'background: red; color: white; display: block;');
    }

    function minNumberFloatMessage(numInput, data) {
        var floatInput = parseFloat(data);
        numInput.val(floatInput.toFixed(2));
        console.log('%c Value cannot exceed minimum FLOAT number limit', 'background: red; color: white; display: block;');
    }

    function incrementFloatByStep(numInput, data, maxNumber) {
        var floatInput = parseFloat(numInput.val());
        var diff = floatInput + parseFloat(data);
        if (typeof maxNumber == "number" && diff <= maxNumber) {
            numInput.val(diff.toFixed(2));
        } else if (typeof maxNumber == "string" && maxNumber == '') {
            numInput.val(diff.toFixed(2));
        }
    }

    function decrementFloatByStep(numInput, data, minNumber) {
        var floatInput = parseFloat(numInput.val());
        var diff = floatInput - parseFloat(data);
        if (typeof minNumber == "number" && diff >= minNumber) {
            numInput.val(diff.toFixed(2));
        } else if (typeof minNumber == "string" && minNumber == '') {
            numInput.val(diff.toFixed(2));
        }
    }

    function emptyFieldMessage() {
        console.log('%c Please enter some value ', 'background: red; color: white; display: block;');
    }

    function otherChanges(numericOnly) {
        var currentSelector = numericOnly.target;
        var newClass = numericOnly.addClass;
        if (newClass != '') {
            $(currentSelector).addClass(newClass);
        }
    }
}(jQuery));
