<template>
    <div class="modal fade" id="createOptionsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                    <template v-if="names && names.length">

                        <div class="row" v-for="name in names" :key="name.id">
                            <div class="col-12 py-2 text-lg">
                                - {{name.title}}
                            </div>
                        </div>
                    </template>

                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary" @click="appendField">
                                Добавить ещё
                            </button>
                        </div>
                    </div>


                    <template v-for="(element, key) in formData" :key="key">
                        <div class="row" >
                            <div class="col-5">
                                <div class="form-group">
                                    <label>Добавить свойство</label>
                                    <select class="form-control" v-model="element.option_name_id">
                                        <option disabled value="default">Выберите свойство</option>
                                        <option value="new">Добавить новое свойство</option>
                                        <template v-if="allOptionNames && allOptionNames.length">
                                            <option
                                                v-for="freeName in allOptionNames"
                                                :key="freeName.id"
                                                :value="freeName.id"
                                            >
                                                {{freeName.title}}
                                            </option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label>Значение по умолчанию</label>
                                    <select
                                        :disabled="!element.option_name_id ||
                                    element.option_name_id === 'new' ||
                                    element.option_name_id === 'default'"

                                        class="form-control"
                                        v-model="element.default_option_value_id"
                                    >
                                        <option disabled value="default">Выберите значение</option>
                                        <option value="new">Добавить новое значение</option>
                                        <template v-if="element.option_name_id && allNames.find(n => n.id === element.option_name_id)">
                                            <option
                                                v-for="value in allNames.find(n => n.id === element.option_name_id).option_values"
                                                :key="value.id"
                                                :value="value.id"
                                            >
                                                {{value.title}}
                                            </option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 flex items-center justify-center">
                                <button
                                    class="btn btn-danger"
                                    @click="deleteField(element)"
                                    v-if="key !== 0"
                                >
                                    Удалить
                                </button>
                            </div>
                        </div>

                        <div class="row" v-if="element.option_name_id === 'new'">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Введите название для свойства</label>
                                    <input type="text" class="form-control" v-model="element.name">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Значение по умолчанию</label>
                                    <input type="text" class="form-control" v-model="element.value">
                                </div>
                            </div>
                        </div>

                        <div class="row" v-if="element.option_name_id !== 'new' && element.option_name_id !== 'default' && element.default_option_value_id === 'new'">
                            <div class="col-5">

                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label>Значение по умолчанию</label>
                                    <input type="text" class="form-control" v-model="element.value">
                                </div>
                            </div>
                        </div>
                    </template>




<!--                    <template v-if="createOptionFormData && createOptionFormData.length">-->
<!--                        <div class="row" v-for="option in createOptionFormData">-->
<!--                            <div-->
<!--                                :class="[{'col-sm-6': option.id == createOptionFormData[0].id}, {'col-sm-5': option.id != createOptionFormData[0].id}]">-->
<!--                                &lt;!&ndash; text input &ndash;&gt;-->
<!--                                <div class="form-group">-->
<!--                                    <label>Название свойства</label>-->
<!--                                    <input type="text" v-model="option.name" class="form-control"-->
<!--                                           placeholder="Введите название свойства">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div-->
<!--                                :class="[{'col-sm-6': option.id == createOptionFormData[0].id}, {'col-sm-5': option.id != createOptionFormData[0].id}]">-->
<!--                                <div class="form-group">-->
<!--                                    <label>Значение по умолчанию</label>-->
<!--                                    <input type="text" class="form-control" v-model="option.defaultValue"-->
<!--                                           placeholder="Введите значение по умолчанию">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-sm-2 d-flex justify-center items-end"-->
<!--                                 v-if="option.id != createOptionFormData[0].id">-->
<!--                                <div class="form-group">-->
<!--                                    <button class="btn btn-danger"-->
<!--                                            @click="deleteOptionInCreateForm($event, option.id)">-->
<!--                                        Удалить-->
<!--                                    </button>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="flex justify-center">-->
<!--                            <button class="btn btn-info" @click="addOptionNameInForm">-->
<!--                                Добавить свойство-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </template>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="submit">Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {handleError} from "@/utils/handleError";

export default {
    name: "OptionsModal",
    props: ['productOptionNames', 'allOptionNames', 'product'],
    data () {
        return {
            names: this.productOptionNames,
            allNames: this.allOptionNames,
            formData: [
                {
                    option_name_id: 'default',
                    name: '',
                    default_option_value_id: 'default',
                    value: '',
                },
            ]
        }
    },
    methods: {
        appendField() {
            this.formData.push({
                option_name_id: 'default',
                name: null,
                default_option_value_id: 'default',
                value: null,
            })
        },
        deleteField(element) {
            this.formData = this.formData.filter(elem => elem !== element)
        },
        async submit() {
            try {
                let options = []
                this.formData.map(element => {
                    if (element.option_name_id === 'new') {
                        options.push({
                            name: element.name,
                            value: element.value,
                        })
                    } else if (element.default_option_value_id === 'new') {
                        options.push({
                            option_name_id: element.option_name_id,
                            value: element.value,
                        })
                    } else if (element.option_name_id !== 'default' && element.option_value_id !== 'default') {
                        options.push({
                            option_name_id: element.option_name_id,
                            option_value_id: element.default_option_value_id
                        })
                    }
                })
                let response = await axios.post(`/admin/products/${this.product.id}/options`, {options: options})
            } catch (e) {
                console.log(e)
                let {message, errors, errorsList} = handleError(e)
                alert(errorsList)
            }
        }
    }
}
</script>

<style scoped>

</style>
