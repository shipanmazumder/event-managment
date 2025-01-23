$(document).ready(function() {
    const $form = $('#eventRegistrationForm');
    const $messageDiv = $('#message');

    function showMessage(message, isError = false) {
        $messageDiv.text(message)
            .removeClass('alert-danger alert-success')
            .addClass(`alert ${isError ? 'alert-danger' : 'alert-success'}`)
            .show();
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validateMobile(mobile) {
        return /^[0-9]{11}$/.test(mobile);
    }

    $('#name').on('input blur', function() {
        const $input = $(this);
        if ($input.val().trim().length < 2) {
            $input.addClass('is-invalid');
        } else {
            $input.removeClass('is-invalid').addClass('is-valid');
        }
    });

    $('#email').on('input blur', function() {
        const $input = $(this);
        if (!validateEmail($input.val().trim())) {
            $input.addClass('is-invalid');
        } else {
            $input.removeClass('is-invalid').addClass('is-valid');
        }
    });

    $('#mobile').on('input blur', function() {
        const $input = $(this);
        if (!validateMobile($input.val().trim())) {
            $input.addClass('is-invalid');
        } else {
            $input.removeClass('is-invalid').addClass('is-valid');
        }
    });

    $form.on('submit', function(e) {
        e.preventDefault();

        $messageDiv.hide();

        const data = {};
        $form.serializeArray().forEach(item => {
            data[item.name] = item.value.trim();
        });

        $('#name, #email, #mobile').trigger('blur');

        if ($form.find('.is-invalid').length > 0) {
            showMessage('Please correct the errors in the form', true);
            return;
        }

        const $submitButton = $form.find('button[type="submit"]');
        $submitButton.prop('disabled', true).text('Registering...');

        $.ajax({
            url: 'register-event-ajax.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(result) {
                if (result.success) {
                    showMessage(result.message);
                    $form[0].reset();
                    $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                } else {
                    showMessage(result.message, true);
                }
            },
            error: function(result) {
                showMessage(result.responseJSON.message, true);
            },
            complete: function() {
                $submitButton.prop('disabled', false).text('Register for Event');
            }
        });
    });
});
