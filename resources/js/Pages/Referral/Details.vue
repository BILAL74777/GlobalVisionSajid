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
                        <li class="breadcrumb-item">Referral</li>
                        <li class="breadcrumb-item active">Details</li>
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
                                <th class="border p-2">Details</th>
                                <th class="border p-2">Date</th>
                                <th class="border p-2">Actual Amount</th>
                                <th class="border p-2">Commission (%)</th>
                                <th class="border p-2">Net Amount</th>
                                <th class="border p-2">
                                    Amount Referral Will Receive
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(entry, index) in filteredTransactions"
                                :key="index"
                                class="text-center"
                            >
                                <td>
                                    <a
                                        href="#"
                                        class="theme-text-color"
                                        @click="openModal(entry.visa)"
                                    >
                                        <i class="bi bi-list"></i>
                                    </a>
                                </td>
                                <td class="border p-2">
                                    {{ formatDate(entry.created_at) }}
                                </td>
                                <td class="border p-2">
                                    {{ entry.cash_in || "-" }}
                                </td>
                                <td class="border p-2">
                                    {{ entry.referral_commission || "-" }}
                                </td>
                                <td class="border p-2 text-green-600">
                                    {{ calculateNetAmount(entry) }}
                                </td>
                                <td class="border p-2 font-bold text-blue-600">
                                    {{ calculateAmountAfterCommission(entry) }}
                                </td>
                            </tr>
                        </tbody>

                        <tfoot class="bg-gray-100 font-bold">
                            <tr>
                                <td class="border p-2 text-center" colspan="2">
                                    Total
                                </td>
                                <td class="border p-2">
                                    {{ totalActualAmount }} 
                                </td>
                                <td class="border p-2">
                                    -
                                </td>
                                <td class="border p-2 text-green-600">
                                    {{ totalNetAmount }}
                                </td>
                                <td class="border p-2 text-blue-600">
                                    {{ totalAmountReceived }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <!-- Bootstrap Modal -->
        <div
            class="modal fade"
            id="visaModal"
            tabindex="-1"
            aria-labelledby="modalTitle"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">
                            {{ selectedVisa.full_name }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">Phone Number</th>
                                    <td>{{ selectedVisa.phone_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>{{ selectedVisa.status }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Amount</th>
                                    <td>{{ selectedVisa.amount }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Visa Fee</th>
                                    <td>{{ selectedVisa.visa_fee }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Tracking ID</th>
                                    <td>{{ selectedVisa.tracking_id }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>{{ selectedVisa.gmail }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Gender</th>
                                    <td>{{ selectedVisa.gender }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Date</th>
                                    <td>{{ selectedVisa.date }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Pak Visa Password</th>
                                    <td>
                                        {{ selectedVisa.pak_visa_password }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
            selectedVisa: {},
        };
    },
    computed: {
    // Get the filtered transactions for the selected month and year
    filteredTransactions() {
        return this.transactions.filter((transaction) => {
            const transactionDate = new Date(transaction.created_at);
            return (
                transactionDate.getFullYear() === this.selectedYear &&
                transactionDate.getMonth() + 1 === this.selectedMonth
            );
        });
    },

    // Calculate total of actual amounts
    totalActualAmount() {
        return this.filteredTransactions
            .reduce((sum, entry) => {
                // Ensure entry.amount is a valid number before adding
                const amount = parseFloat(entry.cash_in);
                return sum + (isNaN(amount) ? 0 : amount);
            }, 0)
            .toFixed(2); // Rounded to two decimals
    },

    // Calculate total net amount
    totalNetAmount() {
        return this.filteredTransactions
            .reduce(
                (sum, entry) =>
                    sum +
                    (parseFloat(entry.cash_in || 0) - parseFloat(entry.cash_out || 0)),
                0
            )
            .toFixed(2); // Rounded to two decimals
    },

    // Calculate total amount person will receive after commission
    totalAmountReceived() {
        return this.filteredTransactions
            .reduce(
                (sum, entry) =>
                    sum +
                    parseFloat(this.calculateAmountAfterCommission(entry)),
                0
            )
            .toFixed(2); // Rounded to two decimals
    },
}
,

    methods: {
        openModal(visa) {
            this.selectedVisa = visa;
            const modal = new bootstrap.Modal(
                document.getElementById("visaModal")
            );
            modal.show();
        },

        // Format the date for display
        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },
    // Calculate Net Amount: This calculates the difference between cash_in and cash_out
    calculateNetAmount(entry) {
        const cashIn = parseFloat(entry.cash_in || 0);
        const cashOut = parseFloat(entry.cash_out || 0);
        return (cashIn - cashOut).toFixed(2);  // Rounded to two decimals
    },

    // Calculate Amount Person Will Receive after commission
    calculateAmountAfterCommission(entry) {
    const cashIn = parseFloat(entry.cash_in || 0);
    const cashOut = parseFloat(entry.cash_out || 0);
    const netAmount = cashIn - cashOut;
    const commissionPercentage = parseFloat(entry.referral_commission || 0);
    const commissionAmount = (commissionPercentage / 100) * netAmount;

    return commissionAmount.toFixed(2);
}
,
    // Footer Computation of Total Actual Amount
    totalActualAmount() {
        return this.filteredTransactions.reduce(
            (sum, entry) => sum + parseFloat(entry.amount || 0),
            0
        ).toFixed(2);  // Rounded to two decimals
    },

     

    // Footer Computation of Total Net Amount
    totalNetAmount() {
        return this.filteredTransactions.reduce(
            (sum, entry) => sum + (parseFloat(entry.cash_in || 0) - parseFloat(entry.cash_out || 0)),
            0
        ).toFixed(2);  // Rounded to two decimals
    },

    // Footer Computation of Total Amount Person Will Receive
    totalAmountReceived() {
        return this.filteredTransactions.reduce(
            (sum, entry) => sum + parseFloat(this.calculateAmountAfterCommission(entry)),
            0
        ).toFixed(2);  // Rounded to two decimals
    }
}
,
};
</script>

<style>
@import "@vueform/multiselect/themes/default.css"; /* Import Multiselect Styles */

table {
    width: 100%;
    border-collapse: collapse;
}
.card th,
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
