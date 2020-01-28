<template>
    <div>
        <p v-for="message in messages">{{ message }}</p>
        <input v-model="text">
        <input v-model="token" placeholder="token">
        <input v-model="id" placeholder="id">
        <input v-model="idDelete" placeholder="id delete">
        <button @click="deleteMessage">Delete</button>
        <button @click="getMessage">GetMessagesByUserId</button>
        <button @click="postMessage">submit</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                text: '',
                messages: [],
                token: '',
                id: '',
                idDelete: ''
            }
        },
        computed: {
            contentExists() {
                return this.text.length > 0;
            }
        },
        methods: {
            postMessage() {
                axios.post('messages/send', {
                    'message': this.text,
                    'id': this.id
                }, {headers: {'Authorization': "bearer " + this.token}}).then(({data}) => {
                    //this.messages.push(data);
                    console.log(data);
                });
            },
            deleteMessage() {
                axios.delete(`messages/${this.idDelete}`, {headers: {'Authorization': "bearer " + this.token}}).then(({data}) => {
                    console.log(data);
                });
            },
            getMessage() {
                axios.get(`messages/${this.id}`, {headers: {'Authorization': "bearer " + this.token}}).then(({data}) => {
                    //this.messages.push(data);
                    console.log(data);
                });
            }

        },
        created() {
            /* axios.get('/getAll').then(({data}) => {
                 this.messages = data;
             });*/
            // Registered client on public channel to listen to MessageSent event
            Echo.private('chat.1').listen('ChatMessage', (message) => {
                //this.messages.push(message);
                console.log(message)
            });
            Echo.connector.socket.on('connect', function () {
                console.log('connected', Echo.socketId());
            });
            Echo.connector.socket.on('disconnect', function () {
                console.log('disconnected');
            });
            Echo.connector.socket.on('reconnecting', function (attemptNumber) {
                console.log('reconnecting', attemptNumber);
            });
        }
    }
</script>