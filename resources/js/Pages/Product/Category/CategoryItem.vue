<template>
    <div class="row">
        <div class="col-12 flex justify-start items-center">
            <i class="fas fa-caret-down cursor-pointer" @click="isExpanded = true" v-if="!isExpanded && category.child_categories"></i>
            <i class="fas fa-caret-up cursor-pointer" @click="isExpanded = false" v-else-if="category.child_categories"></i>
            <div
                :class="['ml-2 hover:text-gray-500 transition-colors cursor-pointer text-lg', {'bg-gray-200': activeCategoryId === `${category.id}-all`}]"
                @click="handleClick(category, 'all')"
            >
                {{category.name}}
            </div>
        </div>

    </div>
    <div v-if="isExpanded">
        <div
            :class="['ml-3 cursor-pointer hover:text-gray-500', {'bg-gray-200': child_category.id === activeCategoryId}]"
            v-for="child_category in category.child_categories"

            @click="handleClick(child_category)"
        >
            {{child_category.name}}
        </div>
        <div
            :class="['ml-3 cursor-pointer hover:text-gray-500', {'bg-gray-200': category.id === activeCategoryId}]"
            @click="handleClick(category)"
        >
            Остальные
        </div>
    </div>


</template>

<script>
export default {
    name: "CategoryItem",
    props: ['category', 'activeCategoryId'],
    data() {
        return {
            isExpanded: false
        }
    },
    emits: ['clickCategory'],
    methods: {
        handleClick(category, type = 'current') {
            this.$emit('clickCategory', category, type)
        }
    }
}
</script>

<style scoped>

</style>
