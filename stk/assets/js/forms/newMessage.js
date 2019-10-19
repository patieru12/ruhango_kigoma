function newMessageValidate()
{   
    updateCountdown();
    $('#newMessageContent').change(updateCountdown);
    $('#newMessageContent').keyup(updateCountdown);

     $('#formNewMessage')
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
                receiverType: {
                    validators: {
                        notEmpty: {
                            message: 'receiver type is required'
                        }
                    }
                },
                studentType: {
                    validators: {
                        notEmpty: {
                            message: 'Student type is required'
                        }
                    }
                },
                subject: {
                    validators: {
                        notEmpty: {
                            message: 'subject is required'
                        }
                    }
                },
                message: {
                    validators: {
                        notEmpty: {
                            message: 'your message is required'
                        },
                        stringLength: {
                            message: 'Your message can\'t be more than 300 characters',
                            max: function (value, validator, $field) {
                                return 310 - (value.match(/\r/g) || []).length;
                            }
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
                        .html(index === numTabs - 1 ? 'SEND MESSAGE' : 'Next');
                        
            var $total = navigation.find('li').length;
            var $current = index+1;
            var $percent = ($current/$total) * 100;
            $('#rootwizard').find('.bar').css({
                width:$percent+'%'
            });
        },
        onTabClick: function(tab, navigation, index) {
            return validateMeNewMessageTab(index);
        },
        onNext: function(tab, navigation, index) {
            var numTabs    = $('#rootwizard').find('.tab-pane').length,
                isValidTab = validateMeNewMessageTab(index - 1);
            if (!isValidTab) {
                return false;
            }

            if (index === numTabs) {

               $('#formNewMessage').formValidation('defaultSubmit');

            }

            return true;
        },
        onPrevious: function(tab, navigation, index) {
            return validateMeNewMessageTab(index + 1);
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

function updateCountdown() {
    // 140 is the max message length
    var remaining = 300 - jQuery('#newMessageContent').val().length;
    jQuery('.ab_countdown').text(remaining + ' characters remaining.');
}

function validateMeNewMessageTab(index) {
    var fv   = $('#formNewMessage').data('formValidation'), // FormValidation instance
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