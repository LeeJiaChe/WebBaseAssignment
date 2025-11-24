$(() => {

    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // No JS toggle for the user icon dropdown — menu is shown via CSS on hover/focus
    // Add to cart handler (stores simple cart in localStorage)
    $(document).on('click', '.add-to-cart', function () {
        const $btn = $(this);
        const item = {
            name: $btn.data('name'),
            price: parseFloat($btn.data('price')),
            image: $btn.data('image'),
            qty: 1
        };

        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch(e) { cart = []; }

        // simple merge: if item name exists increment qty
        const existing = cart.find(c => c.name === item.name);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push(item);
        }
        localStorage.setItem('cart', JSON.stringify(cart));

        // show small toast
        const $toast = $('<div class="cart-toast">Added to cart</div>');
        $('body').append($toast);
        $toast.css({position: 'fixed', right: '16px', bottom: '16px', background: '#222', color: '#fff', padding: '8px 12px', borderRadius: '6px', zIndex: 9999});
        setTimeout(() => { $toast.fadeOut(400, () => $toast.remove()); }, 1400);
        // update cart badge
        if (typeof updateCartBadge === 'function') updateCartBadge();

        // remember last added item name so drawer can highlight it
        try { localStorage.setItem('lastAdded', item.name); } catch(e){}

        // temporary disable button to avoid duplicate clicks and show feedback
        $btn.prop('disabled', true);
        const prevText = $btn.text();
        $btn.text('Added');
        setTimeout(() => { $btn.prop('disabled', false); $btn.text(prevText); }, 1200);

        // open cart drawer and render
        openCartDrawer();
    });

    // cart drawer creation/rendering
    function ensureCartDrawer() {
        if ($('#cartOverlay').length) return;
        const $overlay = $('<div id="cartOverlay" class="cart-overlay"></div>');
        const $drawer = $(
            '<div id="cartDrawer" class="cart-drawer" aria-hidden="true">' +
            '<div class="cart-header"><div><strong>Cart</strong></div><div><button id="cartClose" aria-label="Close" class="cart-close">&times;</button></div></div>' +
            '<div class="cart-body"><div id="cartDrawerItems"></div></div>' +
            '<div class="cart-footer"><div id="cartDrawerTotal"></div><div style="margin-top:8px"><button id="checkoutBtn" class="checkout-btn">CHECKOUT</button></div></div>' +
            '</div>'
        );
        $('body').append($overlay).append($drawer);

        $overlay.on('click', closeCartDrawer);
        $drawer.find('#cartClose').on('click', closeCartDrawer);
    }

    function renderCartDrawer() {
        ensureCartDrawer();
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch(e) { cart = []; }
        const $list = $('#cartDrawerItems');
        $list.empty();
        if (!cart.length) {
            $('#cartDrawerItems').append('<div class="cart-empty">Your cart is empty.</div>');
            $('#cartDrawerTotal').text('');
            return;
        }

        let total = 0;
        const lastAdded = localStorage.getItem('lastAdded');
        cart.forEach((it, idx) => {
            const subtotal = (it.price || 0) * (it.qty || 1);
            total += subtotal;
            const $row = $('<div class="cart-item" data-idx="'+idx+'"></div>');
            const $img = $('<img>').attr('src', it.image || '').attr('alt', it.name || '');
            const $meta = $('<div class="cart-item-meta"></div>');
            $meta.append('<div class="name">'+(it.name||'')+'</div>');

            // quantity controls
            const $qtyControls = $('<div class="qty-controls" data-idx="'+idx+'">' +
                '<button class="qty-decr" aria-label="Decrease">-</button>' +
                '<span class="qty-value">'+(it.qty||1)+'</span>' +
                '<button class="qty-incr" aria-label="Increase">+</button>' +
                '</div>');
            $meta.append($qtyControls);

            $meta.append('<div class="price">RM'+(it.price||0).toFixed(2)+'</div>');
            const $remove = $('<button class="remove-item">Remove</button>');
            $row.append($img).append($meta).append($remove);
            $list.append($row);

            if (lastAdded && it.name === lastAdded) {
                $row.addClass('just-added');
                setTimeout(() => $row.removeClass('just-added'), 1400);
            }
        });
        $('#cartDrawerTotal').text('Total: RM' + total.toFixed(2));
        const $checkout = $('#checkoutBtn');
        if ($checkout.length) {
            $checkout.prop('disabled', total <= 0);
            $checkout.text('CHECKOUT — RM' + total.toFixed(2));
        }

        // wire remove
        $('#cartDrawerItems').find('.remove-item').off('click').on('click', function(){
            const idx = $(this).closest('.cart-item').data('idx');
            let c = JSON.parse(localStorage.getItem('cart')||'[]');
            c.splice(idx,1);
            localStorage.setItem('cart', JSON.stringify(c));
            renderCartDrawer();
            if (typeof updateCartBadge === 'function') updateCartBadge();
        });

        // wire qty controls
        $('#cartDrawerItems').find('.qty-controls').off('click').on('click', 'button', function(e){
            const $btn = $(this);
            const idx = $btn.closest('.qty-controls').data('idx');
            let c = JSON.parse(localStorage.getItem('cart')||'[]');
            if (!c[idx]) return;
            if ($btn.hasClass('qty-incr')) {
                c[idx].qty = (c[idx].qty || 1) + 1;
            } else if ($btn.hasClass('qty-decr')) {
                c[idx].qty = Math.max(1, (c[idx].qty || 1) - 1);
            }
            localStorage.setItem('cart', JSON.stringify(c));
            renderCartDrawer();
            if (typeof updateCartBadge === 'function') updateCartBadge();
        });
    }

    function openCartDrawer() {
        ensureCartDrawer();
        $('#cartOverlay').fadeIn(160);
        $('#cartDrawer').addClass('open').attr('aria-hidden', 'false');
        renderCartDrawer();
    }

    function closeCartDrawer() {
        $('#cartOverlay').fadeOut(160);
        $('#cartDrawer').removeClass('open').attr('aria-hidden', 'true');
    }

    // open drawer when cart button clicked
    $(document).on('click', 'a.cart-button', function(e){
        e.preventDefault();
        openCartDrawer();
    });

    // drawer checkout button -> go to checkout page
    $(document).on('click', '#checkoutBtn', function(e){
        e.preventDefault();
        // if disabled, ignore
        if ($(this).prop('disabled')) return;
        window.location = 'checkout.php';
    });

    // exposed helper to update badge
    window.updateCartBadge = function () {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch(e) { cart = []; }
        const count = cart.reduce((s, i) => s + (i.qty || 0), 0);
        const $badge = $('#cartCount');
        if ($badge.length) {
            $badge.text(count);
            if (count <= 0) $badge.addClass('hidden'); else $badge.removeClass('hidden');
        }
    };

    // initialize badge on load
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        window.updateCartBadge();
    } else {
        $(window).on('load', () => window.updateCartBadge());
    }
});

