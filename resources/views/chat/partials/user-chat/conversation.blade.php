<div>
    @isset($group)
        <ul class="list-unstyled chat-conversation-list"
            x-init="
                Livewire.hook('message.processed', (message, component) => {
                    scrollToBottom('users-chat');
                });

                Echo.private('chat.{{ $group->id }}')
                    .listen('MessageSent', (e) => {
                        @this.call('messageReceived', e.id);
                    })">
            @foreach($messages as $message)
                <li class="chat-list @if ($message->user_id == auth()->id()) right @else left @endif" id="{{ $message->id }}"
                    wire:key="{{ $loop->index }}">
                    <div class="conversation-list">
                        @if ($message->user_id != auth()->id())
                            <div class="chat-avatar">
                                <img src="{{ $message->user->profile_photo_url }}" alt="{{ $message->user->name }}">
                            </div>
                        @endif
                        <div class="user-chat-content">
                            <div class="ctext-wrap">
                                <div class="ctext-wrap-content">
                                    @if(! $message->deleted_at && isset($message->options['reply']))
                                        <div class="replymessage-block mb-0 d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="conversation-name">{{ $message->options['reply']['user']['name'] }}, {{ Helper::convertMessageSentDatetimeForReply($message->options['reply']['created_at']) }}</h5>
                                                <p class="mb-0">{!! $message->options['reply']['content'] !!}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-sm btn-link mt-n2 me-n3 font-size-18">
                                                    <i class="ri-arrow-go-back-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @elseif(! $message->deleted_at && isset($message->options['forward']))
                                        <div class="replymessage-block mb-0 d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="conversation-name">{{ $message->options['forward']['user']['name'] }}</h5>
                                                <p class="mb-0">{!! $message->options['forward']['content'] !!}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-sm btn-link mt-n2 me-n3 font-size-18">
                                                    <i class="ri-arrow-go-back-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    <p class="mb-0 ctext-content text-start">{!! $message->contents !!}</p>
                                </div>
                                @empty($message->deleted_at)
                                    <div class="dropdown align-self-start message-box-drop">
                                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between reply-message" href="#" id="reply-message-0" data-bs-toggle="collapse" data-bs-target=".replyCollapse"
                                               wire:click="$emitTo('user-chat.input', 'messageReplying', {{ $message->id }})">Reply <i class="bx bx-share ms-2 text-muted"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal"
                                               wire:click="$emitTo('user-chat.forward-modal', 'messageForwarding', {{ $message->id }})">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-0">Copy <i class="bx bx-copy text-muted ms-2"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                            @if ($message->user_id != auth()->id())
                                                <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Mark as Unread <i class="bx bx-message-error text-muted ms-2"></i></a>
                                            @endif
                                            @if ($message->user_id == auth()->id())
                                                <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" href="#"
                                                   wire:click="deleteMessage({{ $message->id }})">Delete <i class="bx bx-trash text-muted ms-2"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                @endempty
                            </div>
                            <div class="conversation-name">
                                <small class="text-muted time">{{ $message->readable_created_at }}</small>
                                <span class="text-success check-message-icon"><i class="bx bx-check-double"></i></span>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endisset
</div>

@push('modals')
    @livewire('user-chat.forward-modal')
@endpush
