<template>
    <tr
        data-widget="expandable-table"
        aria-expanded="false"
        :class="['clickable', {'bg-gray': selectedCategory?.id == category.id}]"
        @click="$emit('changeSelectedCategory', category)"
    >
        <td>
            <div class="flex justify-between items-center">
                <div>
                    <i class="expandable-table-caret fas fa-caret-right fa-fw" v-if="category.child_categories && category.child_categories.length"></i>
                    {{ category.name }}
                </div>
                <div>
                    <input v-if="hasCheckbox"  type="checkbox" class="form-control" @change="onChangeCheckbox($event, category)" @click="handleCheckboxClick" :checked="category.is_checked || checkedIds?.includes(category.id)">
                    <button v-if="deleteButton" class="mr-2" @click="category.parent_category_id ? emitDeleteCategoryWithoutCategory :  emitDeleteCategoryWithCategory($event, category)">
                        <i class="fas fa-times" ></i>
                    </button>
                </div>
            </div>
        </td>
    </tr>

    <tr class="expandable-body d-none" v-if="category.child_categories && category.child_categories.length">
        <td>
            <div class="p-0" style="display: none;">
                <table class="table table-hover">
                    <tbody>
                        <CategoryRow

                            v-for="cat in category.child_categories"
                            :key="cat.id"
                            :category="cat"
                            :selected-category="selectedCategory"
                            @changeSelectedCategory="emitChangeSelectedCategory"
                            @changeCheckboxValue="emitChangeCheckboxValue"
                            @deleteCategory="deleteCategory($event, category, cat)"
                            :delete-button="deleteButton"
                            :has-checkbox="hasCheckbox"
                            :checked-ids="checkedIds"
                        />
                    </tbody>
                </table>
            </div>
        </td>
    </tr>

    <tr class="expandable-body d-none" v-else>
        <td>
            <div class="p-0" style="display: none;">
                <table class="table table-hover">
                    <tbody>

                    </tbody>
                </table>
            </div>
        </td>
    </tr>

</template>

<script>

export default {
    name: "CategoryRow",
    emits: ['changeSelectedCategory', 'deleteCategory', 'changeCheckboxValue'],
    data () {
        return {

        }
    },
    components: {
    },
    props: ['category',
        'selectedCategory',
        'changeSelectedCategory',
        'deleteButton',
        'hasCheckbox',
        'checkedIds'
    ],
    methods: {
        async deleteCategory(event, parentCategory, category) {
            try {
                event.stopPropagation()
                await axios.delete(`/admin/categories/${category.id}`)
                parentCategory.child_categories = parentCategory.child_categories.filter(cat => cat.id != category.id)
                this.$emit('changeSelectedCategory', null)
            } catch (e) {
                alert(e)
            }
        },
        emitChangeSelectedCategory(category) {
            this.$emit('changeSelectedCategory', category)
        },
        emitDeleteCategoryWithoutCategory(event) {
            event.stopPropagation()
            this.emitChangeSelectedCategory(null)
            this.$emit('deleteCategory')
        },
        emitDeleteCategoryWithCategory(event, category) {
            this.emitChangeSelectedCategory(null)
            event.stopPropagation()
            this.$emit('deleteCategory', category)
        },
        emitChangeCheckboxValue(category, isChecked) {
            this.$emit('changeCheckboxValue', category, isChecked)
        },
        onChangeCheckbox(event, category) {
            event.stopPropagation()
            let isChecked = event.target.checked
            this.$emit('changeCheckboxValue', category, isChecked)
        },
        handleCheckboxClick(event) {
            event.stopPropagation()
        }
        // findCategoryById(id) {
        //
        // }
    }
}
</script>

<style scoped>

</style>
