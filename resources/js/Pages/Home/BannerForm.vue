<template>
    <Spinner v-if="isLoading" />
    <!--TODO: Ссылка -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить комплект</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Errors />
                    <div class="container-fluid">
                        <div class="row">
                            <label class="col-4">Ссылка</label>
                            <div class="col-8">
                                <input type="text" class="form-control" v-model="link" >
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="attachLink">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div v-if="items && items.length" class="wrapper">
            <draggable :list="items" class="banner">
                <div v-for="item in items" class="banner-item bg-gray-200 relative" :key="item.id">
                    <div class="absolute top-0 right-2 cursor-pointer text-2xl z-10" @click="deleteItem(item.id)">
                        &times;
                    </div>
                    <div class="absolute left-0 top-0 z-10">
                        <button class="btn btn-warning bg-yellow " @click="selectedItem = item"
                                type="button"
                                data-toggle="modal"
                                data-target="#descriptionModal"
                        >
                            Ссылка
                        </button>
                        <button class="btn btn-danger " @click="removeLink(item)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <template v-if="item.video_url">
                        <video class="w-full h-full" controls>
                            <source :src="item.video_url">
                        </video>
                    </template>
                    <template v-else>
                        <img :src="item.image_url" class="object-cover w-full h-full"/>
                    </template>
                </div>
            </draggable>
            <div class="banner-item border-dashed border-2 flex justify-center items-center cursor-pointer" ref="dropzoneRef">Добавить ещё</div>
        </div>
        <div v-else class="banner-item border-dashed border-2 flex justify-center items-center cursor-pointer" ref="dropzoneRef">Добавить</div>
    </div>
    <button class="btn btn-primary bg-blue-500 mt-4" @click="saveOrder">Сохранить</button>
</template>

<script>
import { VueDraggableNext } from "vue-draggable-next"
import Dropzone from "dropzone";
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
export default {
    name: "BannerForm",
    components: {Spinner, Errors, draggable: VueDraggableNext},
    props: ['bannerItems'],
    data () {
        return {
            isLoading: false,
            errors: null,
            link: '',
            selectedItem: null,
            items: this.bannerItems
        }
    },
    methods: {
        initDropzone() {
            setTimeout(() => {
                this.dropzone = new Dropzone(this.$refs.dropzoneRef, {
                    url: '/admin/products/image/test',
                    multiple: false,
                    autoProcessQueue: false,
                    maxFiles: 1,
                    disablePreviews: true
                })
                this.dropzone.on("addedfile", (file) => this.storeFile(file));
            })
        },
        async attachLink() {
            try {
                if (!this.selectedItem) return alert('Ошибка не выбран элемент')
                this.isLoading = true
                let link = this.link
                let id = this.selectedItem.id
                let data = {link: link}
                let response = await axios.post(`/admin/banner-items/${id}/link`, data)
                let searchedItem = this.items.find(item => item.id === this.selectedItem.id)
                searchedItem.link = link
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        async removeLink(item) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/banner-items/${item.id}/link`)
                item.link = null
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        async saveOrder(e) {
            e.preventDefault()
            try {
                if (this.items.length < 1) return
                let orderArray = []
                let itemsLength = this.items.length
                for (let i = 0; i < itemsLength; i++) {
                    orderArray.push(this.items[i].id)
                }
                let response = await axios.post('/admin/banner-items/order', {order: orderArray})
            } catch (e) {
                alert(e)
            }
        },
        async storeFile(file) {
            try {
                let formData = new FormData()
                formData.append('item', file)
                let res = await axios.post(`/admin/banner-items`, formData)
                let newItem = res.data
                this.items.push(newItem)
                if (this.items.length && this.items.length === 1) this.initDropzone()
            } catch (e) {
                alert(e)
            }
        },
        async deleteItem(id) {
            try {
                await axios.delete(`/admin/banner-items/${id}`)
                this.items = this.items.filter(item => item.id !== id)
                if (this.items.length === 0) {
                    this.initDropzone()
                }
            } catch (e) {
                alert(e)
            }
        }
    },
    watch: {
        selectedItem(val) {
            this.link = val.link
        }
    },
    mounted() {
        this.initDropzone()
    }
}
</script>

<style scoped lang="scss">

    .wrapper{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        .banner {
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            margin-right: 20px;
        }
        .banner-item{
            width: 300px;
            height: 200px;
        }
    }

</style>
