document.addEventListener('DOMContentLoaded', function () {
    const picker = document.getElementById(
        'checkoutVoucherPicker'
    );

    const input = document.getElementById(
        'checkoutVoucherInput'
    );

    const list = document.getElementById(
        'checkoutSavedVoucherList'
    );

    if (!picker || !input || !list) {
        return;
    }

    const options = Array.from(
        list.querySelectorAll(
            '[data-saved-voucher-option]'
        )
    );

    const openList = function () {
        list.classList.add(
            'is-open'
        );
        input.setAttribute(
            'aria-expanded',
            'true'
        );
    };

    const closeList = function () {
        list.classList.remove(
            'is-open'
        );
        input.setAttribute(
            'aria-expanded',
            'false'
        );
    };

    const filterOptions = function () {
        const keyword = input.value
            .trim()
            .toLowerCase();

        options.forEach(function (option) {
            const searchValue =
                option.dataset.voucherSearch || '';

            option.hidden =
                keyword !== ''
                && !searchValue.includes(keyword);
        });
    };

    input.addEventListener('focus', openList);
    input.addEventListener('click', openList);

    input.addEventListener(
        'input',
        function () {
            filterOptions();
            openList();
        }
    );

    options.forEach(function (option) {
        option.addEventListener(
            'click',
            function () {
                if (option.disabled) {
                    return;
                }

                input.value =
                    option.dataset.voucherCode || '';

                filterOptions();
                closeList();
                input.focus({
                    preventScroll: true
                });
            }
        );
    });

    document.addEventListener(
        'click',
        function (event) {
            if (!picker.contains(event.target)) {
                closeList();
            }
        }
    );

    input.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Escape') {
                closeList();
            }
        }
    );
});