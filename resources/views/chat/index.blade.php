@extends('layouts.customer')


@section(
    'title',
    'Chat hỗ trợ - MiniGrove'
)


@push('styles')

<link
    rel="stylesheet"
    href="{{
        asset('css/chat.css')
        . '?v='
        . filemtime(public_path('css/chat.css'))
    }}"
>

@endpush


@section('content')

<section
    class="customer-chat-page"
    data-customer-chat
    data-user-id="{{ auth()->id() }}"
    data-conversation-id="{{ $conversation?->id ?? '' }}"
    data-has-chat-history="{{ $hasChatHistory ? '1' : '0' }}"
>

    <div class="customer-container">

        <div class="customer-chat-heading">

            <div>

                <span class="customer-chat-eyebrow">
                    💬 HỖ TRỢ MINI GROVE
                </span>

                <h1>
                    Chat với MiniGrove
                </h1>

                <p>
                    Gửi câu hỏi về sản phẩm, đơn hàng hoặc cách chăm sóc Terrarium.
                </p>

            </div>

                <span
                    class="
                        customer-chat-status
                        {{
                            $conversation
                                ? 'customer-chat-status-' . $conversation->status
                                : ''
                        }}
                    "
                    data-chat-status
                    @if (!$conversation) hidden @endif
                >
                    @if ($conversation)
                    @if ($conversation->status === 'open')
                        Đang mở
                    @elseif ($conversation->status === 'waiting')
                        Đang chờ phản hồi
                    @else
                        {{ ucfirst($conversation->status) }}
                    @endif
                    @endif
                </span>

        </div>


        <div class="customer-chat-shell">

            <div class="customer-chat-main">

                <div
                    class="customer-chat-messages"
                    data-chat-messages
                >

                    @if ($conversation && $messages->isNotEmpty())

                        @foreach ($messages as $message)

                            @php
                                $isMine =
                                    (int) $message->sender_id
                                    === (int) auth()->id();
                            @endphp

                            <div
                                class="
                                    customer-chat-message-row
                                    {{
                                        $isMine
                                            ? 'customer-chat-message-row-mine'
                                            : 'customer-chat-message-row-support'
                                    }}
                                "
                                data-message-id="{{ $message->id }}"
                            >

                                <div
                                    class="
                                        customer-chat-bubble
                                        {{
                                            $isMine
                                                ? 'customer-chat-bubble-mine'
                                                : 'customer-chat-bubble-support'
                                        }}
                                    "
                                >

                                    <div class="customer-chat-message-author">
                                        {{
                                            $isMine
                                                ? 'Bạn'
                                                : 'MiniGrove Support'
                                        }}
                                    </div>

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

                                    <div class="customer-chat-message-content">
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

                                            {!! $linkedContent !!}
                                        @endif
                                    </div>

                                    @if ($message->productCards->isNotEmpty())
                                        <div class="customer-chat-products">
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
                                                        class="customer-chat-product-card"
                                                    >
                                                @else
                                                    <div class="customer-chat-product-card">
                                                @endif

                                                    <span class="customer-chat-product-image">
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

                                                    <span class="customer-chat-product-info">
                                                        <strong>
                                                            {{ $productCard->product_name }}
                                                        </strong>

                                                        <small>
                                                            {{ $productCard->product_sku }}
                                                        </small>

                                                        <span class="customer-chat-product-price">
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

                                                    <span class="customer-chat-product-view">
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
                                        <div class="customer-chat-attachments">
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
                                                    class="customer-chat-attachment"
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

                                    <div class="customer-chat-message-time">
                                        {{
                                            $message->sent_at
                                                ? $message->sent_at->format('H:i d/m/Y')
                                                : ''
                                        }}
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @else

                        <div
                            class="customer-chat-empty"
                            data-chat-empty
                        >

                            <div class="customer-chat-empty-icon">
                                🌿
                            </div>

                            <h2>
                                Bắt đầu cuộc trò chuyện
                            </h2>

                            <p>
                                Hãy gửi tin nhắn đầu tiên. Nhân viên MiniGrove sẽ phản hồi trong hội thoại này.
                            </p>

                        </div>

                    @endif

                </div>

                @if (!$hasChatHistory)
                    <div
                        class="customer-chat-suggestions"
                        data-chat-suggestions
                        hidden
                    >
                        <div class="customer-chat-suggestions-head">
                            <span>✨</span>

                            <div>
                                <strong>Bạn cần MiniGrove tư vấn gì?</strong>
                                <small>Chọn một câu hỏi để bắt đầu nhanh hơn.</small>
                            </div>
                        </div>

                        <div class="customer-chat-suggestion-list">
                            @foreach ([
                                'Tôi mới chơi Terrarium, MiniGrove tư vấn giúp tôi sản phẩm phù hợp nhé.',
                                'Tôi muốn hỏi cách chăm sóc và tưới nước cho Terrarium.',
                                'MiniGrove kiểm tra giúp tôi tình trạng đơn hàng được không?',
                                'Tôi muốn được tư vấn cách sử dụng Voucher.',
                                'Tôi muốn tự thiết kế một Terrarium, nên bắt đầu từ đâu?',
                            ] as $suggestion)
                                <button
                                    type="button"
                                    class="customer-chat-suggestion"
                                    data-chat-suggestion
                                    data-question="{{ $suggestion }}"
                                >
                                    {{ $suggestion }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif


                <form
                    method="POST"
                    action="{{ route('chat.store') }}"
                    class="customer-chat-form"
                    data-customer-chat-form
                    enctype="multipart/form-data"
                >

                    @csrf

                    <div class="customer-chat-compose">
                        <textarea
                            id="content"
                            name="content"
                            rows="3"
                            maxlength="5000"
                            placeholder="Nhập tin nhắn cho MiniGrove..."
                        >{{ old('content') }}</textarea>
                    </div>

                    <div
                        class="customer-chat-image-preview"
                        data-chat-image-preview
                        hidden
                    ></div>

                    <div class="customer-chat-compose-footer">
                        <div class="customer-chat-tools">
                            <label class="customer-chat-tool-button">
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

                            <span class="customer-chat-file-hint">
                                Tối đa 5 ảnh · 5MB/ảnh
                            </span>
                        </div>

                        <button
                            type="submit"
                            class="customer-chat-send"
                        >
                            <span>Gửi</span>
                            <span aria-hidden="true">➜</span>
                        </button>
                    </div>

                    @error('content')

                        <div class="customer-chat-error">
                            {{ $message }}
                        </div>

                    @enderror

                    <div
                        class="customer-chat-error"
                        data-chat-form-error
                        hidden
                    ></div>

                </form>

            </div>


            <aside class="customer-chat-sidebar">

                <div class="customer-chat-info-card">

                    <h3>
                        Hỗ trợ trực tiếp
                    </h3>

                    <p>
                        Cuộc trò chuyện của bạn chỉ được dùng để xử lý yêu cầu hỗ trợ trong MiniGrove.
                    </p>

                    @if ($conversation)

                        <div class="customer-chat-info-row">

                            <span>
                                Mã hội thoại
                            </span>

                            <strong>
                                #{{ $conversation->id }}
                            </strong>

                        </div>

                        <div class="customer-chat-info-row">

                            <span>
                                Chủ đề
                            </span>

                            <strong>
                                {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
                            </strong>

                        </div>

                    @endif

                </div>


                @if ($closedConversations->isNotEmpty())

                    <div class="customer-chat-history">

                        <h3>
                            Hội thoại đã đóng
                        </h3>

                        @foreach ($closedConversations as $closedConversation)

                            <div class="customer-chat-history-item">

                                <strong>
                                    #{{ $closedConversation->id }}
                                </strong>

                                <span>
                                    {{
                                        $closedConversation->subject
                                        ?: 'Yêu cầu hỗ trợ'
                                    }}
                                </span>

                                <small>
                                    {{
                                        $closedConversation->closed_at
                                            ? $closedConversation->closed_at->format('d/m/Y H:i')
                                            : ''
                                    }}
                                </small>

                            </div>

                        @endforeach

                    </div>

                @endif

            </aside>

        </div>

    </div>

</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.querySelector('[data-customer-chat]');

    if (!root) {
        return;
    }

    const userId = Number(root.dataset.userId);
    const messages = root.querySelector('[data-chat-messages]');
    const empty = root.querySelector('[data-chat-empty]');
    const form = root.querySelector('[data-customer-chat-form]');
    const status = root.querySelector('[data-chat-status]');
    const errorBox = root.querySelector('[data-chat-form-error]');
    const imageInput = root.querySelector('[data-chat-images]');
    const imagePreview = root.querySelector(
        '[data-chat-image-preview]'
    );
    const suggestions = root.querySelector(
        '[data-chat-suggestions]'
    );

    let conversationId =
        Number(root.dataset.conversationId || 0);
    let subscribedConversationId = null;

    const suggestionStorageKey =
        'minigrove_chat_suggestions_seen_' + userId;

    if (suggestions) {
        const hasSeenSuggestions =
            window.localStorage.getItem(
                suggestionStorageKey
            ) === '1';

        if (!hasSeenSuggestions) {
            suggestions.hidden = false;
            window.localStorage.setItem(
                suggestionStorageKey,
                '1'
            );
        }
    }

    root.querySelectorAll('[data-chat-suggestion]')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const textarea = form?.querySelector(
                    'textarea[name="content"]'
                );

                if (!textarea) return;

                textarea.value = button.dataset.question || '';
                textarea.focus();
            });
        });

    const showError = function (message) {
        if (!errorBox) return;

        errorBox.textContent = message;
        errorBox.hidden = false;
    };

    const clearError = function () {
        if (!errorBox) return;

        errorBox.hidden = true;
        errorBox.textContent = '';
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
            item.className = 'customer-chat-preview-item';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;

            item.append(img);
            imagePreview.append(item);
        });
    };

    imageInput?.addEventListener('change', function () {
        clearError();

        const files = Array.from(imageInput.files || []);

        if (files.length > 5) {
            imageInput.value = '';
            renderSelectedImages();
            showError('Mỗi tin nhắn chỉ được chọn tối đa 5 ảnh.');
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

    const scrollToLatest = function () {
        if (!messages) return;
        messages.scrollTop = messages.scrollHeight;
    };

    const updateStatus = function (value) {
        if (!status || !value) return;

        const labels = {
            open: 'Đang mở',
            waiting: 'Đang chờ phản hồi',
            closed: 'Đã đóng',
        };

        status.hidden = false;
        status.textContent =
            labels[value] || value;
        status.className =
            'customer-chat-status '
            + 'customer-chat-status-' + value;

        if (value === 'closed') {
            form
                ?.querySelector('textarea')
                ?.setAttribute('disabled', 'disabled');

            form
                ?.querySelector('button[type="submit"]')
                ?.setAttribute('disabled', 'disabled');
        }
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

        empty?.remove();

        const isMine =
            Number(message.sender_id) === userId;

        const row = document.createElement('div');
        row.className =
            'customer-chat-message-row '
            + (
                isMine
                    ? 'customer-chat-message-row-mine'
                    : 'customer-chat-message-row-support'
            );
        row.dataset.messageId = message.id;

        const bubble = document.createElement('div');
        bubble.className =
            'customer-chat-bubble '
            + (
                isMine
                    ? 'customer-chat-bubble-mine'
                    : 'customer-chat-bubble-support'
            );

        const author = document.createElement('div');
        author.className =
            'customer-chat-message-author';
        author.textContent =
            isMine
                ? 'Bạn'
                : 'MiniGrove Support';

        const content = document.createElement('div');
        content.className =
            'customer-chat-message-content';

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

        appendLinkedText(content, message.content || '');

        const attachments = document.createElement('div');
        attachments.className = 'customer-chat-attachments';

        (message.attachments || []).forEach(function (attachment) {
            const link = document.createElement('a');
            link.href = attachment.url;
            link.target = '_blank';
            link.rel = 'noopener';
            link.className = 'customer-chat-attachment';

            const image = document.createElement('img');
            image.src = attachment.url;
            image.alt = attachment.name || 'Ảnh đính kèm';
            image.loading = 'lazy';

            link.append(image);
            attachments.append(link);
        });

        const products = document.createElement('div');
        products.className = 'customer-chat-products';

        (message.products || []).forEach(function (product) {
            const card = document.createElement(
                product.url ? 'a' : 'div'
            );
            card.className = 'customer-chat-product-card';

            if (product.url) {
                card.href = product.url;
                card.target = '_blank';
                card.rel = 'noopener';
            }

            const imageBox = document.createElement('span');
            imageBox.className = 'customer-chat-product-image';

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
            info.className = 'customer-chat-product-info';

            const name = document.createElement('strong');
            name.textContent = product.name || 'Sản phẩm';

            const sku = document.createElement('small');
            sku.textContent = product.sku || '';

            const price = document.createElement('span');
            price.className = 'customer-chat-product-price';

            const currentPrice = document.createElement('b');
            currentPrice.textContent = product.price_label || '';
            price.append(currentPrice);

            if (product.has_sale) {
                const oldPrice = document.createElement('del');
                oldPrice.textContent =
                    product.list_price_label || '';
                price.append(oldPrice);
            }

            info.append(name, sku, price);

            const view = document.createElement('span');
            view.className = 'customer-chat-product-view';
            view.textContent = 'Xem →';

            card.append(imageBox, info, view);
            products.append(card);
        });

        const time = document.createElement('div');
        time.className =
            'customer-chat-message-time';
        time.textContent =
            message.sent_at_label || '';

        bubble.append(author, content);

        if ((message.attachments || []).length > 0) {
            bubble.append(attachments);
        }

        if ((message.products || []).length > 0) {
            bubble.append(products);
        }

        bubble.append(time);
        row.append(bubble);
        messages.append(row);

        scrollToLatest();
    };

    const subscribe = function (id) {
        if (
            !window.Echo
            || !id
            || subscribedConversationId === id
        ) {
            return;
        }

        subscribedConversationId = id;

        window.Echo
            .private('chat.conversation.' + id)
            .listen('.chat.message.sent', function (event) {
                appendMessage(event.message);
                updateStatus(event.conversation?.status);
            })
            .listen(
                '.chat.conversation.updated',
                function (event) {
                    updateStatus(
                        event.conversation?.status
                    );
                }
            );
    };

    if (conversationId) {
        subscribe(conversationId);
    }

    form?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const textarea = form.querySelector(
            'textarea[name="content"]'
        );
        const content = textarea?.value.trim();
        const imageCount =
            imageInput?.files?.length || 0;

        if (!content && imageCount === 0) {
            showError(
                'Vui lòng nhập tin nhắn hoặc chọn ít nhất một ảnh.'
            );
            return;
        }

        const button = form.querySelector(
            'button[type="submit"]'
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

            conversationId =
                Number(response.data.conversation_id);

            root.dataset.conversationId =
                String(conversationId);

            appendMessage(response.data.message);
            updateStatus(
                response.data.conversation?.status
            );
            subscribe(conversationId);

            if (textarea) {
                textarea.value = '';
                textarea.focus();
            }

            if (imageInput) {
                imageInput.value = '';
            }

            renderSelectedImages();

            if (suggestions) {
                suggestions.hidden = true;
            }
        } catch (error) {
            if (errorBox) {
                errorBox.textContent =
                    error.response?.data?.message
                    || 'Không thể gửi tin nhắn. Vui lòng thử lại.';
                errorBox.hidden = false;
            }
        } finally {
            button?.removeAttribute('disabled');
        }
    });

    scrollToLatest();
});
</script>
@endpush
