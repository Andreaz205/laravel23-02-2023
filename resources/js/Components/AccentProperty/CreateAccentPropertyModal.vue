<template>
    <Spinner v-if="isLoading"></Spinner>
    <div class="modal fade" id="createAccentPropertyModal" tabindex="-1" role="dialog" aria-labelledby="createAccentPropertyModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить специальное свойство</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div v-if="errors" class="bg-red-100 border-2 border-red-200 mb-4 p-4 relative">
                            <div  class="row" v-for="errorField in errors">
                                <div class="col-12" v-for="error in errorField">
                                    {{error}}
                                </div>
                            </div>

                            <div class="absolute right-0 top-0 bg-gray-400 cursor-pointer w-[20px] h-[20px] flex justify-center items-center" @click="errors = null">
                                <span class="text-black">X</span>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-4">
                                <label>
                                Введите название:

                                </label>
                            </div>
                            <div class="col-8">
                                <input type="text" v-model="title" class="form-control">
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-4">
                                <label>
                                    Введите описание
                                </label>
                            </div>
                            <div class="col-8">
                                <textarea v-model="description" class="form-control"/>
                            </div>
                        </div>

                        <div :class="['row mt-4', file ? 'hidden' : '']">
                            <div class="col-12">
                                <div class="text-center text-lg">
                                    Добавить фото
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div
                                    ref="dropzone"
                                    class="w-full h-[200px] bg-gray-500 border-2 border-dashed border-black flex justify-center items-center cursor-pointer"
                                >
                                    <div>
                                        Добавить фото или видео
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="file" class="row my-4">
                            <div class="col-4">
                                <label>
                                    Медиа:
                                </label>
                            </div>
                            <div class="col-8 h-[400px] bg-gray-50 relative">
                                <div v-if="!dataUrl" class="flex justify-center items-center w-full h-full">
                                    К сожалению не удалось загрузить медиа
                                </div>
                                <img v-if="uploadMediaType === 'image'" :src="dataUrl" alt="" class="object-contain w-full h-full">
                                <video autoplay loop muted  v-if="uploadMediaType === 'video'" :src="dataUrl" class="w-full h-full"></video>
                                <div class="absolute top-0 right-5">
                                    <button class="btn btn-default">
                                        <label for="change-media" class="mb-0 cursor-pointer">Заменить</label>
                                        <input type="file" @change="setNewFile" class="hidden" id="change-media">
                                    </button>
                                    <button class="btn btn-danger ml-4" @click="clearPhoto">
                                        Удалить
                                    </button>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">
                        Закрыть
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="submit"
                    >
                        Создать
                    </button>
                    <!--                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить-->
                    <!--</button>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Dropzone from "dropzone";
import Spinner from "@/Components/Spinner.vue";

export default {
    components: {Spinner},
    name: "CreateAccentPropertyModal",
    props: [],
    emits: ['created'],
    data () {
        return {
            isLoading: false,
            errors: null,
            title: '',
            description: '',
            dropzone: null,
            file: null,
            dataUrl: null,
            uploadMediaType: 'image',
            // fileToChange: null,
        }
    },
    methods: {
        clearPhoto() {
            this.file = null
            this.dataUrl = null
        },
        setNewFile(event) {
            let file = event.target.files[0]
            const reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = (event) => {
                let dataUrl = event.target.result
                this.dataUrl = dataUrl
            };
        },
        async submit() {
            try {
                this.isLoading = true
                let formData  = new FormData()
                formData.append('description', this.description)
                formData.append('title', this.title)
                formData.append('file', this.file)

                let response = await axios.post(`/admin/accent-properties`, formData)
                this.errors = null
                this.$emit('created', response.data)
                this.title = null
                this.description = null
                this.file = null
                this.dataUrl = null
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) {
                    return this.errors = e.response.data.errors
                }
                alert(e?.message ?? e)
            }
        },
    },


    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: '/',
            autoProcessQueue: false,
            maxFiles: 10,
            disablePreviews: true
        })
        this.dropzone.on("addedfile", async (file) => {
            this.file = file
            if (file.type.includes('video')) {
                this.uploadMediaType = 'video'
            } else {
                this.uploadMediaType = 'image'
            }
            const reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = (event) => {
                let dataUrl = event.target.result
                this.dataUrl = dataUrl
            };
        });
    }
}
</script>

<style scoped>

</style>
