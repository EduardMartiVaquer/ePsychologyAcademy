<template id="class-event-template">
<div class="video-container" style="z-index: 1000; position: fixed; overflow: hidden; top: 0; left: 0">
    
    <!-- Small self -->
    <div id="video-out-fixed"></div>
    <div id="video-out-shower" style="z-index: 100; background-color: transparent !important;" @click="videoOutVisible = !videoOutVisible">
        <p class="h3 m-0 pointer text-white">
            <span v-if="videoOutVisible">
                <i class="fa fa-eye-slash"></i>
            </span>
            <span v-if="!videoOutVisible">
                <i class="fa fa-eye"></i>
            </span>
        </p>
    </div>
    <div id="video-out-screen" :style="size == 0 ? 'opacity: 1' : 'opacity: 0; display: none'"></div>

    <!-- Pins % volume -->
    <div id="video-pins-container" class="d-flex flex-wrap" style="align-content: start">
        <div v-for="(subscriber, index) in subscribers" :key="index" :id="'pin-' + index" class="video-pin">
            <canvas :id="'canvas-' + index" style="
                width: 100px;
                top: 0;
                height: 100px;
                left: 57px;
                position: fixed;
            "></canvas>
            <div class="video-pin-inner">
                <div class="video-pin-box">
                    <img v-if="isPinned(index)" :src=" url + '/images/unpin.svg'" class="pointer mr-5" @click="unpinScreen(index)" height="20px"/>
                    <img v-if="!isPinned(index)" :src=" url + '/images/pin.svg'" class="pointer mr-5" @click="pinScreen(index)" height="20px"/>
                    <img :src="url + (subscribers[index].getAudioVolume() == 0 ? '/images/volume-white.svg' : '/images/volume-none-white.svg')" class="pointer" @click="mute(index)" height="20px"/>
                </div>
            </div>
        </div>
    </div>

    <!-- Streams -->
    <div id="video-in" class="d-flex flex-wrap" style="align-content: start" :style="size == 1 ? 'width: 300px !important; height: 168.75px !important' : ''"></div>

    <!-- Main Buttons -->
    <div class="buttons-holder">
        <button class="call-button mr-2" :class="OTcallOptions.publishVideo ? 'btn-success' : 'btn-secondary'" @click="toggleVideo()" :style="size == 1 ? 'width: 30px; height: 30px' : ''" data-toggle="tooltip" data-placement="top" title="Activar/desactivar video">
            <img :src="url + (OTcallOptions.publishVideo ? '/images/camera.svg' : '/images/camera-none.svg')" style="height: 30%; width: inherit">
        </button>
        <button class="call-button mr-2" :class="OTcallOptions.publishAudio ? 'btn-success' : 'btn-secondary'" @click="toggleAudio()" :style="size == 1 ? 'width: 30px; height: 30px' : ''" data-toggle="tooltip" data-placement="top" title="Activar/desactivar audio">
            <img :src="url + (OTcallOptions.publishAudio ? '/images/audio.svg' : '/images/audio-none.svg')" style="height: 30%; width: inherit">
        </button>
        <button class="call-button btn-danger mr-2" @click="endCall()" :style="size == 1 ? 'width: 30px; height: 30px' : ''" data-toggle="tooltip" data-placement="top" title="Terminar la llamada">
            <img :src="url + '/images/phone.svg'" style="transform: rotate(135deg); height: 30%; width: inherit">
        </button>
        <button class="call-button btn-secondary" v-if="!isSharingScreen" @click="startSharingScreen()" :style="size == 1 ? 'display: none !important' : ''" data-toggle="tooltip" data-placement="top" title="Empezar/Parar compartir pantalla">
            <img :src="url + '/images/share_screen.svg'" style="height: 30%; width: inherit">
        </button>
        <button class="call-button btn-secondary" v-if="isSharingScreen" @click="stopSharingScreen()" :style="size == 1 ? 'display: none !important' : ''" data-toggle="tooltip" data-placement="top" title="Empezar/Parar compartir pantalla">
            <img :src="url + '/images/cancel_share_screen.svg'" style="height: 30%; width: inherit">
        </button>

        <!-- Timmer -->
        <button id="timmer-button" class="call-button btn-secondary ml-4" @click="toggleTime()" :style="size == 1 ? 'display: none !important' : ''" data-toggle="tooltip" data-placement="top" title="Tiempo de la sesión">
            <img :src="url + (timeVisible ? '/images/no-clock.svg' : '/images/clock.svg')" style="height: 40%; width: inherit">
        </button>
        <p id="time-ellapsed-p" class="h5 m-0 text-white ml-2" :class="timeVisible ? '' : 'd-none'" :style="size == 1 ? 'display: none !important' : ''">{{ currentTime }}</p>
    </div>
    <div style="position: absolute; right: 20px; bottom: 20px; z-index: 21" class="d-flex align-items-center" :style="size == 1 ? 'display: none !important' : ''">
        <button class="call-button btn-secondary ml-4" @click="openOTOptions()">
            <i class="fa fa-cog"></i>
        </button>
    </div>
