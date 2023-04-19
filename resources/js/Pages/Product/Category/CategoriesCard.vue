<template>
    <div class="card">
        <div class="card-header text-center text-lg" >
            <div @click="handleCategoryClick(null)" class="cursor-pointer transition-colors hover:text-gray-400">
                Категории
            </div>

        </div>
        <div class="card-body">
            <div class="container-fluid">
                <CategoryItem
                    v-for="category in categories"
                    :key="category.id"
                    :category="category"
                    :active-category-id="activeCategoryId"
                    @clickCategory="handleCategoryClick"
                />

            </div>

        </div>
    </div>
</template>

<script>
import CategoryItem from "@/Pages/Product/Category/CategoryItem.vue";
export default {
    name: "CategoriesCard",
    components: {CategoryItem},
    emits: ['changeCategory'],
    props: [
        'categories'
    ],
    data () {
        return {
            activeCategoryId: null,
        }
    },
    methods: {
        handleCategoryClick(category, type) {
            type === 'all'
                ? this.activeCategoryId = category?.id + '-all'
                : this.activeCategoryId = category?.id
            this.$emit('changeCategory', category, type)
        }
    }
}
</script>

<style scoped>

</style>
