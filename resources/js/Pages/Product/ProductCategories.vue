<template>
    <form id='category-form'>
        <div class="card" style="width: 400px">

            <div class="card-body p-2">
                <table class="table table-hover" style="border: none">
                    <tbody>
                    <tr aria-expanded="true" id="catalog">
                        <td style="border: none">
                            Каталог
                        </td>
                    </tr>
                    <tr class="expandable-body">
                        <td style="border: none">
                            <div class="p-0">
                                <table class="table table-hover" style="border: none">
                                    <tbody v-if="categories && categories.length">
                                        <template v-for="category in categories">
                                            <ProductCategoryRow v-if="category.parent_category_id === null" :category-data="category" :product-data="this.$props.productData"/>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="category-button">
                <button type="submit" class="btn btn-primary bg-blue">
                    Сохранить
                </button>
            </div>
        </div>
    </form>
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
