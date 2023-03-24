<template>
    <div v-if="$page.props.flash.message" class="card m-5 p-5 bg-yellow-100 relative">
        <div class="card-body">
            <div class="card-title">
                {{$page.props.flash.message}}
            </div>
        </div>
        <div class="absolute top-4 right-4 cursor-pointer" @click="this.$page.props.flash.message = null">
            <i class="fas fa-times"></i>
        </div>
    </div>
    <div>
        <Errors :errors="err" v-if="err" class="card m-5 p-5"/>
    </div>

</template>

<script>
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "FlashMessage",
    components: {Errors},
    data () {
        return {
            err: null,
        }
    },
    computed: {
        errors() {
            return this.$page.props.errors && Object.keys(this.$page.props.errors)?.length ? [this.$page.props.errors] : null
        },
    },
    watch: {
        errors: {
            handler: function (val) {
                this.err = val
            }
        }
    }
}
</script>

<style scoped>

</style>
