// app/js/app.js

$(() => {
  // --- Header 效果 ---

  $(window).on("scroll", function () {
    if ($(window).scrollTop() > 50) {
      $(".main-header").addClass("scrolled");
    } else {
      $(".main-header").removeClass("scrolled");
    }
  });

  if ($(window).scrollTop() > 50) $(".main-header").addClass("scrolled");

  // --- Product Card Click Handler ---
  $(document).on("click", ".product-card", function (e) {
    // Only navigate if clicking on the card itself, not on buttons
    if (!$(e.target).closest("button").length) {
      const productId = $(this).data("product-id");
      if (productId) {
        window.location.href = "product.php?id=" + productId;
      }
    }
  });

  // --- Add to Cart 处理 ---

  $(document).on("click", ".add-to-cart", function () {
    const $btn = $(this);

    addToLocalStorage($btn);

    // 显示提示

    const $toast = $('<div class="cart-toast">Added to cart</div>');

    $("body").append($toast);

    $toast.css({
      position: "fixed",
      top: "80px",
      left: "50%",
      transform: "translateX(-50%)",
      background: "#222",
      color: "#fff",

      padding: "12px 24px",
      borderRadius: "6px",
      zIndex: 9999,
    });

    setTimeout(() => {
      $toast.fadeOut(400, () => $toast.remove());
    }, 1400);

    updateCartBadge();

    openCartDrawer();
  });

  // --- 新增：Buy Now 处理 ---

  $(document).on("click", ".buy-now", function (e) {
    e.preventDefault();

    const $btn = $(this);

    addToLocalStorage($btn); // 先存入购物车

    window.location.href = "checkout.php"; // 直接跳去结账
  });

  // 公用存入 LocalStorage 函数

  function addToLocalStorage($btn) {
    const item = {
      id: $btn.data("id"),

      name: $btn.data("name"),

      price: parseFloat($btn.data("price")),

      image: $btn.data("image"),

      qty: 1,
    };

    let cart = [];

    try {
      cart = JSON.parse(localStorage.getItem("cart") || "[]");
    } catch (e) {
      cart = [];
    }

    const existing = cart.find((c) => c.id === item.id);

    if (existing) {
      existing.qty += 1;
    } else {
      cart.push(item);
    }

    localStorage.setItem("cart", JSON.stringify(cart));

    localStorage.setItem("lastAdded", item.name);
  }

  // --- 购物车侧栏逻辑 ---

  function ensureCartDrawer() {
    if ($("#cartOverlay").length) return;

    const $overlay = $('<div id="cartOverlay" class="cart-overlay"></div>');

    const $drawer = $(
      '<div id="cartDrawer" class="cart-drawer" aria-hidden="true">' +
        '<div class="cart-header"><div><strong>Cart</strong></div><div><button id="cartClose" class="cart-close">&times;</button></div></div>' +
        '<div class="cart-body"><div id="cartDrawerItems"></div></div>' +
        '<div class="cart-footer"><div id="cartDrawerTotal"></div><div style="margin-top:8px"><button id="checkoutBtn" class="checkout-btn">CHECKOUT</button></div></div>' +
        "</div>",
    );

    $("body").append($overlay).append($drawer);

    $overlay.on("click", closeCartDrawer);

    $("#cartClose").on("click", closeCartDrawer);
  }

  function renderCartDrawer() {
    ensureCartDrawer();

    let cart = JSON.parse(localStorage.getItem("cart") || "[]");

    const $list = $("#cartDrawerItems").empty();

    if (!cart.length) {
      $list.append('<div class="cart-empty">Your cart is empty.</div>');

      $("#cartDrawerTotal").text("");

      $("#checkoutBtn")
        .text("CHECKOUT")
        .prop("disabled", true)
        .css("opacity", "0.5")
        .css("cursor", "not-allowed");

      return;
    }

    let total = 0;

    cart.forEach((it, idx) => {
      total += it.price * it.qty;

      const $row = $(`<div class="cart-item" data-idx="${idx}">

        <img src="${it.image}">

        <div class="cart-item-meta">

          <div class="name">${it.name}</div>

          <div class="qty-controls">

            <button class="qty-decr">-</button><span>${it.qty}</span><button class="qty-incr">+</button>

          </div>

          <div class="price">RM${(it.price * it.qty).toFixed(2)}</div>

        </div>

        <button class="remove-item">Remove</button>

      </div>`);

      $list.append($row);
    });

    $("#cartDrawerTotal").text("Total: RM" + total.toFixed(2));

    $("#checkoutBtn")
      .text("CHECKOUT — RM" + total.toFixed(2))
      .prop("disabled", false)
      .css("opacity", "1")
      .css("cursor", "pointer");
  }

  function openCartDrawer() {
    renderCartDrawer();
    $("#cartOverlay").show();
    $("#cartDrawer").addClass("open");
  }

  function closeCartDrawer() {
    $("#cartOverlay").hide();
    $("#cartDrawer").removeClass("open");
  }

  $(document).on("click", "a.cart-button", (e) => {
    e.preventDefault();
    openCartDrawer();
  });

  $(document).on("click", "#checkoutBtn", () => {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (cart.length === 0) {
      return false;
    }
    window.location = "checkout.php";
  });

  // 数量加减逻辑

  $(document).on("click", ".qty-incr, .qty-decr", function () {
    const idx = $(this).closest(".cart-item").data("idx");

    let cart = JSON.parse(localStorage.getItem("cart") || "[]");

    if ($(this).hasClass("qty-incr")) cart[idx].qty++;
    else cart[idx].qty = Math.max(1, cart[idx].qty - 1);

    localStorage.setItem("cart", JSON.stringify(cart));

    renderCartDrawer();

    updateCartBadge();
  });

  $(document).on("click", ".remove-item", function () {
    const idx = $(this).closest(".cart-item").data("idx");

    let cart = JSON.parse(localStorage.getItem("cart") || "[]");

    cart.splice(idx, 1);

    localStorage.setItem("cart", JSON.stringify(cart));

    renderCartDrawer();

    updateCartBadge();
  });

  window.updateCartBadge = function () {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");

    const count = cart.reduce((s, i) => s + i.qty, 0);

    $("#cartCount")
      .text(count)
      .toggleClass("hidden", count <= 0);
  };

  updateCartBadge();
  // --- User Dropdown Logic ---
  $(document).on("click", "#userIconButton", function (e) {
    e.stopPropagation(); // Prevent event from bubbling to document
    $("#userIconMenu").toggleClass("show"); // Toggle visibility
  });

  // Close dropdown when clicking outside
  $(document).on("click", function (e) {
    if (!$(e.target).closest("#userIconDropdown").length) {
      $("#userIconMenu").removeClass("show");
    }
  });
});
