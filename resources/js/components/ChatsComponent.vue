<template>
    <div class="row">
        <div class="col-8">
            <div class="card card-default">
                <div class="card-header">Messages</div>
                <div class="card-body p-0">
                    <ul id="messageList" class="list-unstyled" style="height: 300px; overflow-y: scroll;">
                        <li class="p-2" v-for="(message, index) in messages" :key="index">
                            <strong>{{ message.user.name }}</strong>
                            {{ message.message }}
                        </li>
                    </ul>
                </div>

                <input
                    @keydown="sendTypingEvent"
                    @keyup.enter="sendMessage"
                    v-model="newMessage"
                    type="text"
                    name="message"
                    placeholder="Enter your message..."
                    class="form-control"
                >
            </div>
            <span class="text-muted" v-if="activeUser">{{ activeUser.name }} is typing...</span>
        </div>

        <div class="col-4">
            <div class="card card-default">
                <div class="card-header">Active Users</div>
                <div class="card-body">
                    <ul>
                        <li class="py-2" v-for="(user, index) in users" :key="index">{{ user.name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import Echo from 'laravel-echo';

    export default {
        props: ['user'],

        data() {
            return {
                newMessage: '',
                messages: [],
                users: [],
                activeUser: false,
                typingTimer: false,
            }
        },

        created() {
            this.fetchMessages();

            window.Echo.join('chat')
                .here(users => {
                    console.log(users);                    
                    this.users = users
                })
                .joining(user => {
                    this.users.push(user);
                })
                .leaving(user => {
                    this.users = this.users.filter(u => u.id != user.id);
                })
                .listen('MessageSent', event => {
                    this.messages.push(event.message);

                    this.scrollToBottom();
                })
                .listenForWhisper('typing', user => {
                    this.activeUser = user;

                    if (this.typingTimer) {
                        clearTimeout(this.typingTimer);
                    }

                    this.typingTimer = setTimeout(() => {
                        this.activeUser = false;
                    }, 3000);
                })
        },

        methods: {
            fetchMessages() {
                axios.get('messages').then(response => {
                    this.messages = response.data;
                })
            },
            
            sendMessage() {
                this.messages.push({
                    user: this.user,
                    message: this.newMessage
                });

                this.scrollToBottom();

                axios.post('message', { message: this.newMessage });

                this.newMessage = '';
            },

            sendTypingEvent() {
                window.Echo.join('chat').whisper('typing', this.user);
            },

            scrollToBottom() {
                setTimeout(() => {
                    var $list = $('#messageList');
                    $list.scrollTop($list[0].scrollHeight);
                }, 100)
            }
        }
    }
</script>
