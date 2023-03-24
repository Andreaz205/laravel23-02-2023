<template>
    <div class="modal fade" id="createVariantModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить свойства</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">



<!--                        <template v-if="productOptionNames && productOptionNames.length">-->
<!--                            <div class="row" v-for="name in productOptionNames" :key="name.id">-->
<!--                                <div class="col-6">-->
<!--                                    <div class="form-group">-->
<!--                                        <label>Выберите свойство</label>-->
<!--                                        <select class="form-control">-->
<!--                                            <option value="new">Добавить новое значение</option>-->
<!--                                            <option disabled value="-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;"></option>-->
<!--                                            <template v-if="freeOptionNames && freeOptionNames.length">-->
<!--                                                <option v-for="freeOptionName in freeOptionNames" :key="freeOptionName.id">-->

<!--                                                </option>-->
<!--                                            </template>-->

<!--                                        </select>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                                <div class="col-6">-->
<!--                                    <div class="form-group">-->
<!--                                        <label>Выберите значение по умолчанию</label>-->
<!--                                        <select>-->
<!--                                            <option value="new">Добавить новое значение</option>-->
<!--                                            <option disabled value="-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;"></option>-->
<!--                                            <template v-if="freeOptionNames && freeOptionNames.length">-->
<!--                                                <option v-for="freeOptionName in freeOptionNames" :key="freeOptionName.id">-->

<!--                                                </option>-->
<!--                                            </template>-->
<!--                                        </select>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </template>-->

                    <template v-if="creatingVariantFormData && creatingVariantFormData.length">
                        <div class="container-fluid" v-for="dataItem in creatingVariantFormData">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>{{ dataItem.title }}</label>
                                        <select class="custom-select"
                                                @change="handleClickCreateVariantOptionValue($event, dataItem)"
                                                :value="dataItem.creating_variant_selected_id">
                                            <option value="new">Добавить новое значение</option>
                                            <option v-for="(value) in dataItem.option_values" :value="value.id">
                                                {{ value.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div v-if="dataItem.is_new" class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" v-model="dataItem.new_value" class="form-control"
                                               placeholder="Введите название свойства">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

<!--                    <template v-else>-->
<!--                        <div class="container">-->
<!--                            <div class="row">-->
<!--                                <div class="col-xl-12 mb-2">-->
<!--                                    <button class="btn btn-primary" @click="addVariantCreatingOption">-->
<!--                                        Добавить свойство-->
<!--                                    </button>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row" v-for="option in variantCreatingOptions">-->
<!--                                <div class="col-5">-->
<!--                                    <div class="form-group">-->
<!--                                        <label>Название свойства</label>-->
<!--                                        <input type="text" class="form-control" v-model="option.name"-->
<!--                                               placeholder="Введите название свойства">-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-5">-->
<!--                                    <div class="form-group">-->
<!--                                        <label>Значение по умолчанию</label>-->
<!--                                        <input type="text" class="form-control" v-model="option.value"-->
<!--                                               placeholder="Введите значение по умолчанию">-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-xl-2 flex justify-center items-center" v-if="option.id != 1">-->
<!--                                    <button class="btn btn-danger" @click="deleteVariantCreatingOption(option.id)">-->
<!--                                        Удалить-->
<!--                                    </button>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </template>-->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="createVariant">Добавить
                        вариант
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {handleError} from "@/utils/handleError";

export default {
    name: "CreateVariantModal",
    emit: ['variantCreated'],
    props: ['productOptionNames', 'product'],
    data() {
        return {
            creatingVariantFormData: this.productOptionNames
        }
    },
    methods: {
        async createVariant() {
            try {
                let newOptionValues = []
                let optionsForBind = []
                let data
                if (this.creatingVariantFormData && this.creatingVariantFormData.length) {
                    this.creatingVariantFormData.map(item => {
                        if (item.creating_variant_selected_id != 'new') {
                            optionsForBind.push(item.creating_variant_selected_id)
                        }
                    })

                    this.creatingVariantFormData.map(item => {
                        if (item.is_new) {
                            newOptionValues.push({
                                'value': item.new_value,
                                'name_id': item.id,
                            })
                        }
                    })
                    data = {
                        newValues: newOptionValues,
                        values: optionsForBind
                    }
                    let response = await axios.post(`/admin/products/${this.product.id}/variants`, data)
                    let newVariant = response.data.data

                    // this.product.variants.push(newVariant)

                    if (newOptionValues &&  newOptionValues.length) {

                        newOptionValues.map(newValue => {

                            let val = newVariant.option_values.find(val => val.option_name_id == newValue.name_id)

                            let nameToUpdate = this.creatingVariantFormData.find(name => name.id == newValue.name_id)
                            nameToUpdate?.option_values.push({
                                id: val.id,
                                option_name_id: newValue.name_id,
                                is_default: true,
                                product_id: this.product.id,
                                title: newValue.value,
                            })

                            nameToUpdate.creating_variant_selected_id = +val.id
                            nameToUpdate.is_new = false
                        })
                    }
                    this.$emit('variantCreated', newVariant)
                }
                // else {
                //     data = {
                //         newOptions: this.variantCreatingOptions,
                //     }
                //     await axios.post(`/admin/products/${this.product.id}/variants`, data)
                //     location.reload()
                // }
            } catch (e) {
                console.log(e)
                let {errorsList} = handleError(e)
                alert( e)
            }
        },
        // deleteVariantCreatingOption(id) {
        //     if (this.variantCreatingOptions.length <= 1) {
        //         return;
        //     }
        //     this.variantCreatingOptions = this.variantCreatingOptions.filter(option => option.id != id)
        // },
        // addVariantCreatingOption() {
        //     let lastId = this.variantCreatingOptions[this.variantCreatingOptions.length - 1].id + 1
        //     this.variantCreatingOptions.push({id: lastId, name: null, value: null})
        // },
        handleClickCreateVariantOptionValue(event, dataItem) {
            let selectedIndex = event.target.options.selectedIndex
            dataItem.creating_variant_selected_id = event.target.options[event.target.options.selectedIndex].value
            if (selectedIndex == 0) dataItem.is_new = !dataItem.is_new
            if (dataItem.is_new == true && selectedIndex != 0) dataItem.is_new = false
        },
    },
    mounted() {
        // this.creatingVariantFormData = this.creatingVariantFormData.map(item => ({
        //     ...item, is_new: false, new_value: null, creating_variant_selected_id: item.pivot.default_option_value_id
        // }))
    }
}
</script>

<style scoped>

</style>
