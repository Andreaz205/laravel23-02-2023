<template>
    <tr data-widget="expandable-table" aria-expanded="false" class="clickable">
        <td style="border: none; position: relative">
            <div class="d-flex justify-start">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input"
                               :id="'customSwitchCategory' + category.id" :checked="category.is_checked"
                               @change="toggleChecked"
                        >
                        <label class="custom-control-label" :for="'customSwitchCategory' + category.id"></label>
                    </div>
                </div>
                <div>
                    <i class="expandable-table-caret fas fa-caret-right fa-fw" v-if="category.child_categories && category.child_categories.length"></i>
                    {{ category.name }}
                </div>

            </div>

        </td>
    </tr>


    <tr class="expandable-body d-none" v-if="category.child_categories && category.child_categories.length">
        <td style="border: none">
            <div class="p-0" style="display: none; border: none" >
                <table class="table table-hover" style="border: none">
                    <tbody v-if="category.child_categories && category.child_categories.length">
                        <template v-for="childCategory in category.child_categories">
                            <ProductCategoryRow :category-data="childCategory" :product-data="this.$props.productData" />
                        </template>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>

    <tr class="expandable-body d-none" v-else>
        <td style="border: none">
            <div class="p-0" style="display: none; border: none">
                <table class="table table-hover" style="border: none">
                    <tbody>

                    </tbody>
                </table>
            </div>
        </td>
    </tr>

</template>

<script>
export default {
    name: "ProductCategoryRow",
    props: [
        'categoryData',
        'productData'
    ],
    data () {
        return {
            category: this.$props.categoryData,
            product: this.$props.productData
        }
    },
    methods: {
        async toggleChecked() {
            try {
                let res = await axios.get(`/admin/products/${this.product.id}/categories/${this.category.id}/toggle`)
                console.log(res)
            } catch (e) {
                alert(e)
            }
        }
    }
}
</script>

<style scoped>

</style>
