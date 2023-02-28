<template>
    <div
        class="flex flex-wrap w-full"
         v-if="imgs && imgs.length"
    >
        <draggable :list="imgs" class="draggable-list">

            <div class="p-2" v-for="image in imgs" :key="image.id">
                <div class="relative overflow-hidden border border-black"
                     style="width: 100px; height: 100px">

                    <img :src="image.image_url" class='object-cover w-full h-full' alt="">
                    <button class="absolute top-0 right-0 bg-white opacity-70"
                            @click="$emit('deleteImage', image.id)">
                        <i class="text-black fas fa-times bg-gray-900 p-2 bg-opacity-10"></i>
                    </button>
                </div>
            </div>

        </draggable>
    </div>
    <div v-else class="w-full h-full flex justify-center items-center text-lg">
        Здесь пока нет фотографий!
    </div>
</template>

<script>
import { VueDraggableNext } from "vue-draggable-next"

export default {
    name: "DraggableProductImages",
    props: ['product', 'images'],
    emits: ['deleteImage', 'saveOrder'],
    components: {draggable: VueDraggableNext},
    data () {
        return {
            imgs: this.$props.product.images,
        }
    },
    methods: {
        async saveOrderedImages() {
            let data = this.imgs.map(img => img.id)
            this.$emit('saveOrder', data)
            // try {
            //     if (this.imgs && this.imgs.length) {
            //         let data = this.imgs.map(img => img.id)
            //         await axios.post(`/admin/products/${this.product.id}/images/order`, {order: data})
            //     }
            // } catch (e) {
            //     alert(e)
            // }
        }
    },
    watch: {
        images() {
          this.imgs = this.$props.product.images
            console.log('this.images')
            console.log(this.images)
        }
    },
    mounted() {

    }
}
</script>

<style scoped lang="scss">
    .draggable-list{
        flex-wrap: wrap;
        display: flex;
        justify-content: flex-start;

    }
</style>
