import Swal from 'sweetalert2';

window.onAfterAddToCart = response => {
    $('.cart-total').text(response.data.cartCount);
};

$(document).ready(() => {
    $.ajax({
        url : '/cart',
        success: function(r){
            $('.cart-total').text(r.count);
        }
    });

    $('.remove-cart-item').on('click', function(){
        let id = $(this).data('id');

        $.ajax({
            url : '/cart/remove',
            data: {id: id},
            method : 'POST',
            success: function(r){
                if(r.status === 'OK') {
                    window.location.reload();
                }else{
                    Swal.fire('Error', r.message, 'error');
                }
            }
        });
    });

    $('.rating-container').rating();
});