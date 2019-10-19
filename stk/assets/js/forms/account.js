
function validateSetPasswordForm() {
    // body...
    $('#formSetPassword').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        // This option will not ignore invisible fields which belong to inactive panels
        exclude: ':disabled',
        fields: {
          confirmPassword: {
                validators: {
                    notEmpty: {
                        message: 'Password is required'
                    },
                    stringLength: {
                    min: 8,
                    message: 'Password should be at least 8 characters  '
                    },
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same'
                    }
                }
            }
        }
    });
}


function validateSetNewPassword() {
    // body...
    $('#formSetNewPassword').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        // This option will not ignore invisible fields which belong to inactive panels
        exclude: ':disabled',
        fields: {
          confirmPassword: {
                validators: {
                    notEmpty: {
                        message: 'Password is required'
                    },
                    stringLength: {
                    min: 8,
                    message: 'Password should be at least 8 characters  '
                    },
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same'
                    }
                }
            }
        }
    });
}