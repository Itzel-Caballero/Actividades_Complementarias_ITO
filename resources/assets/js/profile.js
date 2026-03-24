$(document).on('click', '.edit-profile', function (event) {
    $('#editProfileUserId').val(loggedInUser.id);
    $('#pfName').val(loggedInUser.name);
    $('#pfEmail').val(loggedInUser.email);
    $('#EditProfileModal').appendTo('body').modal('show');
});

$(document).on('change', '#pfImage', function () {
    let ext = $(this).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $(this).val('');
        $('#editProfileValidationErrorsBox').
            html(
                'The profile image must be a file of type: jpeg, jpg, png.').
            show();
    } else {
        displayPhoto(this, '#edit_preview_photo');
    }
});

window.displayPhoto = function (input, selector) {
    let displayPreview = true
    if (input.files && input.files[0]) {
        let reader = new FileReader()
        reader.onload = function (e) {
            let image = new Image()
            image.src = e.target.result
            image.onload = function () {
                $(selector).attr('src', e.target.result)
                displayPreview = true
            }
        }
        if (displayPreview) {
            reader.readAsDataURL(input.files[0])
            $(selector).show()
        }
    }
}

$(document).on('submit', '#editProfileForm', function (event) {
    event.preventDefault();
    let userId = $('#editProfileUserId').val();
    var loadingButton = jQuery(this).find('#btnPrEditSave');
    loadingButton.button('loading');
    $.ajax({
        url: usersUrl + '/' + userId,
        type: 'post',
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success: function success(result) {
            if (result.success) {
                // Si hay campo de carrera (alumno), guardarlo también
                let idCarrera = $('#pfCarrera').val();
                if (idCarrera) {
                    $.ajax({
                        url: '/perfil/carrera',
                        type: 'POST',
                        data: {
                            id_carrera: idCarrera,
                            _token: $('input[name="_token"]').first().val()
                        },
                        complete: function () {
                            $('#EditProfileModal').modal('hide');
                            setTimeout(function () { location.reload(); }, 1000);
                        }
                    });
                } else {
                    $('#EditProfileModal').modal('hide');
                    setTimeout(function () { location.reload(); }, 1500);
                }
            }
        },
        error: function error(result) {
            console.log(result);
        },
        complete: function complete() {
            loadingButton.button('reset');
        }
    });
});
