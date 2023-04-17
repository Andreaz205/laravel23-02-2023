<template>
    <div v-if="points?.length" class="row mt-2">
        <div class="col-12">
            <ModelSelect :options="options" v-model="item"/>
        </div>
    </div>
</template>

<script>
import {ModelSelect} from 'vue-search-select'
import "vue-search-select/dist/VueSearchSelect.css"
export default {
    name: "Pvz",
    emits: ['changePvz'],
    props: [
        'points'
    ],
    components: {ModelSelect},
    data() {
        return {
            options: this.points?.map(point => ({
                text: point.address,
                value: point.platform_id,
            })),
            item: {
                text: null,
                value: null
            },
        }
    },
    watch: {
        item(value) {
            this.$emit('changePvz', value)
        }
    }
}
</script>

<style scoped>

</style>
