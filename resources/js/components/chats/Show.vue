<template>
    <div>
        <div id="messages-block" class="pb-5">
            <div class="d-flex"
                 :class="{'justify-content-end': message.user !== guestId}"
                 v-for="message in messages">
                <div
                    class="card mt-3"
                    style="width: 30%;">
                    <div class="card-body">
                        {{ message.text }}
                    </div>
                </div>
            </div>
            <p class="text-center" id="conversation-empty-text" v-if="messages.length === 0">No messages</p>
        </div>
        <div style="margin-top: 10px;clear: both">
                    <textarea name="text"
                              id="message_area"
                              cols="30"
                              placeholder="Enter text..."
                              class="form-control"
                              v-model="textareaValue"
                              rows="4"></textarea>
            <div class="mt-1 d-flex justify-content-end">
                <button @click="sendMessage" class="btn btn-primary text-white">
                    Send
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Show",
    data() {
        return {
            messages: [],
            textareaValue: '',
            chat_id: null
        }
    },
    computed: {
        guestId() {
            return +location.pathname.split('/').pop();
        }
    },
    methods: {
        fetchAll() {
            axios.get(`/chat/${this.guestId}`)
                .then(({data}) => {
                    this.messages = data.data.messages;
                    this.chat_id = data.data.chat_id;

                    this.setSocket();
                })
        },
        sendMessage() {
            if (this.textareaValue.length === 0) {
                return;
            }
            axios.post(`/chat/message/${this.guestId}`, {text: this.textareaValue})
                .then(({data}) => {
                    // this.messages.push(data.data);
                    this.textareaValue = "";

                }).catch((response) => {
                console.log(response);
            });
        },
        setSocket() {
            Echo.private(`conversation.${this.chat_id}`)
                .listen('.as.message', (e) => {
                    this.messages.push(e);
                });
        }
    },
    mounted() {
        this.fetchAll();

    },
    created() {
    }
}
</script>

<style scoped>

</style>
