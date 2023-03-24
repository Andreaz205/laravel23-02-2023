<template>
    <tr data-widget="expandable-table" aria-expanded="false" class="clickable">
        <td style="border: none; position: relative">
            <div class="d-flex justify-between">
                <div class="form-group" v-if="isSwitch">
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
                <div class="form-group" v-if="selectButton">
                    <button class="btn btn-primary" @click="handleSelectButtonClick($event, category)">
                        Выбрать
                    </button>
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
                            <ProductCategoryRow :category-data="childCategory" :product-data="this.$props.productData" :is-switch="isSwitch" :select-button="selectButton" @select="handleNestedSelectedEvent"/>
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
    emits: ['select'],
    props: [
        'selectButton',
        'categoryData',
        'productData',
        'isSwitch'
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
        },
        handleSelectButtonClick(e, category) {
            e.stopPropagation()
            this.$emit('select', category)
        },
        handleNestedSelectedEvent(category) {
            this.$emit('select', category)
        }
    }
}
</script>

<style scoped>

</style>
