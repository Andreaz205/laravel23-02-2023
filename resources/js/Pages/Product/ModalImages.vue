<template>
    <div class="container-fluid" v-if="product.images && product.images.length">
        {{product.images.map(image => image.variant_id)}}
        <div class="row" v-if="selectedVariantImages">
            <div class="col-sm-3" v-for="image in product.images" :key="image.id">
                <div class="relative"  v-if="!image.variant_id || image.variant_id === selectedVariantImages.id">

                    <img :src="image.image_url" alt="">
                    <input v-if="image.variant_id && image.variant_id === selectedVariantImages.id" type="checkbox" :id="'image-checkbox-' + image.id" class="image-checkbox absolute top-3 left-3 w-10 h-10" checked>
                    <input v-if="image.variant_id && image.variant_id !== selectedVariantImages.id" type="checkbox" :id="'image-checkbox-' + image.id" class="image-checkbox absolute top-3 left-3 w-10 h-10">
                    <input v-if="!image.variant_id" type="checkbox" :id="'image-checkbox-' + image.id" class="image-checkbox absolute top-3 left-3 w-10 h-10">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ModalImages",
    props: ['product', 'selectedVariantImages']
}
</script>

<style scoped>

</style>
