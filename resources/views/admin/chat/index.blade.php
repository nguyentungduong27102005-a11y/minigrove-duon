@extends('layouts.admin')

@section('title', 'Chat hỗ trợ - MiniGrove Admin')
@section('page-title', 'Chat hỗ trợ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-chat.css') }}">
@endpush

@section('content')
<div
    data-admin-chat-index
    data-admin-id="{{ auth()->id() }}"
>
    <div class="admin-chat-heading">
        <div>
            <h1>Tư vấn khách hàng</h1>
            <p>
                Tiếp nhận và phản hồi các hội thoại hỗ trợ của Customer theo thời gian thực.
            </p>
        </div>

        <span class="admin-chat-live">
            Realtime
        </span>
    </div>

    <div class="card admin-chat-card">
        <div class="admin-chat-card-head">
            <h2>Hội thoại cần xử lý</h2>
            <p>
                Hội thoại mới sẽ tự xuất hiện mà không cần tải lại trang.
            </p>
        </div>

        <div
            class="admin-chat-list"
            data-chat-conversation-list
        >
            @foreach ($conversations as $conversation)
                <a
                    href="{{ route('admin.chat.show', $conversation->id) }}"
                    class="admin-chat-item"
                    data-conversation-id="{{ $conversation->id }}"
                >
                    <div class="admin-chat-item-main">
                        <div class="admin-chat-item-top">
                            <span class="admin-chat-customer">
                                Customer #{{ $conversation->customer_id }}
                            </span>

                            <span
                                class="
                                    admin-chat-status
                                    admin-chat-status-{{ $conversation->status }}
                                "
                                data-conversation-status
                            >
                                {{
                                    $conversation->status === 'waiting'
                                        ? 'ĐANG CHỜ'
                                        : 'ĐANG MỞ'
                                }}
                            </span>
                        </div>

                        <div
                            class="admin-chat-subject"
                            data-conversation-subject
                        >
                            {{ $conversation->subject ?: 'Yêu cầu hỗ trợ' }}
                        </div>

                        <div class="admin-chat-topic">
                            Chủ đề:
                            <span data-conversation-topic>
                                {{ $conversation->topic }}
                            </span>
                        </div>
                    </div>

                    <div class="admin-chat-item-side">
                        <span
                            class="
                                admin-chat-assignment
                                {{
                                    $conversation->assigned_admin_id
                                        ? 'mine'
                                        : 'unassigned'
                                }}
                            "
                            data-conversation-assignment
                        >
                            {{
                                $conversation->assigned_admin_id
                                    ? 'Bạn đang xử lý'
                                    : 'Chưa nhận'
                            }}
                        </span>

                        <span
                            class="admin-chat-time"
                            data-conversation-time
                        >
                            {{
                                $conversation->last_message_at
                                    ? $conversation->last_message_at->format('d/m/Y H:i')
                                    : ''
                            }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div
            class="admin-chat-empty"
            data-chat-empty
            @if ($conversations->count()) hidden @endif
        >
            <div class="admin-chat-empty-icon">💬</div>
            Hiện không có hội thoại nào cần xử lý.
        </div>

        @if ($conversations->hasPages())
            <div class="admin-chat-pagination">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.querySelector('[data-admin-chat-index]');

    if (!root || !window.Echo) {
        return;
    }

    const adminId = Number(root.dataset.adminId);
    const list = root.querySelector('[data-chat-conversation-list]');
    const empty = root.querySelector('[data-chat-empty]');

    const statusLabel = function (status) {
        if (status === 'waiting') return 'ĐANG CHỜ';
        if (status === 'open') return 'ĐANG MỞ';
        return String(status || '').toUpperCase();
    };

    const refreshEmptyState = function () {
        if (!empty) return;
        empty.hidden = Boolean(
            list.querySelector('[data-conversation-id]')
        );
    };

    const removeConversation = function (conversationId) {
        list
            .querySelector(
                '[data-conversation-id="' + conversationId + '"]'
            )
            ?.remove();

        refreshEmptyState();
    };

    const upsertConversation = function (conversation) {
        if (!conversation?.id) return;

        const assignedAdminId =
            conversation.assigned_admin_id === null
            || conversation.assigned_admin_id === undefined
                ? null
                : Number(conversation.assigned_admin_id);

        if (
            conversation.status === 'closed'
            || (
                assignedAdminId !== null
                && assignedAdminId !== adminId
            )
        ) {
            removeConversation(conversation.id);
            return;
        }

        let item = list.querySelector(
            '[data-conversation-id="' + conversation.id + '"]'
        );

        if (!item) {
            item = document.createElement('a');
            item.className = 'admin-chat-item';
            item.dataset.conversationId = conversation.id;
            item.href =
                @json(url('/admin/chat'))
                + '/'
                + conversation.id;

            item.innerHTML =
                '<div class="admin-chat-item-main">'
                + '<div class="admin-chat-item-top">'
                + '<span class="admin-chat-customer"></span>'
                + '<span class="admin-chat-status" data-conversation-status></span>'
                + '</div>'
                + '<div class="admin-chat-subject" data-conversation-subject></div>'
                + '<div class="admin-chat-topic">Chủ đề: <span data-conversation-topic></span></div>'
                + '</div>'
                + '<div class="admin-chat-item-side">'
                + '<span class="admin-chat-assignment" data-conversation-assignment></span>'
                + '<span class="admin-chat-time" data-conversation-time></span>'
                + '</div>';

            list.prepend(item);
        } else {
            list.prepend(item);
        }

        item.querySelector('.admin-chat-customer').textContent =
            'Customer #' + conversation.customer_id;

        const status = item.querySelector(
            '[data-conversation-status]'
        );
        status.textContent = statusLabel(conversation.status);
        status.className =
            'admin-chat-status admin-chat-status-'
            + conversation.status;

        item.querySelector(
            '[data-conversation-subject]'
        ).textContent =
            conversation.subject || 'Yêu cầu hỗ trợ';

        item.querySelector(
            '[data-conversation-topic]'
        ).textContent =
            conversation.topic || 'GENERAL';

        const assignment = item.querySelector(
            '[data-conversation-assignment]'
        );
        assignment.textContent =
            assignedAdminId === null
                ? 'Chưa nhận'
                : 'Bạn đang xử lý';
        assignment.className =
            'admin-chat-assignment '
            + (
                assignedAdminId === null
                    ? 'unassigned'
                    : 'mine'
            );

        item.querySelector(
            '[data-conversation-time]'
        ).textContent =
            conversation.last_message_at_label || '';

        refreshEmptyState();
    };

    window.Echo
        .private('chat.admin')
        .listen('.chat.message.sent', function (event) {
            upsertConversation(event.conversation);
        })
        .listen('.chat.conversation.updated', function (event) {
            upsertConversation(event.conversation);
        });

    refreshEmptyState();
});
</script>
@endpush
