<?php
require '_base.php';
session_start();
$_title = 'Thank You';
include '_head.php';
?>

<section class="content">
    <h1>Thank you for your order</h1>
    <div id="thankDetails">Loading order...</div>
</section>

<script>
$(function(){
    let last = null;
    try { last = JSON.parse(localStorage.getItem('lastOrder') || 'null'); } catch(e){ last = null; }
    const $d = $('#thankDetails');
    if (!last) { $d.html('<p>No order information found.</p>'); return; }

    const meta = last.meta || {};
    const $info = $('<div></div>');
    $info.append('<div><strong>Email:</strong> '+(meta.email||'')+'</div>');
    $info.append('<div><strong>Delivery:</strong> Ship</div>');
    $info.append('<div style="margin-top:8px;"><strong>Items</strong></div>');
    const $list = $('<div></div>');
    (meta.cart||[]).forEach(it => {
        $list.append('<div style="display:flex;gap:8px;margin:6px 0;"><img src="'+(it.image||'')+'" style="width:60px;height:44px;object-fit:cover;"/>'+
            '<div><div style="font-weight:600">'+(it.name||'')+'</div><div>RM'+parseFloat(it.price||0).toFixed(2)+' × '+(it.qty||1)+'</div></div></div>');
    });
    $info.append($list);
    $d.empty().append($info);
});
</script>

<?php include '_foot.php'; ?>
