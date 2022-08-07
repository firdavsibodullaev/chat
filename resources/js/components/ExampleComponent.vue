<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h1>Xozir chatda</h1>
                    <ul class="list-group">
                        <li class="list-group-item" v-for="user in users">{{ user.name }}</li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            users: []
        };
    },
    mounted() {
        Echo.join(`chat`)
            .here((users) => {
                this.users = users;
            })
            .joining((user) => {
                console.log(user.name + " kanalga qo'shildi");
                this.users.push(user);
            })
            .leaving((user) => {
                this.users = this.users.filter((item) => item.id !== user.id);
                console.log(user.name + " kanalni tark etdi");
            })
            .error((error) => {
                console.error(error);
            });
    }
}
</script>
