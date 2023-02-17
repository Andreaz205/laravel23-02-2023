<template>
    <tr>
        <td class=" text-lg h-full">
            <a :href="'/admin/orders/' + order.id" class="block text-center">
                {{ order.id }}
            </a>
        </td>
        <td>{{ order.delivery_date }}</td>
        <td>{{ order.sum }}</td>
        <td>{{ order.delivery_price }}</td>
        <td>{{ order.is_payed ? 'Оплачен' : 'Не оплачен' }}</td>
        <td>{{calculateStatus(order.status)}}</td>
        <td>{{order.user_name}}</td>
        <td>{{order.phone}}</td>
        <td>{{order.email}}</td>
        <td>{{calculatePaymentVariant(order.payment_variant)}}</td>
        <td>
            {{ order.delivery_type === 'delivery' ? 'Доставка' : order.delivery_type === 'none' ? 'Нет' : 'Самовывоз' }}
        </td>
        <td>{{order.admin_comment}}</td>
        <td>{{order.client_comment}}</td>
        <td>{{order.address}}</td>
        <td>{{order?.admin?.name || 'Нет'}}</td>
        <td>{{formatDate(order.created_at)}}</td>
    </tr>

</template>

<script>
export default {
    name: "OrderRow",
    props: ['order', 'formatDate'],
    data () {
        return {
            status: '',
            paymentVariant: '',
        }
    },
    methods: {
        calculateStatus(status) {
            if (status === 'new') {
                return this.status = 'Не обработан'
            } else if (status === 'not_handled') {
                return this.status = 'Не обработан'
            } else if (status === 'handled') {
                return this.status = 'Обработан'
            } else if (status === 'order_created') {
                return this.status = 'Заказ создан'
            } else if (status === 'recreating') {
                return this.status = 'Переизготовление'
            } else if (status === 'ready_delivery') {
                return this.status = 'Готов для доставки'
            } else if (status === 'in_delivery') {
                return this.status = 'В доставке'
            } else if (status === 'delivered') {
                return this.status = 'Доставлен'
            } else if (status === 'canceled') {
                return this.status = 'Отменен'
            } else if (status === 'return') {
                return this.status = 'Возврат'
            }
        },
        calculatePaymentVariant(paymentVariant) {
            if (paymentVariant === 'cash') {
                return this.paymentVariant = 'Наличные'
            } else if (paymentVariant === 'card') {
                return this.paymentVariant = 'Онлайн оплата картой'
            } else if (paymentVariant === 'partials') {
                return this.paymentVariant = 'Оплата по частям'
            } else if (paymentVariant === 'out_variant') {
                return this.paymentVariant = 'Внешний способ оплаты'
            }
        },

    },

    mounted() {
        this.calculateStatus(this.$props.order.status)
    }
}
</script>

<style scoped>

</style>
