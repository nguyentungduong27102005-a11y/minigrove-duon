@extends('layouts.admin')

@section('title', 'Hội thoại #' . $conversation->id . ' - MiniGrove Admin')
@section('page-title', 'Chat hỗ trợ')

@push('styles')
<link
    rel="stylesheet"
    href="{{
        asset('css/admin-chat.css')
        . '?v='
        . filemtime(public_path('css/admin-chat.css'))
    }}"
>
@endpush

@section('content')
<div
    class="admin-chat-detail"
    data-admin-chat-show
    data-conversation-id="{{ $conversation->id }}"
    data-admin-id="{{ auth()->id() }}"
    data-customer-id="{{ $conversation->customer_id }}"
    data-read-url="{{
        route(
            'admin.chat.read',
            $conversation->id
        )
    }}"
>
    <div class="admin-chat-heading">
        <div>
            <a
                href="{{ route('admin.chat.index') }}"
                class="admin-chat-back"
            >
                ← Quay lại danh sách Chat
            </a>

            <h1 style="margin-top:12px">
                Customer #{{ $conversation->customer_id }}
            </h1>

            <p>
                {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
            </p>
        </div>

        <span class="admin-chat-live">
            Realtime
        </span>
    </div>

    <div class="card admin-chat-conversation">
        <div class="admin-chat-conversation-head">
            <div class="admin-chat-conversation-meta">
                <span>Trạng thái:</span>

                <span
                    class="
                        admin-chat-status
                        admin-chat-status-{{ $conversation->status }}
                    "
                    data-chat-status
                >
                    {{
                        $conversation->status === 'waiting'
                            ? 'ĐANG CHỜ'
                            : (
                                $conversation->status === 'open'
                                    ? 'ĐANG MỞ'
                                    : 'ĐÃ ĐÓNG'
                            )
                    }}
                </span>

                <strong>
                    Hội thoại #{{ $conversation->id }}
                </strong>
            </div>

            <div data-chat-assignment-actions>
                @if ($conversation->assigned_admin_id === null)
                    <form
                        method="POST"
                        action="{{ route('admin.chat.accept', $conversation->id) }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="admin-chat-button"
                        >
                            Nhận hội thoại
                        </button>
                    </form>
                @else
                    <span class="admin-chat-assignment mine">
                        Bạn đang xử lý
                    </span>
                @endif
            </div>
        </div>

        <div
            class="admin-chat-messages"
            data-chat-messages
        >
            @forelse ($messages as $message)
                @php
                    $isCustomer =
                        (int) $message->sender_id
                        === (int) $conversation->customer_id;
                @endphp

                <div
                    class="
                        admin-chat-message-row
                        {{ $isCustomer ? 'customer' : 'admin' }}
                    "
                    data-message-id="{{ $message->id }}"
                >
                    <div class="admin-chat-message">
                        <div class="admin-chat-message-author">
                            {{
                                $isCustomer
                                    ? 'Customer #' . $conversation->customer_id
                                    : 'Bạn'
                            }}
                        </div>

                        <div class="admin-chat-bubble">
                            @php
                                $displayContent =
                                    $message->content;

                                if (
                                    $message->productCards->isNotEmpty()
                                    && $displayContent
                                    && str_contains(
                                        $displayContent,
                                        'Sản phẩm MiniGrove gợi ý:'
                                    )
                                ) {
                                    $displayContent = trim(
                                        explode(
                                            'Sản phẩm MiniGrove gợi ý:',
                                            $displayContent,
                                            2
                                        )[0]
                                    );
                                }
                            @endphp

                            @if ($displayContent)
                                @php
                                    $escapedContent = e(
                                        $displayContent
                                    );
                                    $linkedContent =
                                        preg_replace(
                                            '~(https?://[^\s<]+)~',
                                            '<a href="$1" target="_blank" rel="noopener">$1</a>',
                                            $escapedContent
                                        );
                                @endphp

                                <div class="admin-chat-message-content">
                                    {!! $linkedContent !!}
                                </div>
                            @endif

                            @if ($message->productCards->isNotEmpty())
                                <div class="admin-chat-products">
                                    @foreach ($message->productCards as $productCard)
                                        @php
                                            $hasSale =
                                                $productCard->sale_price !== null
                                                && (float) $productCard->sale_price
                                                    < (float) $productCard->list_price;
                                            $price =
                                                $hasSale
                                                    ? $productCard->sale_price
                                                    : $productCard->list_price;
                                            $productImageUrl =
                                                $productCard->image_path
                                                    ? asset(
                                                        'storage/'
                                                        . $productCard->image_path
                                                    )
                                                    : null;
                                            $productUrl =
                                                $productCard->product_id
                                                    ? route(
                                                        'products.show',
                                                        $productCard->product_id
                                                    )
                                                    : null;
                                        @endphp

                                        @if ($productUrl)
                                            <a
                                                href="{{ $productUrl }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="admin-chat-product-card"
                                            >
                                        @else
                                            <div class="admin-chat-product-card">
                                        @endif

                                            <span class="admin-chat-product-card-image">
                                                @if ($productImageUrl)
                                                    <img
                                                        src="{{ $productImageUrl }}"
                                                        alt="{{ $productCard->product_name }}"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    🌱
                                                @endif
                                            </span>

                                            <span class="admin-chat-product-card-info">
                                                <strong>
                                                    {{ $productCard->product_name }}
                                                </strong>

                                                <small>
                                                    {{ $productCard->product_sku }}
                                                </small>

                                                <span class="admin-chat-product-card-price">
                                                    <b>
                                                        {{
                                                            number_format(
                                                                (float) $price,
                                                                0,
                                                                ',',
                                                                '.'
                                                            )
                                                        }}đ
                                                    </b>

                                                    @if ($hasSale)
                                                        <del>
                                                            {{
                                                                number_format(
                                                                    (float) $productCard->list_price,
                                                                    0,
                                                                    ',',
                                                                    '.'
                                                                )
                                                            }}đ
                                                        </del>
                                                    @endif
                                                </span>
                                            </span>

                                            <span class="admin-chat-product-card-view">
                                                Xem →
                                            </span>

                                        @if ($productUrl)
                                            </a>
                                        @else
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if ($message->attachments->isNotEmpty())
                                <div class="admin-chat-attachments">
                                    @foreach ($message->attachments as $attachment)
                                        @php
                                            $attachmentUrl =
                                                asset(
                                                    'storage/'
                                                    . $attachment->file_path
                                                );
                                        @endphp

                                        <a
                                            href="{{ $attachmentUrl }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="admin-chat-attachment"
                                        >
                                            <img
                                                src="{{ $attachmentUrl }}"
                                                alt="{{ $attachment->original_name }}"
                                                loading="lazy"
                                            >
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="admin-chat-message-time">
                            {{
                                $message->sent_at
                                    ? $message->sent_at->format('H:i d/m/Y')
                                    : ''
                            }}
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="admin-chat-message-empty"
                    data-chat-message-empty
                >
                    Chưa có tin nhắn.
                </div>
            @endforelse
        </div>

        @if ($conversation->status !== 'closed')
            <div class="admin-chat-compose">
                @if (
                    (int) $conversation->assigned_admin_id
                    === (int) auth()->id()
                )
                    <form
                        method="POST"
                        action="{{ route('admin.chat.reply', $conversation->id) }}"
                        data-admin-chat-form
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <textarea
                            name="content"
                            maxlength="5000"
                            placeholder="Nhập phản hồi cho khách hàng..."
                        ></textarea>

                        <div
                            class="admin-chat-image-preview"
                            data-chat-image-preview
                            hidden
                        ></div>

                        <div
                            class="admin-chat-product-picker"
                            data-product-picker
                            hidden
                        >
                            <div class="admin-chat-product-picker-head">
                                <div>
                                    <strong>Chọn sản phẩm gửi cho khách</strong>
                                    <small>
                                        Tối đa 3 sản phẩm mỗi lần gửi
                                    </small>
                                </div>

                                <span data-product-count>0/3</span>
                            </div>

                            <div class="admin-chat-product-list">
                                @forelse ($products as $product)
                                    @php
                                        $productImage =
                                            $product->images->first();
                                        $productImageUrl =
                                            $productImage
                                                ? asset(
                                                    'storage/'
                                                    . $productImage->image_path
                                                )
                                                : null;
                                        $productPrice =
                                            $product->sale_price
                                            ?? $product->list_price;
                                    @endphp

                                    <label class="admin-chat-product-option">
                                        <input
                                            type="checkbox"
                                            name="product_ids[]"
                                            value="{{ $product->id }}"
                                            data-product-checkbox
                                        >

                                        <span class="admin-chat-product-thumb">
                                            @if ($productImageUrl)
                                                <img
                                                    src="{{ $productImageUrl }}"
                                                    alt="{{ $product->name }}"
                                                    loading="lazy"
                                                >
                                            @else
                                                🌱
                                            @endif
                                        </span>

                                        <span class="admin-chat-product-copy">
                                            <strong>{{ $product->name }}</strong>
                                            <small>
                                                {{ $product->sku }}
                                                ·
                                                {{ number_format((float) $productPrice, 0, ',', '.') }}đ
                                            </small>
                                        </span>
                                    </label>
                                @empty
                                    <div class="admin-chat-product-empty">
                                        Chưa có sản phẩm đang hoạt động.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div
                            class="admin-chat-error"
                            data-chat-form-error
                        ></div>

                        <div class="admin-chat-compose-actions">
                            <div class="admin-chat-compose-tools">
                                <label class="admin-chat-tool-button">
                                    <span>📷</span>
                                    <span>Gửi ảnh</span>

                                    <input
                                        type="file"
                                        name="images[]"
                                        accept="image/jpeg,image/png,image/webp,image/gif"
                                        multiple
                                        data-chat-images
                                        hidden
                                    >
                                </label>

                                <button
                                    type="button"
                                    class="admin-chat-tool-button"
                                    data-product-picker-toggle
                                >
                                    <span>🪴</span>
                                    <span>Gửi sản phẩm</span>
                                </button>

                                <span class="admin-chat-file-hint">
                                    Ảnh: tối đa 5 · 5MB/ảnh
                                </span>
                            </div>

                            <div class="admin-chat-compose-buttons">
                                <button
                                    type="submit"
                                    form="close-conversation-form"
                                    class="
                                        admin-chat-button
                                        admin-chat-button-secondary
                                    "
                                >
                                    Đóng hội thoại
                                </button>

                                <button
                                    type="submit"
                                    class="admin-chat-button"
                                >
                                    Gửi phản hồi
                                    <span aria-hidden="true">➜</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <form
                        id="close-conversation-form"
                        method="POST"
                        action="{{ route('admin.chat.close', $conversation->id) }}"
                    >
                        @csrf
                    </form>
                @else
                    <div class="admin-chat-note">
                        Bạn cần nhận hội thoại trước khi phản hồi.
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.querySelector('[data-admin-chat-show]');

    if (!root) {
        return;
    }

    const conversationId =
        Number(root.dataset.conversationId);
    const customerId = Number(root.dataset.customerId);
    const readUrl = root.dataset.readUrl;
    const messages = root.querySelector('[data-chat-messages]');
    const status = root.querySelector('[data-chat-status]');
    const form = root.querySelector('[data-admin-chat-form]');
    const errorBox = root.querySelector('[data-chat-form-error]');
    const imageInput = root.querySelector('[data-chat-images]');
    const imagePreview = root.querySelector(
        '[data-chat-image-preview]'
    );
    const productPicker = root.querySelector(
        '[data-product-picker]'
    );
    const productToggle = root.querySelector(
        '[data-product-picker-toggle]'
    );
    const productCount = root.querySelector(
        '[data-product-count]'
    );
    const productCheckboxes = Array.from(
        root.querySelectorAll('[data-product-checkbox]')
    );

    const scrollToLatest = function () {
        if (!messages) return;
        messages.scrollTop = messages.scrollHeight;
    };

    const showError = function (message) {
        if (!errorBox) return;

        errorBox.textContent = message;
        errorBox.classList.add('visible');
    };

    const clearError = function () {
        if (!errorBox) return;

        errorBox.textContent = '';
        errorBox.classList.remove('visible');
    };

    const renderSelectedImages = function () {
        if (!imageInput || !imagePreview) return;

        imagePreview.innerHTML = '';

        const files = Array.from(imageInput.files || []);

        if (files.length === 0) {
            imagePreview.hidden = true;
            return;
        }

        imagePreview.hidden = false;

        files.forEach(function (file) {
            const item = document.createElement('div');
            item.className = 'admin-chat-preview-item';

            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = file.name;

            item.append(image);
            imagePreview.append(item);
        });
    };

    imageInput?.addEventListener('change', function () {
        clearError();

        const files = Array.from(imageInput.files || []);

        if (files.length > 5) {
            imageInput.value = '';
            renderSelectedImages();
            showError(
                'Mỗi tin nhắn chỉ được chọn tối đa 5 ảnh.'
            );
            return;
        }

        const tooLarge = files.find(
            (file) => file.size > 5 * 1024 * 1024
        );

        if (tooLarge) {
            imageInput.value = '';
            renderSelectedImages();
            showError(
                'Ảnh "' + tooLarge.name + '" vượt quá 5MB.'
            );
            return;
        }

        renderSelectedImages();
    });

    productToggle?.addEventListener('click', function () {
        if (!productPicker) return;

        productPicker.hidden = !productPicker.hidden;
        productToggle.classList.toggle(
            'active',
            !productPicker.hidden
        );
    });

    const updateProductSelection = function () {
        const selected = productCheckboxes.filter(
            (checkbox) => checkbox.checked
        );

        if (productCount) {
            productCount.textContent =
                selected.length + '/3';
        }

        productCheckboxes.forEach(function (checkbox) {
            const option = checkbox.closest(
                '.admin-chat-product-option'
            );

            option?.classList.toggle(
                'selected',
                checkbox.checked
            );
        });
    };

    productCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            clearError();

            const selectedCount = productCheckboxes.filter(
                (item) => item.checked
            ).length;

            if (selectedCount > 3) {
                checkbox.checked = false;
                showError(
                    'Mỗi lần chỉ được gửi tối đa 3 sản phẩm.'
                );
            }

            updateProductSelection();
        });
    });

    updateProductSelection();

    const statusLabel = function (value) {
        if (value === 'waiting') return 'ĐANG CHỜ';
        if (value === 'open') return 'ĐANG MỞ';
        if (value === 'closed') return 'ĐÃ ĐÓNG';
        return String(value || '').toUpperCase();
    };

    const updateStatus = function (value) {
        if (!status || !value) return;

        status.textContent = statusLabel(value);
        status.className =
            'admin-chat-status admin-chat-status-' + value;
    };

    const appendLinkedText = function (container, value) {
        const parts = String(value || '').split(
            /(https?:\/\/[^\s]+)/g
        );

        parts.forEach(function (part) {
            if (/^https?:\/\//.test(part)) {
                const link = document.createElement('a');
                link.href = part;
                link.target = '_blank';
                link.rel = 'noopener';
                link.textContent = part;
                container.append(link);
                return;
            }

            container.append(
                document.createTextNode(part)
            );
        });
    };

    const appendMessage = function (message) {
        if (!message?.id || !messages) return;

        if (
            messages.querySelector(
                '[data-message-id="' + message.id + '"]'
            )
        ) {
            return;
        }

        messages
            .querySelector('[data-chat-message-empty]')
            ?.remove();

        const isCustomer =
            Number(message.sender_id) === customerId;

        const row = document.createElement('div');
        row.className =
            'admin-chat-message-row '
            + (isCustomer ? 'customer' : 'admin');
        row.dataset.messageId = message.id;

        const box = document.createElement('div');
        box.className = 'admin-chat-message';

        const author = document.createElement('div');
        author.className = 'admin-chat-message-author';
        author.textContent =
            isCustomer
                ? 'Customer #' + customerId
                : 'Bạn';

        const bubble = document.createElement('div');
        bubble.className = 'admin-chat-bubble';

        if (message.content) {
            const content = document.createElement('div');
            content.className = 'admin-chat-message-content';
            appendLinkedText(content, message.content);
            bubble.append(content);
        }

        if ((message.attachments || []).length > 0) {
            const attachments = document.createElement('div');
            attachments.className = 'admin-chat-attachments';

            message.attachments.forEach(function (attachment) {
                const link = document.createElement('a');
                link.href = attachment.url;
                link.target = '_blank';
                link.rel = 'noopener';
                link.className = 'admin-chat-attachment';

                const image = document.createElement('img');
                image.src = attachment.url;
                image.alt =
                    attachment.name || 'Ảnh đính kèm';
                image.loading = 'lazy';

                link.append(image);
                attachments.append(link);
            });

            bubble.append(attachments);
        }

        if ((message.products || []).length > 0) {
            const products = document.createElement('div');
            products.className = 'admin-chat-products';

            message.products.forEach(function (product) {
                const card = document.createElement(
                    product.url ? 'a' : 'div'
                );
                card.className = 'admin-chat-product-card';

                if (product.url) {
                    card.href = product.url;
                    card.target = '_blank';
                    card.rel = 'noopener';
                }

                const imageBox = document.createElement('span');
                imageBox.className =
                    'admin-chat-product-card-image';

                if (product.image_url) {
                    const image = document.createElement('img');
                    image.src = product.image_url;
                    image.alt =
                        product.name || 'Sản phẩm MiniGrove';
                    image.loading = 'lazy';
                    imageBox.append(image);
                } else {
                    imageBox.textContent = '🌱';
                }

                const info = document.createElement('span');
                info.className =
                    'admin-chat-product-card-info';

                const name = document.createElement('strong');
                name.textContent = product.name || 'Sản phẩm';

                const sku = document.createElement('small');
                sku.textContent = product.sku || '';

                const price = document.createElement('span');
                price.className =
                    'admin-chat-product-card-price';

                const currentPrice =
                    document.createElement('b');
                currentPrice.textContent =
                    product.price_label || '';
                price.append(currentPrice);

                if (product.has_sale) {
                    const oldPrice =
                        document.createElement('del');
                    oldPrice.textContent =
                        product.list_price_label || '';
                    price.append(oldPrice);
                }

                info.append(name, sku, price);

                const view = document.createElement('span');
                view.className =
                    'admin-chat-product-card-view';
                view.textContent = 'Xem →';

                card.append(imageBox, info, view);
                products.append(card);
            });

            bubble.append(products);
        }

        const time = document.createElement('div');
        time.className = 'admin-chat-message-time';
        time.textContent = message.sent_at_label || '';

        box.append(author, bubble, time);
        row.append(box);
        messages.append(row);

        scrollToLatest();
    };

    const markCustomerMessagesRead =
        async function () {
            if (!readUrl || !window.axios) {
                return;
            }

            try {
                await window.axios.post(
                    readUrl,
                    {},
                    {
                        headers: {
                            Accept: 'application/json',
                        },
                    }
                );
            } catch (error) {
                // Badge sẽ tự đồng bộ ở lần cập nhật kế tiếp.
            }
        };

    if (window.Echo) {
        window.Echo
            .private('chat.conversation.' + conversationId)
            .listen('.chat.message.sent', function (event) {
                appendMessage(event.message);
                updateStatus(event.conversation?.status);

                if (
                    Number(event.message?.sender_id)
                    === customerId
                ) {
                    markCustomerMessagesRead();
                }
            })
            .listen(
                '.chat.conversation.updated',
                function (event) {
                    const conversation = event.conversation;
                    updateStatus(conversation?.status);

                    if (
                        conversation?.status === 'closed'
                    ) {
                        form?.querySelector(
                            'textarea'
                        )?.setAttribute('disabled', 'disabled');

                        form?.querySelector(
                            'button[type="submit"]'
                        )?.setAttribute('disabled', 'disabled');
                    }
                }
            );
    }

    form?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const textarea = form.querySelector(
            'textarea[name="content"]'
        );
        const content = textarea?.value.trim();
        const imageCount =
            imageInput?.files?.length || 0;
        const productSelectedCount =
            productCheckboxes.filter(
                (checkbox) => checkbox.checked
            ).length;

        if (
            !content
            && imageCount === 0
            && productSelectedCount === 0
        ) {
            showError(
                'Vui lòng nhập phản hồi, chọn ảnh hoặc chọn sản phẩm.'
            );
            return;
        }

        const button = form.querySelector(
            'button[type="submit"]:not([form])'
        );

        button?.setAttribute('disabled', 'disabled');
        clearError();

        try {
            const response = await window.axios.post(
                form.action,
                new FormData(form),
                {
                    headers: {
                        Accept: 'application/json',
                    },
                }
            );

            appendMessage(response.data.message);
            updateStatus(
                response.data.conversation?.status
            );

            if (textarea) {
                textarea.value = '';
                textarea.focus();
            }

            if (imageInput) {
                imageInput.value = '';
            }

            renderSelectedImages();

            productCheckboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            updateProductSelection();

            if (productPicker) {
                productPicker.hidden = true;
            }
            productToggle?.classList.remove('active');
        } catch (error) {
            showError(
                error.response?.data?.message
                || 'Không thể gửi phản hồi. Vui lòng thử lại.'
            );
        } finally {
            button?.removeAttribute('disabled');
        }
    });

    scrollToLatest();
});
</script>
@endpush
