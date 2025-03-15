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

        <!-- Filter Section -->
        <section class="section">
            <div class="card p-3">
                <div class="d-flex gap-2 align-items-center">
                    <!-- Year Multiselect -->
                    <Multiselect
                        v-model="selectedYear"
                        :options="years"
                        :searchable="true"
                        placeholder="Select Year"
                    />

                    <!-- Month Multiselect -->
                    <Multiselect
                        v-model="selectedMonth"
                        :options="months"
                        :searchable="true"
                        placeholder="Select Month"
                    />
                </div>
            </div>
        </section>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">Transactions</h5>

                    <table class="table table-striped">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-2">Date</th>
                                <th class="border p-2">Net Amount</th>
                                <th class="border p-2">Commission</th>
                                <th class="border p-2">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(entry, index) in filteredTransactions"
                                :key="index"
                                class="text-center"
                            >
                                <td class="border p-2">
                                    {{ formatDate(entry.created_at) }}
                                </td>
                                <td class="border p-2 text-green-600">
                                    {{ calculateNetAmount(entry) }}
                                </td>
                                <td class="border p-2">
                                    {{
                                        entry.referral_commission
                                            ? entry.referral_commission
                                            : "-"
                                    }}
                                </td>
                                <td class="border p-2 font-bold">
                                    {{
                                        entry.commission_amount
                                            ? entry.commission_amount
                                            : "-"
                                    }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-100 font-bold">
                            <tr>
                                <td class="border p-2 text-center">Total</td>
                                <td class="border p-2 text-green-600">
                                    {{ totalNetAmount }}
                                </td>
                                <td class="border p-2 text-blue-600">-</td>
                                <td class="border p-2 text-blue-600">
                                    {{ totalAmount }}
                                </td>
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
import Multiselect from "@vueform/multiselect"; // Import Multiselect component

export default {
    layout: Master,
    components: { Multiselect }, // Register Multiselect component
    props: ["transactions", "referral"],
    data() {
        return {
            selectedYear: new Date().getFullYear(),
            selectedMonth: new Date().getMonth() + 1,
        };
    },
    computed: {
        years() {
            const currentYear = new Date().getFullYear();
            return Array.from({ length: 10 }, (_, i) => currentYear - i);
        },
        months() {
            return [
                { value: 1, label: "January" },
                { value: 2, label: "February" },
                { value: 3, label: "March" },
                { value: 4, label: "April" },
                { value: 5, label: "May" },
                { value: 6, label: "June" },
                { value: 7, label: "July" },
                { value: 8, label: "August" },
                { value: 9, label: "September" },
                { value: 10, label: "October" },
                { value: 11, label: "November" },
                { value: 12, label: "December" },
            ];
        },
        filteredTransactions() {
            return this.transactions.filter((transaction) => {
                const transactionDate = new Date(transaction.created_at);
                return (
                    transactionDate.getFullYear() === this.selectedYear &&
                    transactionDate.getMonth() + 1 === this.selectedMonth
                );
            });
        },
        totalNetAmount() {
            return this.filteredTransactions.reduce(
                (sum, entry) =>
                    sum +
                    (parseFloat(entry.cash_in || 0) -
                        parseFloat(entry.cash_out || 0)),
                0
            );
        },
        totalCommission() {
            return this.filteredTransactions.reduce(
                (sum, entry) =>
                    sum + parseFloat(entry.referral_commission || 0),
                0
            );
        },
        totalAmount() {
            return this.filteredTransactions.reduce(
                (sum, entry) => sum + parseFloat(entry.commission_amount || 0),
                0
            );
        },
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },

        calculateNetAmount(entry) {
            return (
                parseFloat(entry.cash_in || 0) - parseFloat(entry.cash_out || 0)
            ).toFixed(2);
        },
    },
};
</script>

<style>
@import "@vueform/multiselect/themes/default.css"; /* Import Multiselect Styles */

table {
    width: 100%;
    border-collapse: collapse;
}
th,
td {
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
