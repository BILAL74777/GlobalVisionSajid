<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1 class="theme-text-color">{{ referral.name }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">TTC Global</a>
                        </li>
                        <li class="breadcrumb-item active">Referral</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">Transactions</h5>

                    <table class="table table-striped">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-2">Date</th>
                                <th class="border p-2">Cash In</th>
                                <th class="border p-2">Cash Out</th>
                                <th class="border p-2">Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(entry, index) in transactions" :key="index" class="text-center">
                                <td class="border p-2">{{ formatDate(entry.created_at) }}</td>
                                <td class="border p-2 text-green-600">
                                    {{ entry.cash_in ? entry.cash_in : '-' }}
                                </td>
                                <td class="border p-2 text-red-600">
                                    {{ entry.cash_out ? entry.cash_out : '-' }}
                                </td>
                                <td class="border p-2">
                                    {{ entry.refferal_commission ? entry.refferal_commission : '-' }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-100 font-bold">
                            <tr>
                                <td class="border p-2 text-center">Total</td>
                                <td class="border p-2 text-green-600">{{ totalCashIn }}</td>
                                <td class="border p-2 text-red-600">{{ totalCashOut }}</td>
                                <td class="border p-2 text-blue-600">{{ totalCommission }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import Master from "../Layout/Master.vue";

export default {
    layout: Master,
    props: ["transactions", "referral"],
    computed: {
        totalCashIn() {
            return this.transactions.reduce((sum, entry) => sum + parseFloat(entry.cash_in || 0), 0);
        },
        totalCashOut() {
            return this.transactions.reduce((sum, entry) => sum + parseFloat(entry.cash_out || 0), 0);
        },
        totalCommission() {
            return this.transactions.reduce((sum, entry) => sum + parseFloat(entry.refferal_commission || 0), 0);
        }
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString();
        }
    }
};
</script>

<style>
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
}
th {
    background-color: #f4f4f4;
}
tfoot {
    font-weight: bold;
    background-color: #f9f9f9;
}
</style>
