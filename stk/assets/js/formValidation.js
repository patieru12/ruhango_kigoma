
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
            // This option will not ignore invisible fields which belong to inactive panels
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
                tel1: {
                    validators: {
                        notEmpty: {
                            message: ' Telephone number is required'
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
                        }
                    }
                }
            }
        });

    $('#rootwizard').bootstrapWizard({
            tabClass: 'nav nav-pills',
            onTabClick: function(tab, navigation, index) {
                return validateTab(index);
            },
            onNext: function(tab, navigation, index) {
                var numTabs    = $('#rootwizard').find('.tab-pane').length,
                    isValidTab = validateTab(index - 1);
                if (!isValidTab) {
                    return false;
                }

                if (index === numTabs) {

                   $('#formNewSchool').formValidation('defaultSubmit');

                }

                return true;
            },
            onPrevious: function(tab, navigation, index) {
                return validateTab(index + 1);
            },
            onTabShow: function(tab, navigation, index) {
                // Update the label of Next button when we are at the last tab
                var numTabs = $('#rootwizard').find('.tab-pane').length;
                $('#rootwizard')
                    .find('.next')
                        .removeClass('disabled')  // Enable the Next button
                        .find('a')
                        .html(index === numTabs - 1 ? '<i class="fa fa-times bg-red action">SAVE</i>' : 'Next');

            }
        });

}
   
    

function validateTab(index) {
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
