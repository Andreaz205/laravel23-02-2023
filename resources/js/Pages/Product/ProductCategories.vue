<template>
    <table class="table table-hover" style="border: none">
        <tbody>
        <tr class="expandable-body">
            <td style="border: none">
                <div class="p-0">
                    <table class="table table-hover" style="border: none">
                        <tbody v-if="categories && categories.length">
                            <template v-for="category in categories">
                                <ProductCategoryRow v-if="category.parent_category_id == null" :category-data="category" :product-data="this.$props.productData" :is-switch="true"/>
                            </template>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

</template>

<script>
import ProductCategoryRow from "@/Pages/Product/ProductCategoryRow.vue";

export default {
    name: "ProductCategories",
    components: {
        ProductCategoryRow
    },
    props: [
        'categoriesData',
        'productData',
    ],
    data () {
        return {
            categories: this.$props.categoriesData || null
        }
    },
    methods: {
        // async saveCategories() {
        //     try {
        //
        //     } catch (e) {
        //
        //     }
        // }
    },
    mounted(){
        // this.categories = this.categories.map(category => )
    },
}
</script>

<style scoped>

</style>