</div>
</template>

<script>
const blazeface = require('@tensorflow-models/blazeface');
import * as tf from '@tensorflow/tfjs-core';
import * as tfjsWasm from '@tensorflow/tfjs-backend-wasm';
import * as tfjsWebgl from '@tensorflow/tfjs-backend-webgl';
tfjsWasm.setWasmPath('https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm@latest/dist/tfjs-backend-wasm.wasm');

const state = {
    backend: 'webgl'
};

export default {
    template: "#class-event-template",
    data(){
        return {
            loading: true,
            error: false,
            currentTime: '',
            timeVisible: false,
            start: null,
            timeout: null,
            size: 0,
            videoOutVisible: true,
            table: 0,
            connectedMembers: [],
            endCallTimer: null,
            canShareScreen: false,
            isSharingScreen: false,
            shouldPin: null,
            pinnedScreens: [],
            sidebarOpened: false,
            speechRecognizer: null,
            recognizerLang: "ca-ES",
            canEndRecognizer: false,
            transcript: [],
            transcriptSenderTimeout: null,
            faceModel: null
        }
    },
    computed: {
        currentUser(){
            return this.$store.state.currentUser.user;
        },
        currentClass(){
            return this.$store.state.currentClass.class;
        },
        currentClassMembers(){
            return this.currentClass.users;
        },
        iamAdmin(){
            return this.getTypeFromId(this.currentUser.id) == "admin";
        },
        iamStudent(){
            return this.getTypeFromId(this.currentUser.id) == "student";
        },
        iamTeacher(){
            return this.getTypeFromId(this.currentUser.id) == "teacher";
        },
        iamPatient(){
            return this.getTypeFromId(this.currentUser.id) == "patient";
        },
        iamPsychologist(){
            return this.getTypeFromId(this.currentUser.id) == "psychologist";
        },
        OTcallOptions(){
            return app.OTcallOptions
        },
        streams(){
            return app.OTstreams;
        },
        subscribers(){
            return this.table == 0 ? app.OTsubscribers : app.OTsubscribers;
        },
    },
    methods: {
        getClassEvent(){
            axios.post(window.location.origin + '/get_class_event', {
                classEvent: this.$route.params.id
            })
            .then((response) => {
                if(response.data.status == "OK"){
                    this.$store.state.currentClass.class = response.data.class;
                } else {
                    this.error = true;
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [this.trans.messages.error.error_1]
                    });
                }
                this.loading = false
                this.startOTSession();
            })
            .catch((error) => {
                this.error = true
                this.loading = false
                VueEvent.$emit('alert', {
                    type: 'danger',
                    messages: [this.trans.messages.error.error_1]
                });
            })
        },
        startOTSession(){
            if(!this.calling){
                app.OTcallOptions.publishVideo = true;
                axios.post(window.location.origin + '/start_ot_session', {
                    classEvent: this.$route.params.id,
                    video: true
                })
                .then((response) => {
                    app.OTsessionId = response.data.session_id;
                    app.OTsessionToken = response.data.token;
                    app.OTsession = null;
                    this.initializeSession();
                })
                .catch((error) => {
                    VueEvent.$emit('alert', {
                        type: 'danger',
                        messages: [this.trans.messages.error.error_1]
                    });
                })
            }
        },
        initializeSession(){
            if (OT.checkSystemRequirements() == 1) {
                app.OTsession = OT.initSession(app.OTapiKey, app.OTsessionId);
                app.OTsession.on('streamCreated', (event) => {
                    var coincidence = app.OTstreams.some(stream => {
                        return stream.id == event.stream.id
                    })
                    if(!coincidence){
                        var streamType = this.getTypeFromId(parseInt(event.stream.name.replace('user-camera-', '')));
                        if(this.iamPatient && (streamType != "psychologist")){
                            
                        } else {
                            app.OTstreams.push(_.clone(event));
                            this.subscribeToStream(app.OTstreams.length - 1);
                        }
                    }
                });
                app.OTsession.on('streamDestroyed', (event) => {
                    for(var s in app.OTstreams){
                        if(app.OTstreams[s].stream.id == event.stream.id){
                            app.OTstreams.splice(s, 1);
                            app.OTsubscribers.splice(s, 1);
                            break;
                        }
                    }
                    window.setTimeout(() => {
                        this.recomputeScreen();
                    }, 100)
                })
                app.OTsession.on("sessionDisconnected", (event) => {
                    this.endCall();
                });
                app.OTsession.on("signal", (event) => {console.log(event)})
                if(app.OTpublisher == null){
                    this.createPublisher();
                } else {
                    waitForElement('#video-out-fixed', () => {
                        document.getElementById("video-out-fixed").appendChild(app.OTpublisher.element);
                        this.connectToExistingSession();
                    })
                }
            } else {
                alert('browser not supported');
                this.endCall();
            }
        },
        createPublisher(){
            app.OTpublisher = OT.initPublisher('video-out-fixed', {
                width: '100%',
                height: '100%',
                publishAudio: app.OTcallOptions.publishAudio,
                publishVideo: app.OTcallOptions.publishVideo,
                resolution: '1280x960',
                insertMode: 'append',
                fitMode: 'contain',
                name: ('user-camera-' + this.currentUser.id),
                style: {
                    archiveStatusDisplayMode: 'off',
                    buttonDisplayMode: 'off',
                    nameDisplayMode: "off",
                }
            }, (error) => {
                if(error){
                    console.log(error);
                    this.endCall();
                } else {
                    document.getElementById("video-out-fixed").appendChild(app.OTpublisher.element);
                    this.connectToExistingSession();
                }
            });
            app.OTpublisher.on({
                accessAllowed: (event) => {

                },
                accessDenied: (event) => {
                    this.endCall();
                }
            });
        },
        connectToExistingSession(){
            app.OTsession.connect(app.OTsessionToken, (error) => {
                if (error) {
                    console.log(error);
                    this.endCall();
                } else {
                    if (app.OTsession.capabilities.publish == 1) {
                        app.OTsession.publish(app.OTpublisher, (error) => {
                            if(error){
                                console.log(error);
                                this.endCall();
                            }
                        });
                    } else {
                        this.endCall();
                    }
                }
            });
        },
        subscribeToStream(index){
            var subscriber = app.OTsession.subscribe(app.OTstreams[index].stream, 'video-in', {
                insertMode: 'append',
                publishAudio: app.OTcallOptions.publishAudio,
                publishVideo: app.OTcallOptions.publishVideo,
                width: '100%',
                height: '100%',
                audioVolume: 100,
                fitMode: 'contain',
                nameDisplayMode: "off",
                style: {
                    audioBlockedDisplayMode: 'off',
                    buttonDisplayMode: 'off',
                    nameDisplayMode: "off",
                }
            }, (error) => {
                if(error){
                    console.log(error);
                } else {
                    if(this.start == null){
                        this.start = moment();
                    }
                    var streamType = this.getTypeFromId(parseInt(app.OTstreams[index].stream.name.replace('user-camera-', '')));
                    if(app.OTstreams[index].stream.name.includes('screen') || (streamType == "patient" && !iamPsychologist) || (streamType == "psychologist" && !iamPatient)){
                        this.shouldPin = index
                    }
                    window.setTimeout(() => {
                        this.recomputeScreen();
                    }, 100)
                }
            });
            app.OTsubscribers.push(subscriber);
        },
        recomputeScreen(){
            var index = 0;
            var matrix = [];
            var children = $('#video-in').children();

            var nOfRows = children.length < 3 ? 1 : children.length < 9 ? 2 : children.length.length < 10 ? 3 : children.length < 17 ? 4 : (Math.round(children.length / 10) * 10)
            var maxRowElements = children.length < 5 ? 2 : children.length < 7 ? 3 : children.length < 17 ? 4 : 5;
            
            var elementsPushed = 0;
            for(var r = 0; r < nOfRows; r++){
                matrix.push([]);
                for(var e = 0; e < maxRowElements; e++){
                    if(elementsPushed < children.length){
                        matrix[r].push(index);
                        index += 1;
                        elementsPushed += 1;
                    } else {
                        break;
                    }
                }
                if(elementsPushed >= children.length){
                    break;
                }
            }
            
            var height = Math.round(100 / nOfRows)
            var elementIndex = 0;
            for(var row in matrix){
                var width = Math.round(100 / matrix[row].length);
                for(var element in matrix[row]){
                    var id = children[elementIndex].id;
                    $('#' + id).css({"width": (width + "%")});
                    $('#' + id).css({"height": (height + "%")});
                    $('#' + id).css({"position": "relative"});
                    $('#' + id).css({"z-index": "1"});
                    
                    if($('#pin-' + elementIndex).length > 0){
                        $('#pin-' + elementIndex).css({"width": (width + "%")});
                        $('#pin-' + elementIndex).css({"height": (height + "%")});
                    }
                    
                    elementIndex += 1;
                }
            }

            if(this.shouldPin != null){
                var i = this.shouldPin;
                this.shouldPin = null;
                this.pinnedScreens.push(i);
                this.pinnedScreens = [...new Set(this.pinnedScreens)]
                this.pinScreen(i);
            } else if(this.pinnedScreens.length > 0) {
                this.pinnedScreens = [...new Set(this.pinnedScreens)]
                this.pinScreen(this.pinnedScreens[0]);
            } else {
                $('#video-in').removeClass('flex-column');
            }

            this.blurrSubscribers()
        },
        pinScreen(index){
            this.pinnedScreens.push(index);
            this.pinnedScreens = [...new Set(this.pinnedScreens)];

            //Streams
            var pinnedStreams = []
            var unpinnedStreams = [];
            app.OTstreams.forEach((stream, index) => {
                if(this.pinnedScreens.some(s => {return s == index})){
                    pinnedStreams.push(stream);
                } else {
                    unpinnedStreams.push(stream)
                }
            })

            //Reset streams list
            app.OTstreams = [];
            var newSubscribers = [];
            this.pinnedScreens = [];
            pinnedStreams.forEach((stream, index) => {
                app.OTstreams.push(stream);
                this.pinnedScreens.push(index);

                var subscriber = app.OTsubscribers.find(subscriber => {
                    return subscriber.streamId == stream.stream.id;
                })
                newSubscribers.push(subscriber)
            });
            this.pinnedScreens = [...new Set(this.pinnedScreens)]
            unpinnedStreams.forEach(stream => {
                app.OTstreams.push(stream);
                
                var subscriber = app.OTsubscribers.find(subscriber => {
                    return subscriber.streamId == stream.stream.id;
                })
                newSubscribers.push(subscriber)
            })
            
            app.OTsubscribers = newSubscribers;



            var children = $('#video-in').children();
            var pinnedChildren = [];
            var unpinnedChildren = [];
            //Position pinned streams
            
            app.OTstreams.forEach((stream, index) => {
                var subscriber = app.OTsubscribers[index];
                if(subscriber != null){
                    if(index <= this.pinnedScreens[this.pinnedScreens.length - 1]){
                        $('#' + subscriber.id).css({"width": `${80 / this.pinnedScreens.length}%`});
                        $('#' + subscriber.id).css({"height": "100%"});
                        pinnedChildren.push($('#' + subscriber.id));
                        $('#pin-' + index).css({"width": `${80 / this.pinnedScreens.length}%`});
                        $('#pin-' + index).css({"height": "100%"});
                    } else {
                        $('#' + subscriber.id).css({"width": "20%"});
                        $('#' + subscriber.id).css({"height": `${100 / (children.length - this.pinnedScreens.length)}%`});
                        unpinnedChildren.push($('#' + subscriber.id));
                        $('#pin-' + index).css({"width": "20%"});
                        $('#pin-' + index).css({"height": `${100 / (children.length - this.pinnedScreens.length)}%`});
                    }
                }
            })

            $('#video-in').empty();
            pinnedChildren.forEach(p => {
                $('#video-in').append(p)
            });
            unpinnedChildren.forEach(p => {
                $('#video-in').append(p)
            });

            window.setTimeout(() => {
                $('#video-in').addClass('flex-column');
            }, 300)
        },
        unpinScreen(index){
            var index = this.pinnedScreens.findIndex(p => {
                return p == index;
            })
            if(index !== -1){
                this.pinnedScreens.splice(index, 1);
            }
            this.recomputeScreen();
        },
        isPinned(index){
            return this.pinnedScreens.some(p => {return p == index});
        },
        mute(index){
            if(app.OTsubscribers[index] != undefined){
                if(app.OTsubscribers[index].getAudioVolume() == 0){
                        app.OTsubscribers[index].setAudioVolume(100);
                    } else {
                        app.OTsubscribers[index].setAudioVolume(0);
                    }
            }
            this.table = this.table == 0 ? 1 : 0;
        },
        toggleAudio(){
            app.OTcallOptions.publishAudio = !app.OTcallOptions.publishAudio
            app.OTpublisher.publishAudio(app.OTcallOptions.publishAudio)
        },
        toggleVideo(){
            app.OTcallOptions.publishVideo = !app.OTcallOptions.publishVideo
            app.OTpublisher.publishVideo(app.OTcallOptions.publishVideo)
        },
        openOTOptions(){
            VueEvent.$emit('changeModal', 'ot-options-modal');
        },
        endCall(){
            app._router.back()
        },
        toggleTime(){
            this.timeVisible = !this.timeVisible;
            if(this.timeout != null){
                clearTimeout(this.timeout);
            }
            if(this.timeVisible){
                this.getTimeEllapsed();
            }
        },
        getTimeEllapsed(){
            this.currentTime = this.timeEllapsed();
            if(this.timeVisible){
                clearTimeout(this.timeout);
                this.timeout = window.setTimeout(() => {
                    this.getTimeEllapsed();
                }, 1000)
            }
        },
        timeEllapsed(){
            if(this.start == null){
                return this.currentTime;
            } else {
                var diff = moment().diff(this.start);
                return (moment.duration(diff).hours() < 10 ? ('0' + moment.duration(diff).hours()) : moment.duration(diff).hours()) + ':' + (moment.duration(diff).minutes() < 10 ? ('0' + moment.duration(diff).minutes()) : moment.duration(diff).minutes()) + ':' + (moment.duration(diff).seconds() < 10 ? ('0' + moment.duration(diff).seconds()) : moment.duration(diff).seconds());
            }
        },
        checkSharingCapability(){
            OT.checkScreenSharingCapability((response) => {
                if(!response.supported || response.extensionRegistered === false) {
                    this.canShareScreen = false;
                } else {
                    this.canShareScreen = true;
                }
            });
        },
        startSharingScreen(){
            app.OTscreenPublisher = OT.initPublisher('video-out-screen', {
                    insertMode: 'append',
                    width: '100%',
                    height: '100%',
                    fitMode: 'contain',
                    nameDisplayMode: "off",
                    maxResolution: { width: 1920, height: 1080 },
                    videoSource: 'screen',
                    name: ('user-screen-' + this.currentUser.id),
                    nameDisplayMode: "off",
                    style: {
                        archiveStatusDisplayMode: 'off',
                        buttonDisplayMode: 'off',
                        nameDisplayMode: "off",
                    }
                },
                (error) => {
                    if (error) {
                        console.log(error);
                        alert('Permiso denegado');
                    } else {
                        this.isSharingScreen = true;
                        app.OTsession.publish(app.OTscreenPublisher, (error) => {
                            if (error) {
                                console.log(error);
                            } else {
                                this.recomputeScreen();
                            }
                        });
                    }
                }
            );
        },
        stopSharingScreen(){
            if(app.OTscreenPublisher != null){
                app.OTsession.unpublish(app.OTscreenPublisher);
                app.OTscreenPublisher.destroy();
                app.OTscreenPublisher = null;
                this.isSharingScreen = false;
                this.recomputeScreen();
            }
        },
        getTypeFromId(id){
            var types = ["admin", "student", "teacher", "patient", "psychologist"];
            var member = this.currentClassMembers.find(m => {
                return m.id == id;
            })
            if(typeof member != "undefined"){
                return types[member.class_user.type]
            }
            return null;
        },
        blurrSubscribers(){
            var indexes = []
            app.OTstreams.forEach((stream, index) => {
                var id = stream.stream.name.replace('user-camera-', '');
                var type = this.getTypeFromId(id);
                if(type == "patient"){
                    indexes.push(index)
                }
            });
            indexes.forEach(index => {
                if(index !== -1){
                    var video = $(`#${app.OTsubscribers[index].id}`).find('video')
                    this.renderFaceBlurr(index, video);
                }
            });
        },
        async renderFaceBlurr(index, video) {
            if(this.faceModel == null){
                this.faceModel = await blazeface.load();
            }
            var canvas = document.getElementById('canvas-' + index);
            $(`#canvas-${index}`).css({'width': `${video.width()}px`, 'height': `${video.height()}px`, 'top': `${$(`#${app.OTsubscribers[index].id}`).position().top}px`, 'left': `${$(`#${app.OTsubscribers[index].id}`).position().left}px`});
            canvas.width = video.width();
            canvas.height = video.height();
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = "rgba(255, 0, 0, 0.5)";
            // Pass in an image or video to the model. The model returns an array of
            // bounding boxes, probabilities, and landmarks, one for each detected face.

            const returnTensors = false; // Pass in `true` to get tensors back, rather than values.
            const flipHorizontal = false;
            const annotateBoxes = false;
            const predictions = await this.faceModel.estimateFaces(video[0], returnTensors, flipHorizontal, annotateBoxes);

            if (predictions.length > 0) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let i = 0; i < predictions.length; i++) {
                    
                    console.log(predictions[i].probability)
                    const start = predictions[i].topLeft;
                    const end = predictions[i].bottomRight;
                    var blurredRect = {
                        x: start,
                        y: end,
                        width: end[0] - start[0] - 50,
                        height: end[0] - start[0] - 20,
                        spread: 15
                    };
                    var blurryBox = document.getElementById(`blurry-box-${index}`)
                    var style = `position: fixed; top: ${start[1] - 100}px; left:${start[0] - 70}px; width: ${blurredRect.width}px; height: ${blurredRect.height}px; backdrop-filter: blur(${blurredRect.spread}px); border-radius: ${blurredRect.width / 2}px`
                    if(blurryBox == null){
                        blurryBox = document.createElement('div');
                        blurryBox.id = `blurry-box-${index}`;
                        blurryBox.className = "blurry-box";
                        document.getElementById(`pin-${index}`).append(blurryBox);
                    }
                    blurryBox.style = style;
                }
            }

            window.setTimeout(() => {
                this.renderFaceBlurr(index, video)
            }, 5)
        }
    },
    created(){
        var loadTf = async() => {
            await tf.setBackend(state.backend);
        }
        loadTf();
        $(document).ready(() => {
            this.$store.state.currentClass.id = this.$route.id;
            this.getClassEvent();

            // Load Face model
            
        });
    }
}
</script>