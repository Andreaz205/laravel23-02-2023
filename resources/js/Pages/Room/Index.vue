<template>
    <AuthenticatedLayout>
        <div class="d-flex justify-center items-center m-2">
            <button class="btn btn-primary" @click="togglePopup(true)">
                Добавить комнату
            </button>
        </div>
        <div v-if="isPopupOpen">
            <div class="fixed top-0 left-0 w-full h-full bg-black opacity-25"></div>
            <div class="fixed w-full h-full top-0 left-0 d-flex justify-center items-center z-10" @click="clickCallback">
                <div class='w-96 h-96 bg-white' ref="popup">
                    <div class="d-flex justify-center items-center m-2">Введите название</div>
                    <div class="d-flex justify-center items-center m-2">
                        <input type="text" v-model="roomName" class="border border-black w-full"
                               placeholder="Введите название комнаты">
                    </div>
                    <div class="d-flex justify-center items-center m-2">Прикрепите фотографию для превью</div>
                    <div class="d-flex justify-center items-center m-2">
                        <button class="btn btn-default rounded-full m-2" @click="handleFileClick">
                            Выберите изображение
                        </button>
                        <input type="file" @change="setImage" hidden ref="file">
                    </div>
                    <div class='d-flex justify-center items-center m-2'>
                        <div v-if="image" class="relative w-36 h-36">
                            <img :src="image" alt="" class="absolute object-contain">
                        </div>
                    </div>

                    <div class="d-flex justify-center items-center m-2">
                        <button class="btn btn-primary rounded-full m-2" @click="storeRoom">
                            Создать
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isEditPopupOpen">
            <div class="fixed top-0 left-0 w-full h-full bg-black opacity-25"></div>
            <div class="fixed w-full h-full top-0 left-0 d-flex justify-center items-center z-50 popup-wrapper"
                 @click="editPopupClickCallback">
                <div class='popup bg-white' ref="editPopup">
                    <div class="d-flex justify-center items-center m-2">Выберите категории</div>

                    <div v-if="isCategoriesLoading">Loading...</div>

                    <div v-if="fetchedCategories && fetchedCategories.length" class="m-5">
                        <div v-for="category in fetchedCategories" class="border-b border-b-black">
                            <div class="d-flex justify-between items-center">
                                <div>
                                    {{ category.name }}
                                </div>
                                <div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input cursor-pointer"
                                                :id="'customSwitch' + category.id"
                                                @change="toggleBind(category)"
                                                :checked="category.is_exists"
                                            >
                                            <label class="custom-control-label cursor-pointer"
                                                   :for="'customSwitch' + category.id">Добавить</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="file" ref="updateImage" hidden @change="fetchUpdateImage">
        <div v-if="rooms">
            <div v-for="room in rooms" class="border-b border-b-gray-400">
                <div class='d-flex justify-center items-center m-2 text-3xl font-bold'>{{ room.title }}</div>
                <div class='d-flex justify-center items-center m-2 '>
                    <button class="btn btn-info rounded-full m-2" @click="edit(room)">
                        Редактировать
                    </button>
                    <button class="btn btn-danger rounded-full m-2"
                            @click="deleteRoom(room)">
                        Удалить
                    </button>
                </div>
                <div class='d-flex justify-center items-center m-2'>
                    <div class="relative w-96 h-96">
                        <img :src="room.image_url || '/storage/images/no-image.jpg'" :alt="room.title"
                             class="absolute object-contain top-0 left-0 w-full h-full">
                        <button class="btn btn-danger absolute top-5 right-5 rounded-full w-28 p-0.5 text-sm"
                                @click="updateImage('delete', room)">
                            Удалить фото
                        </button>
                        <button class="btn btn-default absolute top-0 right-0"
                                @click="updateImage('change', room)">
                            Заменить фото
                        </button>
                    </div>
                </div>
                <div v-if="room.categories && room.categories.length">
                    <div v-for="category in room.categories">
                        {{ category.name }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    name: "Rooms",
    components: {AuthenticatedLayout},
    props: ['data'],
    data() {
        return {
            isCategoriesLoading: false,
            selectedRoom: null,
            isPopupOpen: false,
            isEditPopupOpen: false,
            rooms: this.$props.data,
            roomName: '',
            image: null,
            imageData: null,
            fetchedCategories: null
        }
    },
    methods: {
        updateImage(method, room) {
            if (method === 'change') {
                this.$refs.updateImage.roomId = room.id
                this.$refs.updateImage.click()
            } else if (method === 'delete') {
                this.deleteImage(room)
            }
        },
        handleFileClick() {
            this.$refs.file.click()
        },
        togglePopup(isOpen) {
            this.isPopupOpen = isOpen
        },
        toggleEditPopup(isOpen) {
            this.isEditPopupOpen = isOpen
        },
        clickCallback(event) {
            let popup = this.$refs.popup
            let target = event.target
            if (!popup.contains(target)) {
                this.togglePopup(false)
            }
        },
        editPopupClickCallback(event) {
            let popup = this.$refs.editPopup
            let target = event.target
            if (!popup.contains(target)) {
                this.fetchedCategories = null
                this.toggleEditPopup(false)
            }
        },
        setImage(event) {
            let file = event.target.files[0]
            let reader = new FileReader()
            reader.readAsDataURL(file);
            reader.onload = (e) => {
                this.image = e.target.result
            }
            let data = new FormData
            data.append('image', file)
            this.imageData = data
        },
        async storeRoom() {
            try {
                let data = this.imageData
                data.append('title', this.roomName)
                let response = await axios.post('/admin/rooms', data)
                let newRoom = response.data.data
                if (this.rooms) this.rooms.unshift(newRoom)
            } catch (e) {
                alert(e)
            }
        },
        edit(room) {
            this.fetchRoomCategories(room.id)
            this.toggleEditPopup(true)
            this.selectedRoom = room
        },
        async fetchRoomCategories(roomId) {
            try {
                this.isCategoriesLoading = true
                let response = await axios.get(`/admin/rooms/${roomId}/filtered-categories`)
                this.isCategoriesLoading = false
                this.fetchedCategories = response.data.data
            } catch (e) {
                this.isCategoriesLoading = false
                alert(e)
            }

        },
        async toggleBind(category) {
            try {
                let {data} = await axios.get(`/admin/rooms/${this.selectedRoom.id}/categories/${category.id}/toggle`)
                let room = this.rooms.find(room => room.id === this.selectedRoom.id)
                if (data.status === 'delete') {
                    room.categories = room.categories.filter(el => el.id !== category.id)
                } else if (data.status === 'create') {
                    if (room.categories) {
                        room.categories.push(category)
                    } else {
                        room.categories = [category]
                    }
                }

            } catch (e) {
                alert(e)
            }
        },
        async fetchUpdateImage(e) {
            try {
                let file = e.target.files[0]
                let formData = new FormData
                formData.append('image', file)
                let roomId = this.$refs.updateImage.roomId
                let {data} = await axios.post(`/admin/rooms/${roomId}/images`, formData)
                let room = this.rooms.find(room => room.id === roomId)
                room.image_url = data.data.image_url
            } catch (e) {
                alert(e)
            }
        },
        async deleteImage(room) {
            try {
                await axios.delete(`/admin/rooms/${room.id}/images`)
                let r = this.rooms.find(el => el.id === room.id)
                r.image_url = null
            } catch (e) {
                alert(e)
            }
        },
        async deleteRoom(room) {
            try {
                await axios.delete(`/admin/rooms/${room.id}`)
                this.rooms = this.rooms.filter(el => el.id !== room.id)
            } catch (e) {
                alert(e)
            }
        },
    },
    mounted() {
        console.log(this.$props.data)
    }
}
</script>

<style scoped>
@media (max-width: 1400px) {
    .popup-wrapper {
        left: 250px;
    }
}

.popup {

    overflow-y: auto;
    width: 50%;
    height: 70%;
}
</style>
