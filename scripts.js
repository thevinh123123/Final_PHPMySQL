$(document).ready(function(){
    $('.view-product').on('click', function(){
        var productId = $(this).data('id');
        $.ajax({
            url: 'get_product_details.php',
            type: 'GET',
            data: { id: productId },
            success: function(response) {
                $('#productDetails').html(response);
            },
            error: function() {
                $('#productDetails').html('<p>Đã xảy ra lỗi khi tải chi tiết sản phẩm.</p>');
            }
        });
    });
});
