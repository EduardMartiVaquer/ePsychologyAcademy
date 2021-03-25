<template id="test-2-template">
<div>
    <div id="video-out-fixed" :class="videoOutBig ? 'video-out-big' : ''" style="opacity: 1">
        <video id="localVideo" class="inner-video" autoplay playsinline :controls="false" :muted="true"/>
    </div>
    <div id="video-in" class="d-flex flex-wrap" v-bind:style="size == 1 ? 'width: 300px !important; height: 168.75px !important' : ''">
        <div v-for="(stream, index) in remoteStreams" :key="index" :style="'width: ' + scaledProportions[index].width + '%; height: ' + scaledProportions[index].height + '%'">
            <video class="inner-video" autoplay playsinline :controls="false" :muted="true"/>
        </div>
    </div>
</div>
</template>

<script>

export default {
    props: [],
    template: "#test-2-template",
    data(){
        return {
            echoConnected: false,
            connectedUsers: [],
            size: 0,
            videoOutBig: false,
            peer: null
        }
    },
    computed: {
        user(){
            return this.$store.state.currentUser.user;
        },
        hasOtherUsers(){
            var hasOtherUsers = false;
            this.connectedUsers.forEach(user => {
                if(user.id != this.user.id){
                    hasOtherUsers = true;
                }
            });
            return hasOtherUsers
        },
        classroom(){
            return {
                id: 1
            }
        },
        scaledProportions(){
            var index = 0;
            var matrix = [];
            var elements = [];

            var nOfRows = this.remoteStreams.length < 3 ? 1 : this.remoteStreams.length < 9 ? 2 : this.remoteStreams.length.length < 10 ? 3 : this.remoteStreams.length < 17 ? 4 : (Math.round(this.remoteStreams.length / 10) * 10)
            var maxRowElements = this.remoteStreams.length < 5 ? 2 : this.remoteStreams.length < 7 ? 3 : this.remoteStreams.length < 17 ? 4 : 5;
            
            var elementsPushed = 0;
            for(var r = 0; r < nOfRows; r++){
                matrix.push([]);
                for(var e = 0; e < maxRowElements; e++){
                    if(elementsPushed < this.remoteStreams.length){
                        matrix[r].push(index);
                        index += 1;
                        elementsPushed += 1;
                    } else {
                        break;
                    }
                }
                if(elementsPushed >= this.remoteStreams.length){
                    break;
                }
            }
            
            var height = Math.round(100 / nOfRows)
            var elementIndex = 0;
            for(var row in matrix){
                var width = Math.round(100 / matrix[row].length);
                for(var element in matrix[row]){

                    elements.push({
                        width: width,
                        height: height
                    })
                    
                    elementIndex += 1;
                }
            }
            return elements;
        },
    },
    methods: {
        connectToPeer(id){
            this.conn = peer.connect(`user-${id}`);
            conn.on('open', () => {
                conn.send('hi!');
            });
        },
        connectEcho(){
            Echo.join(`ClassCallChannel.${this.class.id}`)
            .here((users) => {
                console.log("here");
                console.log(users);
                this.connectedUsers = users;
                this.echoConnected = true;
                if(users.length <= 1){
                    this.createPeerOffer().then(
                        success => console.log(success),
                        error => console.log(error)
                    )
                }
            })
            .joining((user) => {
                console.log("joining");
                console.log(user);
                var isAlreadyListed = this.connectedUsers.some(u => {
                    return u.id == user.id;
                })
                if(!isAlreadyListed){
                    this.connectedUsers.push(user);
                }
                if(this.peerOffer != null){
                    window.setTimeout(() => {
                        this.sendPeerOffer();
                    }, 2000)
                }
            })
            .leaving((user) => {
                console.log("leaving");
                console.log(user);
                var index = null;
                this.connectedUsers.forEach(u => {
                    if(u.id == user.id){
                        index = true;
                    }
                })
                if(index != null){
                    this.connectedUsers.splice(index, 1);
                }
            })
            .listen('ClassCallMessageEvent', (e) => {
                console.log(e);
            })
        },
    },
    created(){
        $(document).ready(() => {
            
            this.connectToPeer(this.user.id == 1 ? 4 : 1)
        });
    },
    destroyed() {
        Echo.leave(`ClassCallChannel.${classroom.id}`);
    },
}
</script>