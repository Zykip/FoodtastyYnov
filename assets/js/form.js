import 'jquery-form';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods.min';
import Swal from 'sweetalert2';
import 'jquery-deparam';

$(document).ready(() => {
    /**
     * Set default Error placements
     */
    $.validator.setDefaults({
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },
        errorElement: 'div',
        errorClass: 'text-danger',
        errorPlacement: function (error, element) {
            if (element.is('[type="checkbox"]')) {
                element.closest('.checkbox, .checkbox-inline, .switch, .switch-inline').append(error);
            } else if (element.is('[type="radio"]')) {
                element.closest('.radio, .radio-inline').append(error);
            } else{
                element.closest('.form-group').append(error);
            }
        },
    });

    $.validator.addMethod('alphanum', function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9/!@#$%^&*()_=+\-\s]+$/.test(value);
    }, 'Please enter a valid value');

    $.validator.addMethod('ip_with_port', function(value, element){
        return this.optional(element) || /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\:?([0-9]{1,5})?$/i.test(value);
    }, 'Please enter a valid IPV4 address');

    $.validator.addMethod("greaterthan", function(value, element, params) {
        console.log(params);
        if (!/Invalid|NaN/.test(new Date(value))) {
            return new Date(value) > new Date($(params).val());
        }

        return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
    },'Must be greater than {0}.');

    /**
     * Validate and Submit form
     */
    $('.js-form').each(function(){
        $(this).validate({
            submitHandler : function(form){
                $(form).ajaxSubmit({
                    success : (response) => {
                        handleSuccessReponse(response);
                    },
                    error : (error) => {
                        Swal.fire('Error', error.toString(), 'error');
                    }
                });
            }
        })
    });

    $('.js-form-validate').each(function () {
        $(this).validate({
            submitHandler: function(){
                return true;
            }
        })
    });

    $('.js-form-submit').each(function () {
        $(this).ajaxForm({
            success: (response) => {
                handleSuccessReponse(response);
            },
            error: function(error){
                handleErrorResponse(error)
            }
        })
    });

    $('.js-form-json').each(function () {
        $(this).validate({
            submitHandler: function(form){
                var $this_form = $(form);
                $.ajax({
                    url : $this_form.attr('action'),
                    data : JSON.stringify($.deparam($this_form.formSerialize())),
                    method : $this_form.attr('method'),
                    contentType : 'application/json',
                    processData : false,
                    dataType : 'json',
                    success : function(r){
                        handleSuccessReponse(r);
                    },
                    error: function(error){
                        handleErrorResponse(error)
                    }
                });
            }
        })
    });
});

function handleSuccessReponse(response){
    if(response.cb){
        window[response.cb](response);
    }

    if(response.message){
        Swal.fire(response.status === 'OK' ? 'Success' : 'Error', response.message, response.status === 'OK' ? 'success' : 'error');
    }

    if(response.redirect){
        window.location.href = response.redirect;
    }

    if(response.reload){
        window.location.reload();
    }
}

function handleErrorResponse(error){
    Swal.fire('Error', error.statusText, 'error');
}