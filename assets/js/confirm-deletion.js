import Swal from 'sweetalert2';

$(document).on('submit', '.js-form-delete', function(e){
    e.preventDefault();
    let form = $(this);

    Swal.fire({
        title: 'Confirm deletion',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        type : 'warning',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                method : 'DELETE',
                data : form.serialize(),
                url : form.attr('action'),
                success : (response) => {
                    if(response.cb){
                        response.cb(response);
                    }

                    if(response.message){
                        Swal.fire(response.status === 'OK' ? 'Success' : 'Error', response.message, response.status === 'OK' ? 'success' : 'error');
                    }

                    if(response.redirect){
                        window.location.href = response.redirect;
                    }
                }
            });
        }
    });
});