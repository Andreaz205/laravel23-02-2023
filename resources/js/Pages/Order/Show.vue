<template>
    <AuthenticatedLayout>
        <div v-if="order">

            <div class="row">
               <div class="col-xl-9 col-md-12">
                   <div class="card">

                       <div class="card-header">
                           <div class="flex gap-x-4">
                               <Link href="/admin/orders">
                                   <button class="btn btn-default">
                                       <i class="fas fa-long-arrow-alt-left"></i>
                                       <span class="ml-1">
                                    Назад
                                </span>
                                   </button>
                               </Link>
                               <div>
                                   <span class="text-lg">Заказ </span>
                                   <span class="text-2xl text-red-400">
                                {{ order.id }}</span> <span class="text-sm ">от</span> <small class="text-gray-600">
                                   {{ formatDate(order.created_at) }}
                               </small>
                               </div>
                           </div>
                       </div>

                       <div class="card-body">
                           <div class="flex justify-start items-center">
                               <div class="form-group w-1/5 mr-2">
                                   <label>Статус заказа</label>
                                   <select class="form-control select2bs4 select2-hidden-accessible cursor-pointer" style="width: 100%;"
                                           :value="order.status"
                                           tabindex="-1" aria-hidden="true" @change="setStatus">
                                       <option selected="selected" value="new" :disabled="order.status === 'new'"
                                               :class="{'bg-blue-400': order.status === 'new'}">Новый
                                       </option>
                                       <option selected="selected" value="preparing" :disabled="order.status === 'preparing'"
                                               :class="{'bg-blue-400': order.status === 'preparing'}">В обработке
                                       </option>
                                       <option selected="selected" value="agreed" :disabled="order.status === 'agreed'"
                                               :class="{'bg-blue-400': order.status === 'agreed'}">Согласован
                                       </option>
                                       <option selected="selected" value="stored" :disabled="order.status === 'stored'"
                                               :class="{'bg-blue-400': order.status === 'stored'}">Отгружен
                                       </option>
                                       <option selected="selected" value="delivered" :disabled="order.status === 'delivered'"
                                               :class="{'bg-blue-400': order.status === 'delivered'}">Доставлен
                                       </option>
                                       <option selected="selected" value="aborted" :disabled="order.status === 'aborted'"
                                               :class="{'bg-blue-400': order.status === 'aborted'}">Отменен
                                       </option>
                                       <option selected="selected" value="client_enabled" :disabled="order.status === 'client_enabled'"
                                               :class="{'bg-blue-400': order.status === 'client_enabled'}">Покупатель не доступен
                                       </option>
                                       <option selected="selected" value="back" :disabled="order.status === 'back'"
                                               :class="{'bg-blue-400': order.status === 'back'}">Возврат
                                       </option>
                                       <option selected="selected" value="back_process" :disabled="order.status === 'back_process'"
                                               :class="{'bg-blue-400': order.status === 'back_process'}">В процессе возврата
                                       </option>
                                   </select>
                               </div>
                               <div class="form-group w-1/5 mr-2">
                                   <label>Статус заказа</label>
                                   <select class="form-control select2bs4 select2-hidden-accessible cursor-pointer" style="width: 100%;"
                                           :value="order.is_payed"
                                           tabindex="-1" aria-hidden="true" @change="setPayStatus">
                                       <option value=false :disabled="order.is_payed === false">Не оплачен</option>
                                       <option value=true :disabled="order.is_payed === true">Оплачен</option>
                                   </select>
                               </div>
                               <div class="form-group w-1/5 mr-2">
                                   <label>Способ оплаты</label>
                                   <select class="form-control select2bs4 select2-hidden-accessible cursor-pointer" style="width: 100%;"
                                           :value="order.payment_variant"
                                           tabindex="-1" aria-hidden="true" @change="setPaymentVariant">
                                       <option value='cash' :disabled="order.payment_variant === 'cash'">Наличные</option>
                                       <option value='card' :disabled="order.payment_variant === 'card'">Онлайн оплата картой</option>
                                       <option value='partials' :disabled="order.payment_variant === 'partials'">Оплата по частям</option>
                                       <option value='out_variant' :disabled="order.payment_variant === 'out_variant'">Внешний способ
                                       </option>
                                   </select>
                               </div>
                               <div class="form-group w-1/5 mr-2">
                                   <label>Ответственный</label>
                                   <select class="form-control select2bs4 select2-hidden-accessible cursor-pointer" style="width: 100%;"
                                           :value="order.duty_admin_id ?? '-'"
                                           tabindex="-1" aria-hidden="true" @change="setManager">
                                       <option v-for="admin in admins" :value='admin.id' :disabled="order.duty_admin_id === admin.id">
                                           {{ admin.name }}
                                       </option>
                                       <option value="-" :disabled="!order.duty_admin_id">-</option>
                                   </select>
                               </div>
                           </div>
                       </div>
                   </div>

                   <div class="card my-3">
                       <div class="card-body table-responsive p-0">
                           <table class="table table-hover text-nowrap">
                               <thead>
                               <tr>
                                   <th style="width: 50px"></th>
                                   <th>Фото</th>
                                   <th>Артикул</th>
                                   <th>Наименование</th>
                                   <th>Комментарий</th>
                                   <th>Количество</th>
                                   <th>Сумма</th>
                                   <th style="width: 100px;"></th>
                               </tr>
                               </thead>

                               <tbody v-if="variants">

                               <tr v-for="variant in variants">
                                   <td>
                                       <div class="flex justify-center">
                                           {{ variant.id }}
                                       </div>
                                   </td>
                                   <td>
                                       <div class="flex justify-center">
                                           <a :href="'/admin/products/' + variant.product_link_id">
                                               <img :src="variant.image_url || '/storage/images/no-image.jpg'">
                                           </a>
                                       </div>
                                   </td>
                                   <td>
                                       <div class="flex justify-center">
                                           {{ variant.code ?? '-' }}
                                       </div>
                                   </td>
                                   <td>
                                       <div class="flex justify-center">
                                           <a :href="'/admin/products/' + variant.product_link_id">
                                               {{ variant.title }}
                                           </a>
                                       </div>
                                   </td>
                                   <td>
                                       <div class="flex justify-center">
                                           <div >
                                               {{ order.comment ? order.comment : '-' }}
                                           </div>
                                       </div>
                                   </td>
                                   <td>
                                       <div class="flex justify-center">
                                           <div >
                                               {{ variant.ordered_quantity }}
                                           </div>
                                       </div>
                                   </td>
                                   <td>
                                       <div class="flex justify-center">
                                           {{ variant.ordered_quantity * variant.price }}
                                       </div>
                                   </td>
                                   <td>
                                       <div class="flex justify-center">
                                           <a href="#">...</a>
                                       </div>
                                   </td>
                               </tr>
                               </tbody>
                           </table>

                           <div class="d-flex justify-between items-end flex-column">
                               <div>Итого <span class="font-bold">{{ finishSum }}</span></div>
                               <div>Прибыль <span class="font-bold">{{ profit }}</span></div>
                           </div>
                       </div>
                   </div>


                   <div class="card card-primary card-tabs">
                       <div class="card-header p-0 pt-1">
                           <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                               <li class="nav-item">
                                   <a class="nav-link" id="custom-tabs-one-home-tab" data-toggle="pill"
                                      href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                      aria-selected="false">Комментарий продавца</a>
                               </li>
                               <li class="nav-item">
                                   <a class="nav-link active" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                      href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                                      aria-selected="true">История</a>
                               </li>
                               <li class="nav-item">
                                   <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill"
                                      href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages"
                                      aria-selected="false">Клиент</a>
                               </li>
                               <li class="nav-item">
                                   <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill"
                                      href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings"
                                      aria-selected="false">Доставка</a>
                               </li>
                           </ul>
                       </div>

                       <div class="card-body">
                           <div class="tab-content min-h-[300px]" id="custom-tabs-one-tabContent">
                               <div class="tab-pane fade" id="custom-tabs-one-home" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-home-tab">
                                   <div contenteditable="true" @input="adminComment = $event.target.innerText">
                                       {{ order.admin_comment ? order.admin_comment : '-' }}
                                   </div>
                                   <button @click="setManagerComment(adminComment)" class="btn btn-primary">
                                       Сохранить
                                   </button>
                               </div>
                               <div class="tab-pane fade active show" id="custom-tabs-one-profile" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-profile-tab">
                                   <div v-if="order.history">
                                       <div v-for="historyItem in order.history">
                                           {{ formatDate(historyItem.created_at) }} - {{ historyItem.note }}
                                       </div>
                                   </div>
                               </div>
                               <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-messages-tab">
                                   <div>
                                       {{ order.user_name }}
                                   </div>
                                   <div>
                                       {{ order.phone }}
                                   </div>
                               </div>
                               <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-settings-tab">
                                   {{ order.address }}
                               </div>
                           </div>
                       </div>
                       <!-- /.card -->
                   </div>
               </div>
                <div class="col-xl-3 col-md-12">
                    <div class="card">
                        <div class="card-header text-center text-lg">
                            Клиент
                        </div>
                        <div class="card-body">
