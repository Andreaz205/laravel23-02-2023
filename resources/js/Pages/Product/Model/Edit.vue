<template>
    <ProductModels :models-prop="modelsData" :product="product"/>
</template>

<script>
import ProductModels from "@/Components/ProductModel/ProductModels.vue";
export default {
    name: "Edit",
    props: ['models', 'product'],
    components: {ProductModels},
    data () {
        return {
            modelsData: this.models
        }
    }
}
</script>

<style scoped>

</style>
