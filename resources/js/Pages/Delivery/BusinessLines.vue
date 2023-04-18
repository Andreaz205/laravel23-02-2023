<template>
    <Spinner v-if="isLoading" />
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
    <FlushMessage />

    <div class="card m-3">
        <div class="card-header text-center text-lg">
            Терминал:
        </div>
        <div class="card-body">
            <div class="row">
                <input type="text" class="form-control col-9" disabled :value="data?.terminal_address">
                <div class="col-3">
                    <button class="btn btn-warning" @click="handleChangeTerminalFormClick">
                        Изменить
                    </button>
                </div>
            </div>

            <template v-if="isTerminalFormOpen">
                <div class="row mt-3">
                    <div class="col-3">
                        <label >Название города:</label><span class="text-red">*</span>
                    </div>
                    <input type="text" class="col-7 form-control" v-model="cityTerm">
                    <div class="col-2">
                        <button class="btn btn-primary" @click="fetchCity" :disabled="!cityTerm">
                            Запросить
                        </button>
                    </div>
                </div>

                <div class="row mt-3" v-if="options ?? options.length">
                    <label class="col-3">Выберите город из доступных</label>
                    <div class="col-9">
                        <ModelSelect :options="options" v-model="item"/>
                    </div>
                </div>

                <div class="row mt-3" v-if="terminalOptions && terminalOptions.length">
                    <label class="col-3">Выберите терминал из доступных</label>
                    <div class="col-9">
                        <ModelSelect :options="terminalOptions" v-model="terminalItem"/>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button class="btn btn-primary w-full" @click="saveChanges">
                            Сохранить
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

</template>

<script>
import "vue-search-select/dist/VueSearchSelect.css"
import {ModelSelect} from "vue-search-select";
import FlushMessage from '@/Components/FlashMessage.vue'
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Spinner from "@/Components/Spinner.vue";
export default {
    name: "DeliveryLines",
    components: {Spinner, Link, FlushMessage, ModelSelect},
    layout: AuthenticatedLayout,
    props: [
      'data'
    ],
    data () {
        return {
            cityTerm: '',
            isLoading: false,
            isTerminalFormOpen: false,
            terminal_id: null,
            terminal_address: null,
            options: [],
            terminalOptions: [],
            terminalItem: {text: null, value: null},
            item: {text: null, value: null}
        }
    },
    methods: {
        handleChangeTerminalFormClick() {
            this.isTerminalFormOpen = !this.isTerminalFormOpen
        },
        async saveChanges() {
            try {
                this.isLoading = true
                let data = {
                    terminal_id: this.terminalItem.value,
                    terminal_address: this.terminalItem.text
                }
                await this.$inertia.patch('/admin/delivery/business-lines', data)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e?.message ?? e)
            }
        },
        async fetchCity() {
            try {
                this.isLoading = true
                let response = await axios.get(`/api/delivery/business-lines/cities-by-term?q=${this.cityTerm}`)
                let cities = response.data.cities
                this.options = cities?.map(city => ({
                    text: city.aString,
                    value: city.code,
                })) ?? []
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e?.message ?? e)
            }
        },
        async fetchTerminals(code) {
            try {
                this.isLoading = true
                let data = {
                    code: code,
                    direction: 'derival'
                }
                let response = await axios.post(`/api/delivery/business-lines/terminals`, data)
                let terminals = response.data.terminals
                this.terminalOptions = terminals?.map(terminal => ({
                    text: terminal.address,
                    value: terminal.id,
                })) ?? []
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e?.message ?? e)
            }
        },
    },
    watch: {
        async item(value) {
            try {
                this.isLoading = true
                await this.fetchTerminals(value.value)
                this.isLoading = false
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
