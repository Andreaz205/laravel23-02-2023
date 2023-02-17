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
        </td>
    </tr>

    <tr class="expandable-body d-none" v-if="category.child_categories && category.child_categories.length">
        <td>
            <div class="p-0" style="display: none;">
                <table class="table table-hover">
                    <tbody>
                        <CategoryRow v-for="category in category.child_categories" :category="category" :selected-category="selectedCategory" @changeSelectedCategory="$emit('changeSelectedCategory', category)"/>
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
    emits: ['changeSelectedCategory'],
    components: {
    },
    props: ['category', 'selectedCategory'],

}
</script>

<style scoped>

</style>
