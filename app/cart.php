<?php
require '_base.php';
$_title = 'Shopping Cart';
include '_head.php';
?>

<section class="content">
    <h1 class="cart-page-title">Cart</h1>
    <div id="cartView">
        <p>Loading cart...</p>
    </div>
</section>

<script>
(function(){
    function render() {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch(e){ cart = []; }
        const $view = $('#cartView');
        $view.empty();
        if (!cart.length) {
            $view.append('<p>Your cart is empty. <a href="index.php">Continue shopping</a>.</p>');
            if (typeof updateCartBadge === 'function') updateCartBadge();
            return;
        }
        let total = 0;
        const $table = $('<div class="cart-list"></div>');
        cart.forEach((it, idx) => {
            const subtotal = (it.price || 0) * (it.qty || 1);
            total += subtotal;
            const $row = $(
                '<div class="cart-item" data-idx="'+idx+'">' +
                '<img src="'+(it.image||'')+'" style="width:90px;height:60px;object-fit:cover;margin-right:12px;border:1px solid #e6e6e6;padding:4px;"/>' +
                '<div style="flex:1">' +
                '<div style="font-weight:600">'+(it.name||'')+'</div>' +
                    '<div>RM'+(it.price||0).toFixed(2)+' x <span class="qty">'+(it.qty||1)+'</span></div>' +
                '<div style="margin-top:8px"><button class="btn remove-item">Remove</button></div>' +
                '</div>' +
                '</div>'
            );
            $table.append($row);
        });
        const $total = $('<div style="margin-top:12px;font-weight:700">Total: RM'+total.toFixed(2)+'</div>');
        $view.append($table);
        $view.append($total);
        // Checkout button showing total
        const $checkout = $('<div style="margin-top:8px;"><button id="checkoutBtnPage" class="checkout-btn">CHECKOUT — RM'+total.toFixed(2)+'</button></div>');
        $view.append($checkout);

        // wire checkout click to a placeholder (replace with real checkout flow)
        $view.find('#checkoutBtnPage').on('click', function(){
            // for now navigate to a checkout page if exists
            window.location = 'checkout.php';
        });

        // wire remove
        $view.find('.remove-item').on('click', function(){
            const idx = $(this).closest('.cart-item').data('idx');
            let c = JSON.parse(localStorage.getItem('cart')||'[]');
            c.splice(idx,1);
            localStorage.setItem('cart', JSON.stringify(c));
            render();
            if (typeof updateCartBadge === 'function') updateCartBadge();
        });
    }

    $(function(){ render(); });
})();
</script>

<?php include '_foot.php';
?>
