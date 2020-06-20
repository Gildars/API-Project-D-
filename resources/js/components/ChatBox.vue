<template>
    <div>
        <p v-for="message in messages">{{ message }}</p>
        <input v-model="defenderId">
        <input v-model="token" placeholder="token">
        <input v-model="id" placeholder="id">
        <input v-model="idDelete" placeholder="id delete">
        <button @click="test">Test</button>
        <button @click="deleteMessage">Delete</button>
        <button @click="attack">GetMessagesByUserId</button>
        <button @click="postMessage">submit</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                text: '',
                messages: [],
                token: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuZWxvbmljYS5sb2NhbFwvbG9naW4iLCJpYXQiOjE1ODg1OTEyMzQsImV4cCI6MTU4ODU5NDgzNCwibmJmIjoxNTg4NTkxMjM0LCJqdGkiOiJnOE55SlJXd0FCajZPUzVUIiwic3ViIjoxMSwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9.Iv6faIlYoMUtZRJ3wGDDoUt7PAgwoanwslDkyQQI4HA',
                defenderId: '',
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
                    'id': this.defenderId
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
            attack() {
                axios.get(`character/attack/${this.defenderId}`, {headers: {'Authorization': "bearer " + this.token}}).then(({data}) => {
                    //this.messages.push(data);
                    console.log(data);
                });
            },
            test(){
                console.log('test');
                let ws = new WebSocket('ws://api.elonica.local:5200/ws');
                ws.onopen = function () {
                    console.log('socket connection opened properly');
                    ws.send("Hello World"); // send a message
                    console.log('message sent');
                };

                ws.onmessage = function (evt) {
                    console.log("Message received = " + evt.data);
                };

                ws.onclose = function () {
                    // websocket is closed.
                    console.log("Connection closed...");
                };
            }

        },
        mounted() {
            console.log('test');
            let ws = new WebSocket('ws://api.elonica.local:5200/ws');
            ws.onopen = function () {
                console.log('socket connection opened properly');
                ws.send("Hello World"); // send a message
                console.log('message sent');
            };

            ws.onmessage = function (evt) {
                console.log("Message received = " + evt.data);
            };

            ws.onclose = function () {
                // websocket is closed.
                console.log("Connection closed...");
            };
            /* axios.get('/getAll').then(({data}) => {
                 this.messages = data;
             });*/
            // Registered client on public channel to listen to MessageSent event
           /* Echo.private('chat.1').listen('ChatMessage', (message) => {
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
            });*/
        }
    }
</script>
