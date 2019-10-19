function newAssessmentValidate()
{
    $('#formDashNewGrAssessment')
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
                worktypeID: {
                    validators: {
                        notEmpty: {
                            message: 'Type required'
                        }
                    }
                },
                 maximum: {
                    validators: {
                        notEmpty: {
                            message: 'maximum required'
                        }
                    }
                }
            }
        });
}



