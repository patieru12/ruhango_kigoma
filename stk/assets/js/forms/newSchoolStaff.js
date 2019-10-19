function newSchoolStaffValidate()
{   
     $('#formNewSchoolStaff')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            exclude: ':disabled',
            fields: {
                firstname: {
                    validators: {
                        notEmpty: {
                            message: 'Firstname is required'
                        }
                    }
                },
                lastName: {
                    validators: {
                        notEmpty: {
                            message: 'Lastname is required'
                        }
                    }
                },
                'roles[]': {
                    validators: {
                        notEmpty: {
                            message: ' user role is required',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('roles').val();
                                return (options != null && options.length >= 1 );
                            }
                        }
                    }
                },
                sex :{
                    validators: {
                        notEmpty: {
                            message: 'Gender is required'
                        }
                    }
                },
                BD_day :{
                    validators: {
                        notEmpty: {
                            message: 'Day is required'
                        }
                    }
                },
                BD_moth :{
                    validators: {
                        notEmpty: {
                            message: 'Month is required'
                        }
                    }
                },
                BD_year :{
                    validators: {
                        notEmpty: {
                            message: 'Year is required'
                        }
                    }
                },
                maritalStatus :{
                    validators: {
                        notEmpty: {
                            message: 'Marital Status is required'
                        }
                    }
                },
                schoolStaffStatus :{
                    validators: {
                        notEmpty: {
                            message: 'Employment Status is required'
                        }
                    }
                },
                nationality :{
                    validators: {
                        notEmpty: {
                            message: 'Nationality is required'
                        }
                    }
                },
                BP_country :{
                    validators: {
                        notEmpty: {
                            message: 'Country is required'
                        }
                    }
                },
                RA_country :{
                    validators: {
                        notEmpty: {
                            message: 'Country is required'
                        }
                    }
                },
                email :{
                    validators: {
                        notEmpty: {
                            message: ' Staff email is required'
                        },
                        emailAddress: {
                            message: ' Valid email is required'
                        },
                        remote: {
                            url: '/api/validate/email/email',
                            type: 'POST',
                            message: 'Email already exist',
                            delay: 2000
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
            return validateScNewSchoolStaffTab(index);
        },
        onNext: function(tab, navigation, index) {
            var numTabs    = $('#rootwizard').find('.tab-pane').length,
                isValidTab = validateScNewSchoolStaffTab(index - 1);
            if (!isValidTab) {
                return false;
            }

            if (index === numTabs) {

               $('#formNewSchoolStaff').formValidation('defaultSubmit');

            }

            return true;
        },
        onPrevious: function(tab, navigation, index) {
            return validateScNewSchoolStaffTab(index + 1);
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

function validateScNewSchoolStaffTab(index) {
    
    var fv   = $('#formNewSchoolStaff').data('formValidation'), // FormValidation instance
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