<template>
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input"
                   :id="this.switchId" :checked="this.isChecked"
                   @change="$emit('changeSwitch', $event)"
            >
            <label class="custom-control-label" :for="switchId"></label>
        </div>
    </div>
</template>

<script>
export default {
    name: "CustomSwitch",
    emits: ['changeSwitch'],
    props: ['switchId', 'isChecked', 'onChange']
}
</script>

<style scoped>
</style>
