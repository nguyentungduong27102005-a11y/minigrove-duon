document.addEventListener(
    'DOMContentLoaded',
    function () {

        const itemCheckboxes =
            Array.from(
                document.querySelectorAll(
                    '.cart-item-checkbox'
                )
            );

        const selectAllTop =
            document.getElementById(
                'selectAllTop'
            );

        const selectAllBottom =
            document.getElementById(
                'selectAllBottom'
            );

        const selectedTotal =
            document.getElementById(
                'selectedTotal'
            );

        const selectedCountText =
            document.getElementById(
                'selectedCountText'
            );

        const buyButton =
            document.getElementById(
                'buySelectedButton'
            );

        const deleteButton =
            document.getElementById(
                'deleteSelectedButton'
            );

        const wishlistButton =
            document.getElementById(
                'saveWishlistButton'
            );

        const deleteInputs =
            document.getElementById(
                'deleteSelectedInputs'
            );

        const checkoutInputs =
    document.getElementById(
        'checkoutSelectedInputs'
    );

        /*
        |--------------------------------------------------------------------------
        | FORMAT MONEY
        |--------------------------------------------------------------------------
        */

        function formatMoney(value) {

            return new Intl.NumberFormat(
                'vi-VN'
            ).format(value) + 'đ';
        }


        /*
        |--------------------------------------------------------------------------
        | SELECTED ITEMS
        |--------------------------------------------------------------------------
        */

        function getSelectedItems() {

            return itemCheckboxes.filter(
                checkbox =>
                    checkbox.checked
            );
        }

        function getWishlistCandidates() {

            return getSelectedItems().filter(
                checkbox =>
                    checkbox.dataset.itemType
                        === 'product'
                    && checkbox.dataset.wishlisted
                        !== '1'
                    && Boolean(
                        checkbox.dataset.wishlistUrl
                    )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SELECT ALL
        |--------------------------------------------------------------------------
        */

        function syncSelectAll() {

            if (
                itemCheckboxes.length === 0
            ) {
                return;
            }


            const checkedCount =
                getSelectedItems().length;


            const allSelected =
                checkedCount ===
                itemCheckboxes.length;


            if (selectAllTop) {

                selectAllTop.checked =
                    allSelected;

                selectAllTop.indeterminate =
                    checkedCount > 0
                    && !allSelected;
            }


            if (selectAllBottom) {

                selectAllBottom.checked =
                    allSelected;

                selectAllBottom.indeterminate =
                    checkedCount > 0
                    && !allSelected;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE TOTAL
        |--------------------------------------------------------------------------
        */

        function updateSummary() {

            const selected =
                getSelectedItems();


            let total = 0;

            let totalQuantity = 0;


            selected.forEach(
                checkbox => {

                    total += Number(
                        checkbox.dataset.subtotal
                        || 0
                    );


                    totalQuantity += Number(
                        checkbox.dataset.quantity
                        || 0
                    );
                }
            );


            if (selectedTotal) {

                selectedTotal.textContent =
                    formatMoney(total);
            }


            if (selectedCountText) {

                selectedCountText.textContent =
                    'Tổng cộng ('
                    + totalQuantity
                    + ' sản phẩm):';
            }


            if (buyButton) {

                buyButton.disabled =
                    selected.length === 0;
            }


            if (deleteButton) {

                deleteButton.disabled =
                    selected.length === 0;
            }

            if (wishlistButton) {

                const selectedProducts =
                    selected.filter(
                        checkbox =>
                            checkbox.dataset.itemType
                                === 'product'
                    );

                const wishlistCandidates =
                    getWishlistCandidates();

                wishlistButton.disabled =
                    wishlistCandidates.length === 0;

                if (
                    selectedProducts.length > 0
                    && wishlistCandidates.length === 0
                ) {
                    wishlistButton.textContent =
                        '♥ Đã có trong mục đã thích';

                    wishlistButton.title =
                        'Các sản phẩm đang chọn đã có trong mục đã thích.';
                } else {
                    wishlistButton.textContent =
                        '♡ Lưu vào mục đã thích';

                    wishlistButton.title =
                        wishlistCandidates.length > 0
                            ? 'Lưu các sản phẩm đang chọn vào mục đã thích.'
                            : 'Chọn sản phẩm để lưu vào mục đã thích.';
                }
            }


            /*
             * Chuẩn bị CartItem ID
             * cho chức năng Xóa đã chọn.
             */
            if (deleteInputs) {

                deleteInputs.innerHTML = '';


                selected.forEach(
                    checkbox => {

                        const input =
                            document.createElement(
                                'input'
                            );


                        input.type = 'hidden';

                        input.name =
                            'selected_items[]';

                        input.value =
                            checkbox.value;


                        deleteInputs.appendChild(
                            input
                        );
                    }
                );
            }

            /*
 * Chuẩn bị ID CartItem cho Checkout.
 */
if (checkoutInputs) {

    checkoutInputs.innerHTML = '';


    selected.forEach(
        checkbox => {

            const input =
                document.createElement(
                    'input'
                );


            input.type = 'hidden';

            input.name =
                'selected_items[]';

            input.value =
                checkbox.value;


            checkoutInputs.appendChild(
                input
            );
        }
    );
}

            syncSelectAll();
        }


        /*
        |--------------------------------------------------------------------------
        | CHECKBOX ITEM
        |--------------------------------------------------------------------------
        */

        itemCheckboxes.forEach(
            checkbox => {

                checkbox.addEventListener(
                    'change',
                    updateSummary
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | CHECK ALL
        |--------------------------------------------------------------------------
        */

        function setAll(checked) {

            itemCheckboxes.forEach(
                checkbox => {

                    checkbox.checked =
                        checked;
                }
            );


            updateSummary();
        }


        if (selectAllTop) {

            selectAllTop.addEventListener(
                'change',
                function () {

                    setAll(
                        selectAllTop.checked
                    );
                }
            );
        }


        if (selectAllBottom) {

            selectAllBottom.addEventListener(
                'change',
                function () {

                    setAll(
                        selectAllBottom.checked
                    );
                }
            );
        }

        if (wishlistButton) {

            wishlistButton.addEventListener(
                'click',
                async function () {

                    const candidates =
                        getWishlistCandidates();

                    if (candidates.length === 0) {
                        return;
                    }

                    const csrfToken = document
                        .querySelector(
                            'meta[name="csrf-token"]'
                        )
                        ?.getAttribute('content');

                    wishlistButton.disabled = true;
                    wishlistButton.textContent =
                        'Đang lưu...';

                    const results =
                        await Promise.allSettled(
                            candidates.map(
                                async checkbox => {

                                    const response =
                                        await fetch(
                                            checkbox
                                                .dataset
                                                .wishlistUrl,
                                            {
                                                method: 'POST',
                                                headers: {
                                                    Accept:
                                                        'application/json',
                                                    'X-Requested-With':
                                                        'XMLHttpRequest',
                                                    'X-CSRF-TOKEN':
                                                        csrfToken
                                                        ?? '',
                                                },
                                                credentials:
                                                    'same-origin',
                                            }
                                        );

                                    if (!response.ok) {
                                        throw new Error(
                                            'Không thể lưu sản phẩm vào Wishlist.'
                                        );
                                    }

                                    const data =
                                        await response
                                            .json();

                                    if (data.wishlisted) {
                                        checkbox
                                            .dataset
                                            .wishlisted =
                                            '1';
                                    }

                                    return data;
                                }
                            )
                        );

                    const failed =
                        results.filter(
                            result =>
                                result.status
                                    === 'rejected'
                        );

                    if (failed.length > 0) {
                        console.error(
                            'Một số sản phẩm chưa thể lưu vào Wishlist.',
                            failed
                        );
                    }

                    updateSummary();
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX UPDATE QUANTITY
        |--------------------------------------------------------------------------
        |
        | Không reload trang.
        |
        | Checkbox đang tick vẫn giữ nguyên.
        | Giá và tổng tiền cập nhật ngay.
        |
        */

        async function updateQuantity(
            form,
            newQuantity
        ) {

            const row =
                form.closest(
                    '.cart-item-row'
                );


            const input =
                form.querySelector(
                    '.cart-quantity-input'
                );


            const checkbox =
                row.querySelector(
                    '.cart-item-checkbox'
                );


            const subtotalElement =
                row.querySelector(
                    '.cart-subtotal'
                );


            const minusButton =
                form.querySelector(
                    '.quantity-minus'
                );


            const plusButton =
                form.querySelector(
                    '.quantity-plus'
                );


            /*
             * Lưu quantity cũ để phục hồi
             * nếu Server báo lỗi.
             */
            const oldQuantity =
                Number(input.value);


            /*
             * Không gửi số lượng < 1.
             */
            if (newQuantity < 1) {

                return;
            }


            /*
             * Không vượt stock.
             */
            const max =
                Number(input.max);


            if (
                max > 0
                && newQuantity > max
            ) {

                return;
            }


            /*
             * Tạm hiển thị số mới ngay.
             */
            input.value =
                newQuantity;


            /*
             * Khóa nút trong lúc gửi request.
             */
            minusButton.disabled = true;
            plusButton.disabled = true;


            try {

                const formData =
                    new FormData(form);


                /*
                 * Ghi quantity mới vào FormData.
                 */
                formData.set(
                    'quantity',
                    newQuantity
                );


                const response =
                    await fetch(
                        form.action,
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body: formData,
                        }
                    );


                const data =
                    await response.json();


                /*
                 * Validation / Server error.
                 */
                if (!response.ok) {

                    let message =
                        'Không thể cập nhật giỏ hàng.';


                    if (
                        data.errors
                        && data.errors.quantity
                    ) {

                        message =
                            data.errors.quantity[0];
                    }


                    throw new Error(
                        message
                    );
                }


                /*
                 * Quantity chính xác từ Server.
                 */
                input.value =
                    data.item.quantity;


                /*
                 * Cập nhật thành tiền của dòng.
                 */
                subtotalElement.textContent =
                    formatMoney(
                        data.item.subtotal
                    );


                /*
                 * Đây là phần quan trọng:
                 *
                 * checkbox vẫn giữ nguyên trạng thái,
                 * nhưng dữ liệu của nó được cập nhật.
                 */
                checkbox.dataset.quantity =
                    data.item.quantity;


                checkbox.dataset.subtotal =
                    data.item.subtotal;


                /*
                 * Nếu sản phẩm đang được tick,
                 * tổng tiền sẽ thay đổi ngay.
                 *
                 * Nếu chưa tick thì tổng không đổi.
                 */
                updateSummary();


            } catch (error) {

                /*
                 * Nếu lỗi:
                 * phục hồi quantity cũ.
                 */
                input.value =
                    oldQuantity;


                alert(
                    error.message
                );

            } finally {

                /*
                 * Mở lại nút.
                 */
                minusButton.disabled = false;
                plusButton.disabled = false;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MINUS
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '.quantity-minus'
            )
            .forEach(
                button => {

                    button.addEventListener(
                        'click',
                        function () {

                            const form =
                                button.closest(
                                    '.cart-quantity-form'
                                );


                            const input =
                                form.querySelector(
                                    '.cart-quantity-input'
                                );


                            const current =
                                Number(
                                    input.value
                                );


                            if (current <= 1) {

                                return;
                            }


                            updateQuantity(
                                form,
                                current - 1
                            );
                        }
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | PLUS
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '.quantity-plus'
            )
            .forEach(
                button => {

                    button.addEventListener(
                        'click',
                        function () {

                            const form =
                                button.closest(
                                    '.cart-quantity-form'
                                );


                            const input =
                                form.querySelector(
                                    '.cart-quantity-input'
                                );


                            const current =
                                Number(
                                    input.value
                                );


                            const max =
                                Number(
                                    input.max
                                );


                            if (
                                max > 0
                                && current >= max
                            ) {

                                return;
                            }


                            updateQuantity(
                                form,
                                current + 1
                            );
                        }
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | DELETE SELECTED
        |--------------------------------------------------------------------------
        */

        const deleteSelectedForm =
            document.getElementById(
                'deleteSelectedForm'
            );


        if (deleteSelectedForm) {

            deleteSelectedForm.addEventListener(
                'submit',
                function (event) {

                    const selected =
                        getSelectedItems();


                    if (
                        selected.length === 0
                    ) {

                        event.preventDefault();

                        return;
                    }


                    const confirmed =
                        confirm(
                            'Bạn muốn xóa các sản phẩm đã chọn khỏi giỏ hàng?'
                        );


                    if (!confirmed) {

                        event.preventDefault();
                    }
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INITIAL
        |--------------------------------------------------------------------------
        */

        updateSummary();

    }
);