<template>
    <tr
        data-widget="expandable-table"
        aria-expanded="false"
        :class="['clickable', {'bg-gray': selectedCategory?.id === category.id}]"
        @click="$emit('changeSelectedCategory', category)"
    >
        <td>
            <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
            {{ category.name }}
            <button id="close" class="mr-2" @click="category.parent_category_id ? $emit('deleteCategory') :  $emit('deleteCategory', category)">
                <i class="fas fa-times" ></i>
            </button>
        </td>
    </tr>

    <tr class="expandable-body d-none" v-if="category.child_categories && category.child_categories">
        <td>
            <div class="p-0" style="display: none;">
                <table class="table table-hover">
                    <tbody>
                        <CategoryRow
                            v-for="cat in category.child_categories"
                            :key="cat.id"
                            :category="cat"
                            :selected-category="selectedCategory"
                            @changeSelectedCategory="emitCallback"
                            @deleteCategory="deleteCategory(category, cat)"
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
    emits: ['changeSelectedCategory', 'deleteCategory'],
    data () {
        return {

        }
    },
    components: {
    },
    props: ['category', 'selectedCategory', 'changeSelectedCategory'],
    methods: {
        async deleteCategory(parentCategory, category) {
            try {
                await axios.delete(`/admin/categories/${category.id}`)
                parentCategory.child_categories = parentCategory.child_categories.filter(cat => cat.id !== category.id)
                this.$emit('changeSelectedCategory', null)
            } catch (e) {
                alert(e)
            }
        },
        emitCallback(category) {
            this.$emit('changeSelectedCategory', category)
        },
        // findCategoryById(id) {
        //
        // }
    }
}
</script>

<style scoped>

</style>
