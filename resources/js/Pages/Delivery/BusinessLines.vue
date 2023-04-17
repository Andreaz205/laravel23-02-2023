<template>
    <Spinner v-if="isLoading" />
    <FlushMessage />
    <div class="card">
        <div class="card-header text-center text-xl relative">
            Деловые линии
            <button class="btn btn-default absolute top-1/2 -translate-y-1/2 left-1">
                <Link href="/admin/delivery">
                    Ко всем
                </Link>
            </button>
        </div>
    </div>

    <div class="card">

    </div>
    <div class="row">
        <div class="col-12">
            Терминал:
        </div>
    </div>
</template>

<script>

import FlushMessage from '@/Components/FlashMessage.vue'
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Spinner from "@/Components/Spinner.vue";
export default {
    name: "DeliveryLines",
    components: {Spinner, Link, FlushMessage},
    layout: AuthenticatedLayout,
    data () {
        return {
            isLoading: false,
        }
    },
    methods: {
        async saveChanges() {
            try {
                this.isLoading = true
                let data = {
                    terminal_id: 305,
                    terminal_address: 'Какой то адрес'
                }
                await this.$inertia.patch('/admin/delivery/business-lines', data)
                this.isLoading = true
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
