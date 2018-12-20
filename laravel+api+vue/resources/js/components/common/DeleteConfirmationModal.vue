<template>
    <div class="card">
        <div class="card-body">
            <h3 class="text-center">{{ text }}</h3>
            <hr/>
            <div class="text-center mt-4">
                <button class="btn btn-danger" v-on:click="$emit('close', {response: 'yes', index: index})">Yes</button>
                <button class="btn btn-success" v-on:click="$emit('close', {response: 'no', index: index})">No</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name : "DeleteConfirmationModal",
        props: {
            text : {
                type   : String,
                default: ''
            },
            index: {
                type: Number,
            },
        },
    }
</script>

<style scoped>

</style>
