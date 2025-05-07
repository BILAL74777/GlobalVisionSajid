<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>Gmails</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">Global Vision</a>
                        </li>
                        <li class="breadcrumb-item active">Gmails</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body pt-4">
                    <div class="table-responsive">
                        <table
                            id="gmailTable"
                            class="table table-striped display"
                        >
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
            dataTable: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.fetchGmails();
        });
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

                    // Destroy DataTable if it already exists
                    if (this.dataTable) {
                        this.dataTable.destroy();
                        this.dataTable = null;
                    }

                    // Reinitialize the DataTable after DOM update
                    this.$nextTick(() => {
                        this.initializeDataTable();
                    });
                })
                .catch((error) => {
                    toastr.error(
                        error.response?.data?.message || "Fetch failed"
                    );
                });
        },
        initializeDataTable() {
    this.$nextTick(() => {
        this.dataTable = $("#gmailTable").DataTable({
            responsive: true,
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: true,
            lengthMenu: [15, 30, 100],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous",
                },
            },
            columnDefs: [
                {
                    targets: 0, // First column
                    searchable: false,
                    orderable: false,
                },
            ],
            order: [[1, 'asc']], // Optional: default sort by Gmail
            drawCallback: function (settings) {
                const api = this.api();
                api.column(0, { page: 'current' }).nodes().each((cell, i) => {
                    cell.innerHTML = i + 1;
                });
            },
        });
    });
}

    },
};
</script>

<style>
.table td,
.table th {
    vertical-align: middle;
}
</style>
