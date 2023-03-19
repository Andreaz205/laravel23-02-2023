<template>
    <Spinner v-if="isLoading" />
    <AuthenticatedLayout>
        <div>
            Материалы для категории {{category.name}}
        </div>
        <ModelSelect :options="options" v-model="item" placeholder="select item" autocomplite="off"/>
    </AuthenticatedLayout>
</template>

<script>
import {ModelSelect} from 'vue-search-select'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Spinner from "@/Components/Spinner.vue";
import 'vue-search-select/dist/VueSearchSelect.css';
export default {
    name: "Edit",
    components: {AuthenticatedLayout, Spinner, ModelSelect},
    props: [
        'category'
    ],
    data () {
        return {
            errors: null,
            isLoading: false,
            options: [
                {value: '1', text: '6'},
                {value: '2', text: '67'},
            ],
            item: {
                value: null,
                text: null,
            }
        }
    },
}
</script>

<style scoped>

</style>