<!--                            <div v-if="order.user && order.user.length">-->
<!--                                {{order.user[0].name}}-->
<!--                                <small>-->
<!--                                    Зарегестрирован в системе-->
<!--                                </small>-->
<!--                            </div>-->
                            <div v-if="order.user && order.user[0]" class="text-center text-lg">
                                <Link  :href="`/admin/users/${order.user[0].id}`">{{order.user[0].name}}</Link>
                                <small> Зарегестрирован в системе</small>
                            </div>
                            <div v-else>
                                {{order.user_name}}
                                <small>
                                    Клиент не зарегестрирован
                                </small>
                            </div>
                            <div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>



        </div>
    </AuthenticatedLayout>

</template>

<script>
import {formatDate} from "/resources/js/utils/formatDate"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";

export default {
    name: "Show",
    components: {
        AuthenticatedLayout,
        Link
    },
    props: ['orderData', 'variantsData', 'adminsData'],
    data() {
        return {
            order: this.$props.orderData,
            admins: this.$props.adminsData,
            variants: this.$props.variantsData,
            adminComment: this.$props.orderData.admin_comment
        }
    },
    methods: {
        formatDate(date) {
            return formatDate(date)
        },
        async setStatus(event) {
            try {
                let value = event.target.options[event.target.options.selectedIndex].value
                let response = await axios.post(`/admin/orders/${this.order.id}/status`, {'status': value})
                this.order.history = response.data.history
                this.order.status = value
            } catch (e) {
                alert(e)
            }
        },
        async setPayStatus(event) {
            try {
                let value = event.target.options[event.target.options.selectedIndex].value
                if (value === 'true') {
                    value = true
                } else {
                    value = false
                }
                let response = await axios.post(`/admin/orders/${this.order.id}/toggle-pay`, {'is_payed': value})
                this.order.history = response.data.history
                this.order.is_payed = value
            } catch (e) {
                alert(e)
            }
        },
        async setPaymentVariant(event) {
            try {
                let value = event.target.options[event.target.options.selectedIndex].value
                let response = await axios.post(`/admin/orders/${this.order.id}/payment`, {'payment_variant': value})
                this.order.history = response.data.history
                this.order.payment_variant = value
            } catch (e) {
                alert(e)
            }
        },
        async setManager(event) {
            try {
                let adminId = event.target.options[event.target.options.selectedIndex].value
                let response = await axios.post(`/admin/orders/${this.order.id}/duty`, {'admin_id': adminId})
                this.order.history = response.data.history
                this.order.duty_admin_id = adminId
            } catch (e) {
                alert(e)
            }
        },
        async setManagerComment(comment) {
            try {
                let response = await axios.post(`/admin/orders/${this.order.id}/admin-comment`, {'admin_comment': comment})
                this.order.history = response.data.history
                this.order.duty_admin_comment = comment
            } catch (e) {
                alert(e)
            }
        }
    },
    computed: {
        finishSum() {
            let price = 0
            if (this.variants && this.variants.length) {
                this.variants.map(variant => price += (variant.ordered_quantity * variant.price))
            }
            return price
        },
        profit() {
            let price = 0
            if (this.variants && this.variants.length) {
                this.variants.map(variant => price += (variant.ordered_quantity * (variant.price - variant.purchase_price)))
            }
            return price
        },
    },
    mounted() {
        console.log(this.order)
    }
}
</script>

<style lang="scss">
table {
    table-layout: fixed;
    width: 100%;
    border-collapse: collapse;
    /*border: 1px solid black;*/
}

.variants-table th, .variants-table td {
    padding: 20px;
    border: 1px solid black;
}
</style>
