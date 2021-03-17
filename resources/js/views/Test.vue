<template id="test-template">
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
    template: "#test-template",
    data(){
        return {
            echoConnected: false,
            connectedUsers: [],
            callConstraints: {
                video: true,
                audio: true
            },
            cameraList: [],
            localStream: null,
            remoteStreams: [],
            iceConfiguration: {
                iceServers: [
                    {
                        url: 'turn:18.184.14.34:3478',
                        urls: 'turn:18.184.14.34:3478',
                        username: 'ubuntu',
                        credential: 'Clara123123U*'
                    }
                ]
            },
            peerConnection: null,
            peerOffer: null,
            peerAnswer: null,
            size: 0,
            videoOutBig: false,
            candidate: null
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
        class(){
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
        }
    },
    methods: {
        //Local methods
        getConnectedDevices(type, callback){
            navigator.mediaDevices.enumerateDevices()
            .then(devices => {
                const filtered = devices.filter(device => device.kind === type);
                callback(filtered);
            });
        },
        setCameraConstraints(cameraId, width, height) {
            this.callConstraints = {
                'audio': {'echoCancellation': true},
                'video': {
                    'deviceId': cameraId,
                    'width': width,
                    'height': height
                }
            }
        },
        startCamera(){
            navigator.mediaDevices.getUserMedia(this.callConstraints)
            .then(stream => {
                this.setLocalStream(stream);
                this.showLocalStream();
            })
            .catch(error => {
                console.error('Error accessing media devices.', error);
            });
        },
        setLocalStream(stream){
            this.localStream = stream
        },
        showLocalStream(){
            const videoElement = document.querySelector('video#localVideo');
            videoElement.srcObject = this.localStream;
            window.setTimeout(() => {
                this.createPeerConnection();
            }, 1000)
        },

        //Remote methods
        createPeerConnection(){
            if(this.peerConnection == null){
                this.peerConnection = new RTCPeerConnection(this.iceConfiguration);
                this.peerConnection.onicegatheringstatechange = event => {
                    console.log("gathering")
                    console.log(event);
                }
                this.peerConnection.onicecandidate = event => {
                    console.log("ice")
                    console.log(event);
                    if (event.candidate) {
                        this.currentCandidate = event.candidate
                        this.sendIceCandidate();
                    }
                };
                this.peerConnection.onconnectionstatechange = event => {
                    console.log("connectionstatechange")
                    console.log(event);
                    if (this.peerConnection.connectionState === 'connected') {
                        console.log("connected");
                    }
                };
                this.peerConnection.ontrack = async event => {
                    console.log("on track")
                    console.log(event);
                };
            }
            if(!this.echoConnected){
                this.connectEcho()
            }
        },
        async createPeerOffer(){
            this.peerOffer = await this.peerConnection.createOffer({
                offerToReceiveAudio: true,
                offerToReceiveVideo: true,
            });
            this.peerConnection.setLocalDescription(this.peerOffer);
        },
        sendPeerOffer(){
            console.log("send offer");
            axios.post(window.location.origin + '/send_peer_offer', {
                offer: JSON.stringify(this.peerOffer),
                class: this.class.id
            })
        },
        sendPeerAnswer(){
            console.log("send answer");
            axios.post(window.location.origin + '/send_peer_answer', {
                answer: JSON.stringify(this.peerAnswer),
                class: this.class.id
            })
        },
        sendIceCandidate(){
            console.log("send candidate")
            axios.post(window.location.origin + '/send_ice_candidate', {
                candidate: JSON.stringify(this.currentCandidate),
                class: this.class.id
            })
        },
        addLocalTracksToConnection(){
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
        },
        addRemoteTrack(event){
            console.log(event);
            const remoteStream = MediaStream();
            const remoteVideo = document.querySelector('#remoteVideo');
            remoteVideo.srcObject = remoteStream;  
            remoteStream.addTrack(event.track, remoteStream);
        },
        sendTestWhisper(){
            Echo.channel(`presence-ClassCallChannel.${this.class.id}`)
            .whisper('typing', {
                name: this.user.name
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
            .listenForWhisper('typing', (e) => {
                console.log(e);
            })
            .listen('ClassCallMessageEvent', (e) => {
                console.log(e);
                if (e.offer != null && this.peerOffer == null) {
                    console.log('offer')
                    this.peerOffer = e.offer;
                    const addPeerOffer = async () => {
                        this.peerConnection.setRemoteDescription(new RTCSessionDescription(e.offer));
                        const answer = await this.peerConnection.createAnswer();
                        this.peerAnswer = answer
                        await this.peerConnection.setLocalDescription(answer);
                    }
                    addPeerOffer().then(
                        success => {
                            console.log(this.peerAnswer)
                            window.setTimeout(() => {
                                this.sendPeerAnswer();
                            }, 2000);
                        },
                        error => {
                            console.log(error)
                        }
                    )
                }
                if (e.answer != null) {
                    console.log('answer')
                    this.peerAnswer = e.answer;
                    const addPeerAnswer = async () => {
                        const remoteDesc = new RTCSessionDescription(e.answer);
                        await this.peerConnection.setRemoteDescription(remoteDesc);
                    }
                    addPeerAnswer().then(
                        success => {
                            this.addLocalTracksToConnection()
                        },
                        error => {
                            console.log(error);
                        }
                    )
                }
                if (e.candidate != null){
                    console.log("candidate")
                    const addCandidate = async () => {
                        try {
                            await this.peerConnection.addIceCandidate(e.candidate);
                        } catch (e) {
                            console.error('Error adding received ice candidate', e);
                        }
                    }
                    addCandidate().then(
                        success => console.log("success"),
                        error => console.log(error)
                    )
                }
            })
        }
    },
    created(){
        $(document).ready(() => {
            this.getConnectedDevices('videoinput', cameras => {
                this.cameraList = cameras;
                if(this.cameraList.length > 0) {
                    this.setCameraConstraints(cameras[0].deviceId, 1920, 1080);
                    this.startCamera()
                }
            });
            navigator.mediaDevices.addEventListener('devicechange', event => {
                this.getConnectedDevices('videoinput', cameras => {
                    this.cameraList = cameras
                    if(this.cameraList.length > 0) {
                        this.setCameraConstraints(cameras[0].deviceId, 1920, 1080);
                        this.startCamera()
                    }
                });
            });
        });
    },
    mounted(){
        
    },
    destroyed() {
        Echo.leave('ClassCallChannel.' + this.callId);
    },
}
</script>