<template>
    <main id="main" class="main">
        <!-- <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1 class="theme-text-color">ABC</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">TTC Global Tech</a>
                        </li>
                        <li class="breadcrumb-item">ABC</li>
                        <li class="breadcrumb-item active">ABC</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button
                    class="btn btn-success mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#studentmodal"
                >
                    <i class="bi bi-plus-lg"></i> Button
                </button>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">Contents</h5>
                     
                </div>
            </div>
        </section> -->
    </main>
</template>

<script>
import Master from "Layout/Master.vue";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import axios from "axios";
export default {
    layout: Master,
    props: [],
    components: {
        Multiselect,
        Datepicker,
    },
  
    created() {
       // this.function();
    },
    setup() {
        return {};
    },
    methods: {
        function() {
            axios
                .get(route("api.abc", id), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.ABC = response.data;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
    },
};
</script>

<style lang="scss" scoped></style>
