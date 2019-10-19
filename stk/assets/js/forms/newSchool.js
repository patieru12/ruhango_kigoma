function newSchoolValidate()
{   
     $('#formNewSchool')
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
                            message: 'The school name is required'
                        }
                    }
                },
                abbreviation: {
                    validators: {
                        notEmpty: {
                            message: 'The school name abbreviation is required'
                        }
                    }
                },
                foundedIn : {
                    validators: {
                        notEmpty: {
                            message: 'year is required'
                        }
                    }
                },
                schoolOwership :{
                    validators: {
                        notEmpty: {
                            message: 'School ownership is required'
                        }
                    }
                },
                capacity :{
                    validators: {
                        notEmpty: {
                            message: 'capacity is required'
                        },
                        digits:{
                            message: 'Only numeric allowed' 
                        }
                    }
                },
                schoolType: {
                    validators: {
                        notEmpty: {
                            message: 'School type is required'
                        }
                    }
                },
                schoolGender: {
                    validators: {
                        notEmpty: {
                            message: 'Gender is required'
                        }
                    }
                },
                boarding: {
                    validators: {
                        notEmpty: {
                            message: ' Boarding is required'
                        }
                    }
                },
                'schoolProgramme[]': {
                    validators: {
                        notEmpty: {
                            message: ' Offered programme is required',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('schoolProgramme').val();
                                return (options != null && options.length >= 1 );
                            }
                        }
                    }
                },
                'schoolLevels[]': {
                    validators: {
                        notEmpty: {
                            message: ' School level is required',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('schoolLevels').val();
                                return (options != null && options.length >= 1 );
                            }
                        }
                    }
                },
                smsSenderName: {
                    validators: {
                        notEmpty: {
                            message: ' Sender name is required'
                        },
                        stringLength: {
                        min: 3,
                        max: 11,
                        message: 'Sender name between 3 - 11 characters '
                        }
                    }
                },
                'currencyUsed[]': {
                    validators: {
                        notEmpty: {
                            message: ' School level is required',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('currencyUsed').val();
                                return (options != null && options.length >= 1 );
                            }
                        }
                    }
                },
                'languageUsed[]': {
                    validators: {
                        notEmpty: {
                            message: ' School level is required',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('currencyUsed').val();
                                return (options != null && options.length >= 1 );
                            }
                        }
                    }
                },
                electricity: {
                    validators: {
                        notEmpty: {
                            message: ' Electricity availability is required'
                        }
                    }
                },
                water: {
                    validators: {
                        notEmpty: {
                            message: ' Water availability is required'
                        }
                    }
                },
                internet: {
                    validators: {
                        notEmpty: {
                            message: ' Internet availability  is required'
                        }
                    }
                },
                RA_country: {
                    validators: {
                        notEmpty: {
                            message: ' Country is required'
                        }
                    }
                },
                firstname: {
                    validators: {
                        notEmpty: {
                            message: ' admin\'s firstname is required'
                        }
                    }
                },
                lastName: {
                    validators: {
                        notEmpty: {
                            message: ' admin\'s lastname is required'
                        }
                    }
                },
                adminEmail: {
                    validators: {
                        notEmpty: {
                            message: ' admin\'s email is required'
                        },
                        emailAddress: {
                            message: ' valid email is required'
                        },
                        remote: {
                            url: '/api/validate/email/adminEmail',
                            type: 'POST',
                            message: 'Email already exist',
                            delay: 2000
                        }
                    }
                },
                email :{
                    validators: {
                        emailAddress: {
                            message: ' Valid email is required'
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
            return validateScNewSchoolTab(index);
        },
        onNext: function(tab, navigation, index) {
            var numTabs    = $('#rootwizard').find('.tab-pane').length,
                isValidTab = validateScNewSchoolTab(index - 1);
            if (!isValidTab) {
                return false;
            }

            if (index === numTabs) {

               $('#formNewSchool').formValidation('defaultSubmit');

            }

            return true;
        },
        onPrevious: function(tab, navigation, index) {
            return validateScNewSchoolTab(index + 1);
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

function validateScNewSchoolTab(index) {
    
    var fv   = $('#formNewSchool').data('formValidation'), // FormValidation instance
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