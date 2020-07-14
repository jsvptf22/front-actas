<template>
    <div class="container h-100">
        <div class="row h-100 d-flex align-items-center">
            <div class="col-12">
                <Form v-on:newRecord="onNewRecord"></Form>
            </div>
        </div>
    </div>
</template>
<script>
    import Form from "./Form.vue";

    export default {
        name: "NewActionSchedule",
        components: {
            Form,
        },
        methods: {
            onNewRecord: function() {
                top.window.getPanel('iframe_action_modal').close();
            }
        }
    };
</script>
