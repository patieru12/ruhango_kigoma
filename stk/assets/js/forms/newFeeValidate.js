function newSchoolFeeValidate()
{   
     $('#formNewSchoolFee')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                feetype: {
                    validators: {
                        notEmpty: {
                            message: 'fee type required'
                        }
                    }
                },
                description: {
                    validators: {
                        notEmpty: {
                            message: 'description required'
                        }
                    }
                },
                amount: {
                    validators: {
                        notEmpty: {
                            message: 'amount is required'
                        }
                    }
                },
                currency: {
                    validators: {
                        notEmpty: {
                            message: 'currency is required'
                        }
                    }
                },
                dueDate: {
                    validators: {
                        notEmpty: {
                            message: 'dueDate is required'
                        }
                    }
                },
                'studentBoarding[]': {
                    validators: {
                        choice: {
                            min: 1,
                            message: 'Please choose at least one boarding status'
                        }
                    }
                },
                'studentSponsor[]': {
                    validators: {
                        choice: {
                            min: 1,
                            message: 'Please choose at least one sponsor'
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
            return validatePaNewFeeTab(index);
        },
        onNext: function(tab, navigation, index) {
            var numTabs    = $('#rootwizard').find('.tab-pane').length,
                isValidTab = validatePaNewFeeTab(index - 1);
            if (!isValidTab) {
                return false;
            }

            if (index === numTabs) {

               $('#formNewSchoolFee').formValidation('defaultSubmit');

            }

            return true;
        },
        onPrevious: function(tab, navigation, index) {
            return validatePaNewFeeTab(index + 1);
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

function validatePaNewFeeTab(index) {
    
    var fv   = $('#formNewSchoolFee').data('formValidation'), // FormValidation instance
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