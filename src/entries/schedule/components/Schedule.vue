<template>
    <div class="container" style="height: 100vh; overflow-y:auto">
        <div class="row">
            <div class="col-12 col-md-6">
                <Form v-on:newRecord="onNewRecord"></Form>
            </div>
            <div class="col-12 col-md-6">
                <List></List>
            </div>
        </div>
    </div>
</template>
<script>
import Form from "./Form.vue";
import List from "./List.vue";

export default {
    name: "Schedule",
    components: {
        Form,
        List
    },
    methods: {
        onNewRecord: function() {
            this.$root.$emit("resfreshScheduleList");
        }
    },
    mounted: function() {
        this.$root.$emit("resfreshScheduleList");
    }
};
</script>
