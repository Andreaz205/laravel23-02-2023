<template>
    <div class="container-fluid" v-if="product.images && product.images.length">
        <div class="row" v-if="selectedVariantImages">
            <div class="col-sm-3" v-for="image in images()" :key="image.id + ' - '+ image.variant_id">
                <div class="relative">

                    <img :src="image.image_url" alt="">
                    <input v-if="+image.variant_id === +selectedVariantImages.id" type="checkbox" :id="'image-checkbox-' + image.id" class="image-checkbox absolute top-3 left-3 w-10 h-10" checked>
                    <input v-else type="checkbox" :id="'image-checkbox-' + image.id" class="image-checkbox absolute top-3 left-3 w-10 h-10">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ModalImages",
    props: ['product', 'selectedVariantImages'],
    methods: {
        images() {
            let variantImages = JSON.parse(JSON.stringify(this.product?.images?.filter(image => +image.variant_id === +this.selectedVariantImages.id)))
            let otherImages = JSON.parse(JSON.stringify(this.product?.images?.filter(image => !image.variant_id )))
            console.log([...variantImages, ...otherImages])
            return [...variantImages, ...otherImages]
        }
    },
    watch: {
        product: {
            handler: function (value) {
                console.log(value.images)
                this.$page.props.product = value
            },
            deep: true
        }
    }
}
</script>

<style scoped>

</style>
