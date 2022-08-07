<template>
    <div>
        <div class="card my-2" v-for="guest in guests">
            <div class="card-body col-8 mx-auto">
                <div class="d-flex justify-content-between">
                    <p class="m-0">{{ guest.name }}</p>
                    <span>
                        <a v-bind:href="getShowUrl(guest.id)" class="m-0">
                            <i class="bi bi-chat-dots"></i>
                        </a>
                        <a href="javascript:" @click="deleteChat(guest.id)" class="m-0 text-danger ms-2">
                            <i class="bi bi-trash"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Index",
    data() {
        return {
            guests: [],
            host: {}
        };
    },
    methods: {
        fetchChats() {
            axios.get('/chat')
                .then(({data}) => {
                    this.guests = data.data.guests;
                    this.host = data.data.host;
                    this.setSocket();
                })
        },
        getShowUrl(id) {
            return `${location.origin}/conversation/${id}`;
        },
        deleteChat(id) {
            axios.delete(`/chat/${id}`);
        },
        setSocket() {
            Echo.private(`chat.list.${this.host.id}`)
                .listen('.new.chat', (e) => {
                    const guest = e.guest.id !== this.host.id ? e.guest : e.host;
                    this.guests.push(guest);
                });
            Echo.private(`chat.deleted.${this.host.id}`)
                .listen('.chat.delete', (e) => {
                    this.guests = this.guests.filter((item) => !e.includes(item.id));
                });
        }
    },
    mounted() {
        this.fetchChats();
    }
}
</script>

<style scoped>

</style>
