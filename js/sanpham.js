
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sort');
    sortSelect.addEventListener('change', function() {
        const sortValue = this.value;
        const sections = document.querySelectorAll('.product-list');

        sections.forEach(section => {
            const products = Array.from(section.querySelectorAll('.product-card'));
            const video = section.querySelector('video');

            products.sort((a, b) => {
                const priceA = parseInt(a.querySelector('.price').textContent.replace(/\D/g, ''));
                const priceB = parseInt(b.querySelector('.price').textContent.replace(/\D/g, ''));
                return sortValue === 'price-asc' ? priceA - priceB : priceB - priceA;
            });

            const productGrid = section.querySelector('.product-grid');
            productGrid.innerHTML = '';
            if (video) {
                productGrid.appendChild(video);
            }
            products.forEach(product => productGrid.appendChild(product));
        });
    });
});
