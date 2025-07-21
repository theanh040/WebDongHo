function toggleCart() {
    const cartOverlay = document.getElementById('cartOverlay');
    cartOverlay.classList.toggle('active');
}

// Xử lý nút tăng giảm số lượng
document.querySelectorAll('.quantity-btn').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input');
        const currentValue = parseInt(input.value);
        
        if(this.classList.contains('plus')) {
            input.value = currentValue + 1;
        } else if(this.classList.contains('minus') && currentValue > 1) {
            input.value = currentValue - 1;
        }
        updateCartTotal();
    });
});

// Xử lý nút xóa sản phẩm
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function() {
        this.closest('.cart-item').remove();
        updateCartTotal();
    });
});

// Cập nhật tổng tiền
function updateCartTotal() {
    let total = 0;
    const cartItems = document.querySelectorAll('.cart-item');
    
    cartItems.forEach(item => {
        // Lấy giá tiền từ text, loại bỏ "₫" và dấu phẩy
        const priceText = item.querySelector('.cart-item-price').textContent;
        // Chuyển đổi chuỗi giá tiền thành số
        const price = parseInt(priceText.replace(/[₫,.]/g, ''));
        
        // Lấy số lượng
        const quantity = parseInt(item.querySelector('.cart-item-quantity input').value);
        
        // Cộng vào tổng
        total += price * quantity;
    });

    // Format tổng tiền với dấu phẩy ngăn cách hàng nghìn
    const formattedTotal = new Intl.NumberFormat('vi-VN', {
        style: 'decimal',
        maximumFractionDigits: 0
    }).format(total);
    
    // Cập nhật hiển thị tổng tiền
    document.querySelector('.total-amount').textContent = formattedTotal + '₫';

    // Cập nhật số lượng sản phẩm trong giỏ hàng (nếu có)
    const itemCount = cartItems.length;
    updateCartCount(itemCount);
}

// Thêm hàm cập nhật số lượng sản phẩm trên icon giỏ hàng
function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'block' : 'none';
    }
}

// Thêm event listener cho input số lượng
document.querySelectorAll('.cart-item-quantity input').forEach(input => {
    input.addEventListener('change', function() {
        // Giới hạn giá trị nhập vào
        if (this.value < 1) this.value = 1;
        if (this.value > 99) this.value = 99;
        updateCartTotal();
    });
});

// Gọi hàm updateCartTotal khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    updateCartTotal();
});

function addToCart() {
    // Lấy thông tin sản phẩm từ trang chi tiết
    const productName = document.querySelector('.product-info h1').textContent;
    const productPrice = document.querySelector('.product-info .price').textContent;
    // Chuyển đổi giá tiền thành số nguyên trước khi thêm vào giỏ hàng
    const priceNumber = parseInt(productPrice.replace(/[₫,.]/g, ''));
    const formattedPrice = new Intl.NumberFormat('vi-VN', {
        style: 'decimal',
        maximumFractionDigits: 0
    }).format(priceNumber) + '₫';
    
    const productImage = document.querySelector('.main-image img').src;
    const quantity = document.querySelector('.quantity-selector input').value;

    // Tạo HTML cho item mới trong giỏ hàng với giá đã được format
    const cartItem = `
        <div class="cart-item">
            <img src="${productImage}" alt="${productName}" class="cart-item-image">
            <div class="cart-item-details">
                <h3>${productName}</h3>
                <p class="cart-item-price">${formattedPrice}</p>
                <div class="cart-item-quantity">
                    <button class="quantity-btn minus">-</button>
                    <input type="number" value="${quantity}" min="1" max="99">
                    <button class="quantity-btn plus">+</button>
                </div>
            </div>
            <button class="remove-item">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;

    // Thêm item vào giỏ hàng
    const cartItems = document.querySelector('.cart-items');
    cartItems.insertAdjacentHTML('beforeend', cartItem);

    // Cập nhật tổng tiền
    updateCartTotal();

    // Thêm lại event listeners cho các nút mới
    initializeCartItemEvents();
}

function initializeCartItemEvents() {
    // Cập nhật event listeners cho nút tăng/giảm số lượng
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const currentValue = parseInt(input.value);
            
            if(this.classList.contains('plus')) {
                input.value = currentValue + 1;
            } else if(this.classList.contains('minus') && currentValue > 1) {
                input.value = currentValue - 1;
            }
            updateCartTotal();
        });
    });

    // Cập nhật event listeners cho nút xóa
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.cart-item').remove();
            updateCartTotal();
        });
    });

    // Cập nhật event listeners cho input số lượng
    document.querySelectorAll('.cart-item-quantity input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 99) this.value = 99;
            updateCartTotal();
        });
    });
} 