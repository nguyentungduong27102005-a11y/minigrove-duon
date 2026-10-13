import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/*
 * Wishlist quick action.
 *
 * Product cards stay at the current scroll position because
 * adding/removing a favorite is handled without a page reload.
 */
document.addEventListener('submit', async (event) => {
    const form = event.target.closest(
        'form[data-wishlist-toggle]'
    );

    if (!form) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector(
        '.product-card-wishlist'
    );

    if (!button || button.disabled) {
        return;
    }

    const wasWishlisted =
        button.classList.contains('active');

    const url = wasWishlisted
        ? form.dataset.destroyUrl
        : form.dataset.storeUrl;

    const method = wasWishlisted
        ? 'DELETE'
        : 'POST';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    button.disabled = true;
    button.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(
            url,
            {
                method,
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken ?? '',
                },
                credentials: 'same-origin',
            }
        );

        if (!response.ok) {
            throw new Error(
                'Wishlist request failed.'
            );
        }

        const data = await response.json();
        const wishlisted = Boolean(
            data.wishlisted
        );

        button.classList.toggle(
            'active',
            wishlisted
        );

        button.title = wishlisted
            ? 'Bỏ khỏi yêu thích'
            : 'Thêm vào yêu thích';

        button.setAttribute(
            'aria-label',
            button.title
        );

        form.action = wishlisted
            ? form.dataset.destroyUrl
            : form.dataset.storeUrl;

        let methodInput = form.querySelector(
            'input[name="_method"]'
        );

        if (wishlisted) {
            if (!methodInput) {
                methodInput =
                    document.createElement(
                        'input'
                    );

                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }

            methodInput.value = 'DELETE';
        } else if (methodInput) {
            methodInput.remove();
        }
    } catch (error) {
        console.error(error);
    } finally {
        button.disabled = false;
        button.removeAttribute('aria-busy');
    }
});

/*
 * Entire product card opens the product detail page.
 *
 * Interactive controls inside the card (wishlist button/form and
 * existing links) keep their own behavior and do not trigger the
 * card navigation.
 */
document.addEventListener('click', (event) => {
    const card = event.target.closest(
        '[data-product-card]'
    );

    if (!card) {
        return;
    }

    const interactiveTarget = event.target.closest(
        'a, button, form, input, select, textarea, label'
    );

    if (interactiveTarget) {
        return;
    }

    const productUrl = card.dataset.productUrl;

    if (productUrl) {
        window.location.assign(productUrl);
    }
});

document.addEventListener('keydown', (event) => {
    const card = event.target.closest(
        '[data-product-card]'
    );

    if (
        !card
        || event.target !== card
        || !['Enter', ' '].includes(event.key)
    ) {
        return;
    }

    event.preventDefault();

    const productUrl = card.dataset.productUrl;

    if (productUrl) {
        window.location.assign(productUrl);
    }
});
