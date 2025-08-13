// Favourites functionality
class FavouritesManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateFavouriteButtons();
    }

    bindEvents() {
        // Bind favourite button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.favourite-btn')) {
                e.preventDefault();
                const button = e.target.closest('.favourite-btn');
                const productId = button.dataset.productId;
                this.toggleFavourite(productId, button);
            }
        });
    }

    async toggleFavourite(productId, button) {
        try {
            const isFavourited = button.classList.contains('favourited');
            
            if (isFavourited) {
                await this.removeFromFavourites(productId, button);
            } else {
                await this.addToFavourites(productId, button);
            }
        } catch (error) {
            console.error('Error toggling favourite:', error);
            this.showFlashMessage('Ralat mengemas kini kegemaran', 'error');
        }
    }

    async addToFavourites(productId, button) {
        try {
            const response = await fetch('/favourites/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();

            if (data.success) {
                button.classList.add('favourited');
                button.innerHTML = this.getFavouritedIcon();
                this.showFlashMessage('Produk berjaya ditambah ke kegemaran!', 'success');
                this.updateFavouritesCount();
            } else {
                this.showFlashMessage(data.message || 'Ralat menambah ke kegemaran', 'error');
            }
        } catch (error) {
            console.error('Error adding to favourites:', error);
            this.showFlashMessage('Ralat menambah ke kegemaran', 'error');
        }
    }

    async removeFromFavourites(productId, button) {
        try {
            const response = await fetch('/favourites/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();

            if (data.success) {
                button.classList.remove('favourited');
                button.innerHTML = this.getUnfavouritedIcon();
                this.showFlashMessage('Produk berjaya dikeluarkan dari kegemaran', 'success');
                this.updateFavouritesCount();
            } else {
                this.showFlashMessage(data.message || 'Ralat mengeluarkan dari kegemaran', 'error');
            }
        } catch (error) {
            console.error('Error removing from favourites:', error);
            this.showFlashMessage('Ralat mengeluarkan dari kegemaran', 'error');
        }
    }

    async updateFavouriteButtons() {
        const buttons = document.querySelectorAll('.favourite-btn');
        
        for (const button of buttons) {
            const productId = button.dataset.productId;
            try {
                const response = await fetch(`/favourites/check?product_id=${productId}`);
                const data = await response.json();
                
                if (data.success && data.is_favourited) {
                    button.classList.add('favourited');
                    button.innerHTML = this.getFavouritedIcon();
                }
            } catch (error) {
                console.error('Error checking favourite status:', error);
            }
        }
    }

    async updateFavouritesCount() {
        try {
            const response = await fetch('/favourites/count');
            const data = await response.json();
            
            if (data.success) {
                const countElement = document.querySelector('.favourites-count');
                if (countElement) {
                    countElement.textContent = data.count;
                    countElement.style.display = data.count > 0 ? 'inline' : 'none';
                }
            }
        } catch (error) {
            console.error('Error updating favourites count:', error);
        }
    }

    getFavouritedIcon() {
        return `
            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
            </svg>
        `;
    }

    getUnfavouritedIcon() {
        return `
            <svg class="w-5 h-5 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        `;
    }

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    showFlashMessage(message, type = 'success') {
        // Create a temporary form to submit and redirect with flash message
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/favourites/flash-message';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = this.getCsrfToken();
        form.appendChild(csrfInput);
        
        // Add message and type
        const messageInput = document.createElement('input');
        messageInput.type = 'hidden';
        messageInput.name = 'message';
        messageInput.value = message;
        form.appendChild(messageInput);
        
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'type';
        typeInput.value = type;
        form.appendChild(typeInput);
        
        // Add current URL for redirect
        const urlInput = document.createElement('input');
        urlInput.type = 'hidden';
        urlInput.name = 'redirect_url';
        urlInput.value = window.location.href;
        form.appendChild(urlInput);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize favourites manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new FavouritesManager();
});

// Export for use in other scripts
window.FavouritesManager = FavouritesManager; 