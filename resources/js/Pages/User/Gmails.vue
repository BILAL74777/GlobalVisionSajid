<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>Gmails</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Global Vision</a></li>
                        <li class="breadcrumb-item active">Gmails</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body pt-4">
                    <div class="table-responsive">
                        <table id="gmailTable" class="table table-striped display">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Gmail</th>
                                    <th>Password</th>
                                    <th>Pak visa password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in tableData" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>{{ row.gmail }}</td>
                                    <td>{{ row.gmail_password }}</td>
                                    <td>{{ row.pak_visa_password }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import Master from "../Layout/Master.vue";
import axios from "axios";

export default {
    layout: Master,
    data() {
        return {
            tableData: [],
        };
    },
    mounted() {
        this.fetchGmails();
    },
    methods: {
        fetchGmails() {
            axios
                .get(route("api.gmails.fetch"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.tableData = response.data;

                    // Wait for DOM to update, then initialize DataTable
                    this.$nextTick(() => {
                        $('#gmailTable').DataTable();
                    });
                })
                .catch((error) => {
                    toastr.error(error.response?.data?.message || "Fetch failed");
                });
        },
    },
};
</script>

<style>
.table td,
.table th {
    vertical-align: middle;
}
</style>
