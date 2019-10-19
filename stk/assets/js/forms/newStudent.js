function newStudentValidate()
{   
     $('#formNewStudent')
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
                            message: 'firstname is required'
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
                sex: {
                    validators: {
                        notEmpty: {
                            message: 'gender is required'
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
                status :{
                    validators: {
                        notEmpty: {
                            message: 'Student Status is required'
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
                fa_isAlive :{
                    validators: {
                        notEmpty: {
                            message: 'father\'s live status is required'
                        }
                    }
                },
                ma_isAlive :{
                    validators: {
                        notEmpty: {
                            message: 'mother\'s live status is required'
                        }
                    }
                },
                email :{
                    validators: {
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
                },
                fa_email :{
                    validators: {
                        emailAddress: {
                            message: ' Valid email is required'
                        }
                    }
                },
                ma_email :{
                    validators: {
                        emailAddress: {
                            message: ' Valid email is required'
                        }
                    }
                },
                gu_email :{
                    validators: {
                        emailAddress: {
                            message: ' Valid email is required'
                        }
                    }
                },
                sponsorType :{
                    validators: {
                        notEmpty: {
                            message: 'Sponsor is required'
                        }
                    }
                },
                organization :{
                    validators: {
                        callback: {
                            callback: function (value, validator, $field) {

                                var selectNewStudentSponsorType = $("#selectNewStudentSponsorType").select2('val');
                                var selectStudentOrganization   = $("#selectStudentOrganization").select2('val');

                                if ( selectNewStudentSponsorType == 2 )
                                {
                                    if ( selectStudentOrganization > 0 ) 
                                    {
                                        return true;

                                    }else{

                                        return {
                                            valid: false ,    // or false
                                            message: 'Organization is required'
                                        }
                                    }

                                }else{

                                    return true;
                                }
                            }
                        }
                    }
                },
                primarycontact: {
                    validators: {
                        callback: {
                            message: 'Primary Contact is required',
                            callback: function (value, validator, $field) {
                                var fa_PhoneNumber         = $('#fa_PhoneNumber').val().replace(/[#_-]/g,'');
                                var mo_PhoneNumber         = $('#mo_PhoneNumber').val().replace(/[#_-]/g,'');
                                var gu_PhoneNumber         = $('#gu_PhoneNumber').val().replace(/[#_-]/g,'');
                                var selectedPrimarycontact = $("#selectStudentNewPrimarycontact").select2('val');

                                console.log("fa_PhoneNumber: "+fa_PhoneNumber);
                                console.log("mo_PhoneNumber: "+mo_PhoneNumber);
                                console.log("gu_PhoneNumber: "+gu_PhoneNumber);
                                console.log("value: "+value);

                                if ( selectedPrimarycontact > 0 )
                                {
                                        return true;

                                }else if( !value ){

                                    if( (fa_PhoneNumber) || (mo_PhoneNumber) || (gu_PhoneNumber) ) {
                                    
                                     return false;

                                    }else{

                                     return true;
                                    }

                                }else{
                                    
                                    return true;
                                } 
                            }
                        }
                    }
                },
                fa_PhoneNumber: {
                    validators: {
                        different: {
                            field: 'mo_PhoneNumber, gu_PhoneNumber',
                            message: 'Phone must be unique'
                        }
                    }

                },
                mo_PhoneNumber: {
                    validators: {
                        different: {
                            field: 'fa_PhoneNumber, gu_PhoneNumber',
                            message: 'Phone must be unique'
                        }
                    }
                },
                gu_PhoneNumber: {
                    validators: {
                        different: {
                            field: 'fa_PhoneNumber,mo_PhoneNumber',
                            message: 'Phone must be unique'
                        }
                    }
                },
                boarding: {
                    validators: {
                        notEmpty: {
                            message: 'Boarding Status is required'
                        }
                    }
                },
                annualClassroomID :{
                    validators: {
                        notEmpty: {
                            message: 'Class is required'
                        }
                    }
                },
                gu_firstname: {
                    validators: {
                        callback: {
                            callback: function (value, validator, $field) {

                                var gu_firstname            = $('#gu_firstname').val();
                                var gu_lastName             = $('#gu_lastName').val();
                                var select_gu_relationship  = $("#select_gu_relationship").select2('val');

                                var formNewStudent =  $('#formNewStudent');

                                if ( select_gu_relationship != 0 && (gu_firstname.length > 0) && (gu_lastName.length > 0) )
                                {
                                    return true;

                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length == 0)  && (gu_firstname.length == 0) ){
                                    return true;

                                }else if ( select_gu_relationship != 0 &&  (gu_lastName.length == 0) && (gu_firstname.length == 0)  ){

                                    return {
                                        valid: false ,    // or false
                                        message: 'Guardian\'s Firstname is required'
                                    }

                                }
                                else if ( select_gu_relationship != 0 && (gu_lastName.length == 0) ){

                                    return true;

                                }else if ( select_gu_relationship != 0 && (gu_firstname.length == 0) ){
                                    
                                    return {
                                        valid: false ,    // or false
                                        message: 'Guardian\'s Firstname is required'
                                    }

                                }else if ( select_gu_relationship == 0 &&  (gu_lastName.length > 0)  && (gu_firstname.length > 0) ){

                                    return true;

                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length > 0)  && (gu_firstname.length == 0) ){
                                    
                                    return {
                                        valid: false ,    // or false
                                        message: 'Guardian\'s Firstname is required'
                                    }
                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length == 0)  && (gu_firstname.length > 0) ){
                                    
                                    return true;

                                }

                            }
                        }
                    }
                },
                gu_lastName: {
                    validators: {
                        callback: {
                            callback: function (value, validator, $field) {

                                var gu_firstname            = $('#gu_firstname').val();
                                var gu_lastName             = $('#gu_lastName').val();

                                var select_gu_relationship  = $("#select_gu_relationship").select2('val');


                                var formNewStudent =  $('#formNewStudent');

                                if ( select_gu_relationship != 0 && (gu_firstname.length > 0) && (gu_lastName.length > 0) )
                                {
                                    return true;

                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length == 0)  && (gu_firstname.length == 0) ){
                                    
                                    return true;

                                }else if ( select_gu_relationship != 0 &&  (gu_lastName.length == 0) && (gu_firstname.length == 0)  ){

                                    return {
                                        valid: false ,    // or false
                                        message: 'Guardian\'s Lastname is required'
                                    }

                                }
                                else if ( select_gu_relationship != 0 && (gu_lastName.length == 0) ){

                                    return {
                                        valid: false ,    // or false
                                        message: 'Guardian\'s Lastname is required'
                                    }

                                }else if ( select_gu_relationship != 0 && (gu_firstname.length == 0) ){
                                    return true;

                                }else if ( select_gu_relationship == 0 &&  (gu_lastName.length > 0)  && (gu_firstname.length > 0) ){

                                    return true;

                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length > 0)  && (gu_firstname.length == 0) ){
                                    return true;
                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length == 0)  && (gu_firstname.length > 0) ){
                                    
                                    return {
                                        valid: false ,    // or false
                                        message: 'Guardian\'s Lastname is required'
                                    }

                                }

                            }
                        }
                    }
                },
                gu_relationship: {
                    validators: {
                        callback: {
                            callback: function (value, validator, $field) {

                                var gu_firstname            = $('#gu_firstname').val();
                                var gu_lastName             = $('#gu_lastName').val();

                                var select_gu_relationship  = $("#select_gu_relationship").select2('val');


                                var formNewStudent =  $('#formNewStudent');

                                if ( select_gu_relationship != 0 && (gu_firstname.length > 0) && (gu_lastName.length > 0) )
                                {
                                    console.log("All Are not empty");
                                    return true;

                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length == 0)  && (gu_firstname.length == 0) ){
                                    console.log("select_gu_relationship empty and ( gu_lastName&gu_firstname ) are empty");
                                    formNewStudent.formValidation('revalidateField', 'gu_firstname');
                                    formNewStudent.formValidation('revalidateField', 'gu_lastName');
                                    return true;

                                }else if ( select_gu_relationship != 0 &&  (gu_lastName.length == 0) && (gu_firstname.length == 0)  ){

                                    formNewStudent.formValidation('revalidateField', 'gu_firstname');
                                    formNewStudent.formValidation('revalidateField', 'gu_lastName');

                                    console.log("select_gu_relationship not empty and gu_lastName and gu_firstname are empty");
                                    return true;

                                }
                                else if ( select_gu_relationship != 0 && (gu_lastName.length == 0) ){
                                    console.log("select_gu_relationship not empty and gu_lastName is empty");
                                    formNewStudent.formValidation('revalidateField', 'gu_lastName');
                                    return true;

                                }else if ( select_gu_relationship != 0 && (gu_firstname.length == 0) ){
                                     formNewStudent.formValidation('revalidateField', 'gu_firstname');
                                     console.log("select_gu_relationship not empty and gu_firstname is empty");
                                    return true;

                                }else if ( select_gu_relationship == 0 &&  (gu_lastName.length > 0)  && (gu_firstname.length > 0) ){

                                    console.log("select_gu_relationship empty and  (gu_lastName&gu_firstname) are not empty");
                                    return {
                                        valid: false ,    // or false
                                        message: 'Relationship is required'
                                    }

                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length > 0)  && (gu_firstname.length == 0) ){
                                    console.log("select_gu_relationship empty, gu_lastName not empty and gu_firstname empty");
                                    
                                    formNewStudent.formValidation('revalidateField', 'gu_firstname');
                                    return {
                                        valid: false ,    // or false
                                        message: 'Relationship is required'
                                    }
                                }
                                else if ( select_gu_relationship == 0 &&  (gu_lastName.length == 0)  && (gu_firstname.length > 0) ){
                                    console.log("select_gu_relationship empty, gu_lastName is  empty and gu_firstname not empty");

                                    formNewStudent.formValidation('revalidateField', 'gu_lastName');
                                    return {
                                        valid: false ,    // or false
                                        message: 'Relationship is required'
                                    }

                                }

                            }
                        }
                    }
                }
            }
        }).find('[name="fa_PhoneNumber"]').mask('999-999-9999').change(function(e) {

                                                                        var formNewStudent =  $('#formNewStudent');

                                                                        formNewStudent.formValidation('revalidateField', 'fa_PhoneNumber');
                                                                        formNewStudent.formValidation('revalidateField', 'mo_PhoneNumber');
                                                                        formNewStudent.formValidation('revalidateField', 'gu_PhoneNumber');

                                                                }).end()
          .find('[name="mo_PhoneNumber"]').mask('999-999-9999').change(function(e) {

                                                                        var formNewStudent =  $('#formNewStudent');

                                                                        formNewStudent.formValidation('revalidateField', 'mo_PhoneNumber');
                                                                        formNewStudent.formValidation('revalidateField', 'fa_PhoneNumber');
                                                                        formNewStudent.formValidation('revalidateField', 'gu_PhoneNumber');

                                                                }).end()
          .find('[name="gu_PhoneNumber"]').mask('999-999-9999').change(function(e) {

                                                                        var formNewStudent =  $('#formNewStudent');

                                                                        formNewStudent.formValidation('revalidateField', 'gu_PhoneNumber');
                                                                        formNewStudent.formValidation('revalidateField', 'fa_PhoneNumber');
                                                                        formNewStudent.formValidation('revalidateField', 'mo_PhoneNumber');
                                                                       

                                                                }).end()
          .find('[name="gu_firstname"]').change(function(e) {

                                                        var formNewStudent =  $('#formNewStudent');
                                                        formNewStudent.formValidation('revalidateField', 'gu_relationship');
                                                                       
                                                            }).end()
          .find('[name="gu_lastName"]').change(function(e) {

                                                        var formNewStudent =  $('#formNewStudent');
                                                        formNewStudent.formValidation('revalidateField', 'gu_relationship');
                                                           
                                                    }).end()
          .find('[name="sponsorType"]').change(function(e) {

                                                        var formNewStudent =  $('#formNewStudent');
                                                        formNewStudent.formValidation('revalidateField', 'organization');
                                                           
                                                    }).end();;

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
            return validateStNewStudentTab(index);
        },
        onNext: function(tab, navigation, index) {
            var numTabs    = $('#rootwizard').find('.tab-pane').length,
                isValidTab = validateStNewStudentTab(index - 1);
            if (!isValidTab) {
                return false;
            }

            if (index === numTabs) {

               $('#formNewStudent').formValidation('defaultSubmit');

            }

            return true;
        },
        onPrevious: function(tab, navigation, index) {
            return validateStNewStudentTab(index + 1);
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

function validateStNewStudentTab(index) {
    
    var fv   = $('#formNewStudent').data('formValidation'), // FormValidation instance
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