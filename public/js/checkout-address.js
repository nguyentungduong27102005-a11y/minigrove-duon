document.addEventListener(
    'DOMContentLoaded',
    function () {

        const provinceSelect =
            document.getElementById(
                'checkoutProvince'
            );

        const districtSelect =
            document.getElementById(
                'checkoutDistrict'
            );

        const wardSelect =
            document.getElementById(
                'checkoutWard'
            );

        /*
         * Hidden input chứa mã GHN.
         *
         * Hai input này phải tồn tại trong
         * checkout/index.blade.php:
         *
         * #ghnDistrictId
         * #ghnWardCode
         */
        const ghnDistrictIdInput =
            document.getElementById(
                'ghnDistrictId'
            );

        const ghnWardCodeInput =
            document.getElementById(
                'ghnWardCode'
            );


        /*
         * Nếu trang hiện tại không có
         * form địa chỉ thì dừng.
         */
        if (
            !provinceSelect ||
            !districtSelect ||
            !wardSelect
        ) {
            return;
        }


        /*
         |--------------------------------------------------------------------------
         | API BACKEND MINI GROVE
         |--------------------------------------------------------------------------
         |
         | Không gọi GHN trực tiếp từ trình duyệt.
         | GHN Token được giữ ở Backend.
         |
         */

        const API_BASE =
            '/checkout/ghn';


        /*
         |--------------------------------------------------------------------------
         | OLD VALUES
         |--------------------------------------------------------------------------
         */

        const oldProvince =
            provinceSelect.dataset.old || '';

        const oldDistrict =
            districtSelect.dataset.old || '';

        const oldWard =
            wardSelect.dataset.old || '';


        /*
         |--------------------------------------------------------------------------
         | HELPER - RESET SELECT
         |--------------------------------------------------------------------------
         */

        function resetSelect(
            select,
            placeholder
        ) {

            select.innerHTML = '';

            const option =
                document.createElement(
                    'option'
                );

            option.value = '';

            option.textContent =
                placeholder;

            select.appendChild(
                option
            );

            select.disabled = true;
        }


        /*
         |--------------------------------------------------------------------------
         | HELPER - LOADING
         |--------------------------------------------------------------------------
         */

        function showLoading(
            select,
            text
        ) {

            select.innerHTML = '';

            const option =
                document.createElement(
                    'option'
                );

            option.value = '';

            option.textContent = text;

            select.appendChild(
                option
            );

            select.disabled = true;
        }


        /*
         |--------------------------------------------------------------------------
         | HELPER - ERROR
         |--------------------------------------------------------------------------
         */

        function showError(
            select
        ) {

            select.innerHTML = '';

            const option =
                document.createElement(
                    'option'
                );

            option.value = '';

            option.textContent =
                'Không tải được dữ liệu';

            select.appendChild(
                option
            );

            select.disabled = true;
        }


        /*
         |--------------------------------------------------------------------------
         | CLEAR GHN HIDDEN VALUES
         |--------------------------------------------------------------------------
         */

        function clearGhnDistrict() {

            if (ghnDistrictIdInput) {
                ghnDistrictIdInput.value = '';
            }
        }


        function clearGhnWard() {

            if (ghnWardCodeInput) {
                ghnWardCodeInput.value = '';
            }
        }


        /*
         |--------------------------------------------------------------------------
         | SELECT OPTION BY NAME
         |--------------------------------------------------------------------------
         */

        function selectByName(
            select,
            name
        ) {

            if (!name) {
                return null;
            }

            const options =
                Array.from(
                    select.options
                );

            const matched =
                options.find(
                    option =>
                        option.value === name
                );

            if (!matched) {
                return null;
            }

            matched.selected = true;

            return matched;
        }


        /*
         |--------------------------------------------------------------------------
         | CREATE PROVINCE OPTION
         |--------------------------------------------------------------------------
         |
         | GHN:
         *
         * ProvinceID
         * ProvinceName
         *
         */

        function createProvinceOption(
            province
        ) {

            const option =
                document.createElement(
                    'option'
                );

            option.value =
                province.ProvinceName;

            option.textContent =
                province.ProvinceName;

            /*
             * Mã Tỉnh GHN.
             */
            option.dataset.provinceId =
                province.ProvinceID;

            return option;
        }


        /*
         |--------------------------------------------------------------------------
         | CREATE DISTRICT OPTION
         |--------------------------------------------------------------------------
         |
         | GHN:
         *
         * DistrictID
         * DistrictName
         *
         */

        function createDistrictOption(
            district
        ) {

            const option =
                document.createElement(
                    'option'
                );

            option.value =
                district.DistrictName;

            option.textContent =
                district.DistrictName;

            /*
             * Mã Quận/Huyện GHN.
             */
            option.dataset.districtId =
                district.DistrictID;

            return option;
        }


        /*
         |--------------------------------------------------------------------------
         | CREATE WARD OPTION
         |--------------------------------------------------------------------------
         |
         | GHN:
         *
         * WardCode
         * WardName
         *
         */

        function createWardOption(
            ward
        ) {

            const option =
                document.createElement(
                    'option'
                );

            option.value =
                ward.WardName;

            option.textContent =
                ward.WardName;

            /*
             * Mã Phường/Xã GHN.
             */
            option.dataset.wardCode =
                ward.WardCode;

            return option;
        }


        /*
         |--------------------------------------------------------------------------
         | LOAD PROVINCES
         |--------------------------------------------------------------------------
         */

        async function loadProvinces() {

            showLoading(
                provinceSelect,
                'Đang tải Tỉnh / Thành phố...'
            );

            try {

                const response =
                    await fetch(
                        API_BASE + '/provinces',
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );

                if (!response.ok) {

                    throw new Error(
                        'Province API error'
                    );
                }

                const result =
                    await response.json();

                if (
                    !result.success ||
                    !Array.isArray(
                        result.data
                    )
                ) {

                    throw new Error(
                        'Dữ liệu Tỉnh GHN không hợp lệ'
                    );
                }


                provinceSelect.innerHTML = '';

                const defaultOption =
                    document.createElement(
                        'option'
                    );

                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Tỉnh / Thành phố --';

                provinceSelect.appendChild(
                    defaultOption
                );


                result.data.forEach(
                    province => {

                        provinceSelect
                            .appendChild(
                                createProvinceOption(
                                    province
                                )
                            );
                    }
                );


                provinceSelect.disabled =
                    false;


                /*
                 * Restore old Province
                 * nếu Laravel validation quay lại.
                 */
                const oldOption =
                    selectByName(
                        provinceSelect,
                        oldProvince
                    );


                if (oldOption) {

                    const provinceId =
                        oldOption
                            .dataset
                            .provinceId;

                    await loadDistricts(
                        provinceId,
                        oldDistrict,
                        oldWard
                    );
                }


            } catch (error) {

                console.error(
                    'Không tải được tỉnh GHN:',
                    error
                );

                showError(
                    provinceSelect
                );
            }
        }


        /*
         |--------------------------------------------------------------------------
         | LOAD DISTRICTS
         |--------------------------------------------------------------------------
         */

        async function loadDistricts(
            provinceId,
            restoreDistrict = '',
            restoreWard = ''
        ) {

            resetSelect(
                wardSelect,
                '-- Chọn Phường / Xã --'
            );

            clearGhnDistrict();

            clearGhnWard();


            if (!provinceId) {

                resetSelect(
                    districtSelect,
                    '-- Chọn Quận / Huyện --'
                );

                return;
            }


            showLoading(
                districtSelect,
                'Đang tải Quận / Huyện...'
            );


            try {

                const response =
                    await fetch(
                        API_BASE
                        + '/districts/'
                        + encodeURIComponent(
                            provinceId
                        ),
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'District API error'
                    );
                }


                const result =
                    await response.json();


                if (
                    !result.success ||
                    !Array.isArray(
                        result.data
                    )
                ) {

                    throw new Error(
                        'Dữ liệu Quận GHN không hợp lệ'
                    );
                }


                districtSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );

                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Quận / Huyện --';

                districtSelect.appendChild(
                    defaultOption
                );


                result.data.forEach(
                    district => {

                        districtSelect
                            .appendChild(
                                createDistrictOption(
                                    district
                                )
                            );
                    }
                );


                districtSelect.disabled =
                    false;


                /*
                 * Restore old District.
                 */
                if (restoreDistrict) {

                    const oldOption =
                        selectByName(
                            districtSelect,
                            restoreDistrict
                        );


                    if (oldOption) {

                        const districtId =
                            oldOption
                                .dataset
                                .districtId;

                        /*
                         * Gán ngay DistrictID
                         * vào hidden input.
                         */
                        if (
                            ghnDistrictIdInput
                        ) {

                            ghnDistrictIdInput.value =
                                districtId;
                        }


                        await loadWards(
                            districtId,
                            restoreWard
                        );
                    }
                }


            } catch (error) {

                console.error(
                    'Không tải được quận/huyện GHN:',
                    error
                );

                showError(
                    districtSelect
                );
            }
        }


        /*
         |--------------------------------------------------------------------------
         | LOAD WARDS
         |--------------------------------------------------------------------------
         */

        async function loadWards(
            districtId,
            restoreWard = ''
        ) {

            clearGhnWard();


            if (!districtId) {

                resetSelect(
                    wardSelect,
                    '-- Chọn Phường / Xã --'
                );

                return;
            }


            showLoading(
                wardSelect,
                'Đang tải Phường / Xã...'
            );


            try {

                const response =
                    await fetch(
                        API_BASE
                        + '/wards/'
                        + encodeURIComponent(
                            districtId
                        ),
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Ward API error'
                    );
                }


                const result =
                    await response.json();


                if (
                    !result.success ||
                    !Array.isArray(
                        result.data
                    )
                ) {

                    throw new Error(
                        'Dữ liệu Phường GHN không hợp lệ'
                    );
                }


                wardSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );

                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Phường / Xã --';

                wardSelect.appendChild(
                    defaultOption
                );


                result.data.forEach(
                    ward => {

                        wardSelect
                            .appendChild(
                                createWardOption(
                                    ward
                                )
                            );
                    }
                );


                wardSelect.disabled =
                    false;


                /*
                 * Restore old Ward.
                 */
                if (restoreWard) {

                    const oldOption =
                        selectByName(
                            wardSelect,
                            restoreWard
                        );


                    if (oldOption) {

                        if (
                            ghnWardCodeInput
                        ) {

                            ghnWardCodeInput.value =
                                oldOption
                                    .dataset
                                    .wardCode;
                        }
                    }
                }


            } catch (error) {

                console.error(
                    'Không tải được phường/xã GHN:',
                    error
                );

                showError(
                    wardSelect
                );
            }
        }


        /*
         |--------------------------------------------------------------------------
         | CHANGE PROVINCE
         |--------------------------------------------------------------------------
         */

        provinceSelect.addEventListener(
            'change',
            function () {

                const selectedOption =
                    provinceSelect
                        .options[
                            provinceSelect
                                .selectedIndex
                        ];


                const provinceId =
                    selectedOption
                        ?.dataset
                        ?.provinceId;


                /*
                 * Khi đổi Tỉnh:
                 * - Xóa DistrictID
                 * - Xóa WardCode
                 */
                clearGhnDistrict();

                clearGhnWard();


                loadDistricts(
                    provinceId
                );
            }
        );


        /*
         |--------------------------------------------------------------------------
         | CHANGE DISTRICT
         |--------------------------------------------------------------------------
         */

        districtSelect.addEventListener(
            'change',
            function () {

                const selectedOption =
                    districtSelect
                        .options[
                            districtSelect
                                .selectedIndex
                        ];


                const districtId =
                    selectedOption
                        ?.dataset
                        ?.districtId;


                /*
                 * Khi đổi Quận:
                 * - Lưu DistrictID
                 * - Xóa WardCode
                 */
                if (
                    ghnDistrictIdInput
                ) {

                    ghnDistrictIdInput.value =
                        districtId || '';
                }


                clearGhnWard();


                loadWards(
                    districtId
                );
            }
        );


        /*
         |--------------------------------------------------------------------------
         | CHANGE WARD
         |--------------------------------------------------------------------------
         */

        wardSelect.addEventListener(
            'change',
            function () {

                const selectedOption =
                    wardSelect
                        .options[
                            wardSelect
                                .selectedIndex
                        ];


                const wardCode =
                    selectedOption
                        ?.dataset
                        ?.wardCode;


                /*
                 * Lưu WardCode của GHN.
                 */
                if (
                    ghnWardCodeInput
                ) {

                    ghnWardCodeInput.value =
                        wardCode || '';
                }
            }
        );


              /*
         |--------------------------------------------------------------------------
         | EDIT ADDRESS
         |--------------------------------------------------------------------------
         */

        const editButtons =
            document.querySelectorAll(
                '.checkout-address-edit-button'
            );

        const cancelEditButtons =
            document.querySelectorAll(
                '.checkout-address-cancel-edit'
            );


        /*
         * Tìm form sửa theo Address ID.
         */
        function getEditForm(addressId) {

            return document.getElementById(
                'editAddress' + addressId
            );
        }


        /*
         * Tạo option Tỉnh cho form sửa.
         */
        function createEditProvinceOption(province) {

            const option =
                document.createElement('option');

            option.value =
                province.ProvinceName;

            option.textContent =
                province.ProvinceName;

            option.dataset.provinceId =
                province.ProvinceID;

            return option;
        }


        /*
         * Tạo option Quận cho form sửa.
         */
        function createEditDistrictOption(district) {

            const option =
                document.createElement('option');

            option.value =
                district.DistrictName;

            option.textContent =
                district.DistrictName;

            option.dataset.districtId =
                district.DistrictID;

            return option;
        }


        /*
         * Tạo option Phường cho form sửa.
         */
        function createEditWardOption(ward) {

            const option =
                document.createElement('option');

            option.value =
                ward.WardName;

            option.textContent =
                ward.WardName;

            option.dataset.wardCode =
                ward.WardCode;

            return option;
        }


        /*
         * Chọn option theo value.
         */
        function selectEditOption(
            select,
            value
        ) {

            if (!value) {
                return null;
            }

            const option =
                Array.from(
                    select.options
                ).find(
                    option =>
                        option.value === value
                );

            if (!option) {
                return null;
            }

            option.selected = true;

            return option;
        }


        /*
         * Tải Tỉnh cho form sửa.
         */
        async function loadEditProvinces(
            form
        ) {

            const provinceSelect =
                form.querySelector(
                    '.checkout-edit-province'
                );

            const districtSelect =
                form.querySelector(
                    '.checkout-edit-district'
                );

            const wardSelect =
                form.querySelector(
                    '.checkout-edit-ward'
                );

            const currentProvince =
                provinceSelect.dataset.currentProvince
                || '';

            const currentDistrict =
                districtSelect.dataset.currentDistrict
                || '';

            const currentWard =
                wardSelect.dataset.currentWard
                || '';


            provinceSelect.innerHTML = '';

            const loadingOption =
                document.createElement(
                    'option'
                );

            loadingOption.value = '';

            loadingOption.textContent =
                'Đang tải Tỉnh / Thành phố...';

            provinceSelect.appendChild(
                loadingOption
            );

            provinceSelect.disabled = true;

            districtSelect.disabled = true;

            wardSelect.disabled = true;


            try {

                const response =
                    await fetch(
                        API_BASE + '/provinces',
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Edit Province API error'
                    );
                }


                const result =
                    await response.json();


                if (
                    !result.success
                    ||
                    !Array.isArray(
                        result.data
                    )
                ) {

                    throw new Error(
                        'Dữ liệu Tỉnh GHN không hợp lệ'
                    );
                }


                provinceSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );

                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Tỉnh / Thành phố --';

                provinceSelect.appendChild(
                    defaultOption
                );


                result.data.forEach(
                    province => {

                        provinceSelect.appendChild(
                            createEditProvinceOption(
                                province
                            )
                        );

                    }
                );


                provinceSelect.disabled =
                    false;


                /*
                 * Khôi phục Tỉnh hiện tại.
                 */
                const selectedProvince =
                    selectEditOption(
                        provinceSelect,
                        currentProvince
                    );


                if (selectedProvince) {

                    const provinceId =
                        selectedProvince
                            .dataset
                            .provinceId;


                    await loadEditDistricts(
                        form,
                        provinceId,
                        currentDistrict,
                        currentWard
                    );
                }

            } catch (error) {

                console.error(
                    'Không tải được Tỉnh GHN cho form sửa:',
                    error
                );

                provinceSelect.innerHTML = '';

                const errorOption =
                    document.createElement(
                        'option'
                    );

                errorOption.value = '';

                errorOption.textContent =
                    'Không tải được dữ liệu';

                provinceSelect.appendChild(
                    errorOption
                );

                provinceSelect.disabled = true;
            }
        }


        /*
         * Tải Quận cho form sửa.
         */
        async function loadEditDistricts(
            form,
            provinceId,
            restoreDistrict = '',
            restoreWard = ''
        ) {

            const districtSelect =
                form.querySelector(
                    '.checkout-edit-district'
                );

            const wardSelect =
                form.querySelector(
                    '.checkout-edit-ward'
                );

            const ghnDistrictInput =
                form.querySelector(
                    '.checkout-edit-ghn-district'
                );

            const ghnWardInput =
                form.querySelector(
                    '.checkout-edit-ghn-ward'
                );


            districtSelect.innerHTML = '';

            wardSelect.innerHTML = '';


            const loadingOption =
                document.createElement(
                    'option'
                );

            loadingOption.value = '';

            loadingOption.textContent =
                'Đang tải Quận / Huyện...';

            districtSelect.appendChild(
                loadingOption
            );

            districtSelect.disabled = true;

            wardSelect.disabled = true;


            if (ghnDistrictInput) {
                ghnDistrictInput.value = '';
            }

            if (ghnWardInput) {
                ghnWardInput.value = '';
            }


            if (!provinceId) {
                return;
            }


            try {

                const response =
                    await fetch(
                        API_BASE
                        + '/districts/'
                        + encodeURIComponent(
                            provinceId
                        ),
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Edit District API error'
                    );
                }


                const result =
                    await response.json();


                if (
                    !result.success
                    ||
                    !Array.isArray(
                        result.data
                    )
                ) {

                    throw new Error(
                        'Dữ liệu Quận GHN không hợp lệ'
                    );
                }


                districtSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );

                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Quận / Huyện --';

                districtSelect.appendChild(
                    defaultOption
                );


                result.data.forEach(
                    district => {

                        districtSelect.appendChild(
                            createEditDistrictOption(
                                district
                            )
                        );

                    }
                );


                districtSelect.disabled =
                    false;


                /*
                 * Khôi phục Quận hiện tại.
                 */
                const selectedDistrict =
                    selectEditOption(
                        districtSelect,
                        restoreDistrict
                    );


                if (selectedDistrict) {

                    const districtId =
                        selectedDistrict
                            .dataset
                            .districtId;


                    if (ghnDistrictInput) {

                        ghnDistrictInput.value =
                            districtId;
                    }


                    await loadEditWards(
                        form,
                        districtId,
                        restoreWard
                    );
                }

            } catch (error) {

                console.error(
                    'Không tải được Quận GHN cho form sửa:',
                    error
                );

                districtSelect.innerHTML = '';

                const errorOption =
                    document.createElement(
                        'option'
                    );

                errorOption.value = '';

                errorOption.textContent =
                    'Không tải được dữ liệu';

                districtSelect.appendChild(
                    errorOption
                );

                districtSelect.disabled = true;
            }
        }


        /*
         * Tải Phường cho form sửa.
         */
        async function loadEditWards(
            form,
            districtId,
            restoreWard = ''
        ) {

            const wardSelect =
                form.querySelector(
                    '.checkout-edit-ward'
                );

            const ghnWardInput =
                form.querySelector(
                    '.checkout-edit-ghn-ward'
                );


            wardSelect.innerHTML = '';


            const loadingOption =
                document.createElement(
                    'option'
                );

            loadingOption.value = '';

            loadingOption.textContent =
                'Đang tải Phường / Xã...';

            wardSelect.appendChild(
                loadingOption
            );

            wardSelect.disabled = true;


            if (ghnWardInput) {
                ghnWardInput.value = '';
            }


            if (!districtId) {
                return;
            }


            try {

                const response =
                    await fetch(
                        API_BASE
                        + '/wards/'
                        + encodeURIComponent(
                            districtId
                        ),
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Edit Ward API error'
                    );
                }


                const result =
                    await response.json();


                if (
                    !result.success
                    ||
                    !Array.isArray(
                        result.data
                    )
                ) {

                    throw new Error(
                        'Dữ liệu Phường GHN không hợp lệ'
                    );
                }


                wardSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );

                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Phường / Xã --';

                wardSelect.appendChild(
                    defaultOption
                );


                result.data.forEach(
                    ward => {

                        wardSelect.appendChild(
                            createEditWardOption(
                                ward
                            )
                        );

                    }
                );


                wardSelect.disabled =
                    false;


                /*
                 * Khôi phục Phường hiện tại.
                 */
                const selectedWard =
                    selectEditOption(
                        wardSelect,
                        restoreWard
                    );


                if (
                    selectedWard
                    &&
                    ghnWardInput
                ) {

                    ghnWardInput.value =
                        selectedWard
                            .dataset
                            .wardCode;
                }

            } catch (error) {

                console.error(
                    'Không tải được Phường GHN cho form sửa:',
                    error
                );

                wardSelect.innerHTML = '';

                const errorOption =
                    document.createElement(
                        'option'
                    );

                errorOption.value = '';

                errorOption.textContent =
                    'Không tải được dữ liệu';

                wardSelect.appendChild(
                    errorOption
                );

                wardSelect.disabled = true;
            }
        }


        /*
         * Khi bấm nút Sửa.
         */
        editButtons.forEach(
            button => {

                button.addEventListener(
                    'click',
                    async function () {

                        const addressId =
                            this.dataset.addressEdit;

                        const form =
                            getEditForm(
                                addressId
                            );


                        if (!form) {

                            console.error(
                                'Không tìm thấy form sửa Address ID:',
                                addressId
                            );

                            return;
                        }


                        /*
                         * Đóng các form sửa khác.
                         */
                        document
                            .querySelectorAll(
                                '.checkout-address-edit-form'
                            )
                            .forEach(
                                otherForm => {

                                    if (
                                        otherForm !==
                                        form
                                    ) {

                                        otherForm.style.display =
                                            'none';
                                    }

                                }
                            );


                        /*
                         * Hiện form sửa.
                         */
                        form.style.display =
                            'block';


                        /*
                         * Tải dữ liệu GHN
                         * và khôi phục địa chỉ hiện tại.
                         */
                        await loadEditProvinces(
                            form
                        );

                    }
                );

            }
        );


        /*
         * Nút Hủy sửa.
         */
        cancelEditButtons.forEach(
            button => {

                button.addEventListener(
                    'click',
                    function () {

                        const addressId =
                            this.dataset.addressId;

                        const form =
                            getEditForm(
                                addressId
                            );


                        if (form) {

                            form.style.display =
                                'none';
                        }

                    }
                );

            }
        );


        /*
         * Thay đổi Tỉnh trong form sửa.
         */
        document
            .querySelectorAll(
                '.checkout-edit-province'
            )
            .forEach(
                provinceSelect => {

                    provinceSelect.addEventListener(
                        'change',
                        function () {

                            const form =
                                this.closest(
                                    '.checkout-address-edit-form'
                                );


                            const selectedOption =
                                this.options[
                                    this.selectedIndex
                                ];


                            const provinceId =
                                selectedOption
                                    ?.dataset
                                    ?.provinceId;


                            loadEditDistricts(
                                form,
                                provinceId
                            );

                        }
                    );

                }
            );


        /*
         * Thay đổi Quận trong form sửa.
         */
        document
            .querySelectorAll(
                '.checkout-edit-district'
            )
            .forEach(
                districtSelect => {

                    districtSelect.addEventListener(
                        'change',
                        function () {

                            const form =
                                this.closest(
                                    '.checkout-address-edit-form'
                                );


                            const selectedOption =
                                this.options[
                                    this.selectedIndex
                                ];


                            const districtId =
                                selectedOption
                                    ?.dataset
                                    ?.districtId;


                            const ghnDistrictInput =
                                form.querySelector(
                                    '.checkout-edit-ghn-district'
                                );


                            if (
                                ghnDistrictInput
                            ) {

                                ghnDistrictInput.value =
                                    districtId || '';
                            }


                            loadEditWards(
                                form,
                                districtId
                            );

                        }
                    );

                }
            );


        /*
         * Thay đổi Phường trong form sửa.
         */
        document
            .querySelectorAll(
                '.checkout-edit-ward'
            )
            .forEach(
                wardSelect => {

                    wardSelect.addEventListener(
                        'change',
                        function () {

                            const form =
                                this.closest(
                                    '.checkout-address-edit-form'
                                );


                            const selectedOption =
                                this.options[
                                    this.selectedIndex
                                ];


                            const wardCode =
                                selectedOption
                                    ?.dataset
                                    ?.wardCode;


                            const ghnWardInput =
                                form.querySelector(
                                    '.checkout-edit-ghn-ward'
                                );


                            if (
                                ghnWardInput
                            ) {

                                ghnWardInput.value =
                                    wardCode || '';
                            }

                        }
                    );

                }
            );


        /*
         |--------------------------------------------------------------------------
         | INITIAL
         |--------------------------------------------------------------------------
         */

        loadProvinces();

    }
);