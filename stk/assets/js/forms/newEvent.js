function newSchoolEventValidate()
{   
     $('#formNewEvent')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                name: {
                    validators: {
                        notEmpty: {
                            message: 'event name required'
                        }
                    }
                },
                description: {
                    validators: {
                        notEmpty: {
                            message: 'Description required'
                        }
                    }
                },
                'studentGender[]': {
                    validators: {
                        choice: {
                            min: 1,
                            message: 'at least one gender should be selected'
                        }
                    }
                },
                'studentBoarding[]': {
                    validators: {
                        choice: {
                            min: 1,
                            message: 'at least one boarding status should be selected'
                        }
                    }
                }
            }
        });

    $('#rootwizard').bootstrapWizard({
        onTabShow: function(tab, navigation, index){

            var numTabs = $('#rootwizard').find('.tab-pane').length;
                $('#rootwizard')
                    .find('.next')
                        .removeClass('disabled')  // Enable the Next button
                        .find('a')
                        .html(index === numTabs - 1 ? 'SAVE' : 'Next');
                        
            var $total = navigation.find('li').length;
            var $current = index+1;
            var $percent = ($current/$total) * 100;
            $('#rootwizard').find('.bar').css({
                width:$percent+'%'
            });
        },
        onTabClick: function(tab, navigation, index) {
            return validateAtNewEventTab(index);
        },
        onNext: function(tab, navigation, index) {
            var numTabs    = $('#rootwizard').find('.tab-pane').length,
                isValidTab = validateAtNewEventTab(index - 1);
            if (!isValidTab) {
                return false;
            }

            if (index === numTabs) {

               $('#formNewEvent').formValidation('defaultSubmit');

            }

            return true;
        },
        onPrevious: function(tab, navigation, index) {
            return validateAtNewEventTab(index + 1);
        },

    });
    
    if (Array.prototype.forEach) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html);
        });
    } else {
        var elems = document.querySelectorAll('.js-switch');
        for (var i = 0; i < elems.length; i++) {
            var switchery = new Switchery(elems[i]);
        }
    }


}

function validateAtNewEventTab(index) {
    
    var fv   = $('#formNewEvent').data('formValidation'), // FormValidation instance
        // The current tab
    $tab = $('#rootwizard').find('.tab-pane').eq(index);

    // Validate the container
    fv.validateContainer($tab);

    var isValidStep = fv.isValidContainer($tab);
    if (isValidStep === false || isValidStep === null) {
        // Do not jump to the target tab
        return false;
    }

    return true;
}