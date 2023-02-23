<template>
    <div>
        <div v-if="items && items.length" class="wrapper">
            <draggable :list="items" class="banner">
                <div v-for="item in items" class="banner-item bg-gray-200 relative" :key="item.id">
                    <div class="absolute top-0 right-2 cursor-pointer text-2xl z-10" @click="deleteItem(item.id)">
                        &times;
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
export default {
    name: "BannerForm",
    components: {draggable: VueDraggableNext},
    props: ['bannerItems'],
    data () {
        return {
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
                // this.dropzone = null
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
                console.log(this.items.length)
                if (this.items.length === 0) {
                    this.initDropzone()
                    console.log('deleted')
                }
            } catch (e) {
                alert(e)
            }
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
