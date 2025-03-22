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

                    <table id="ledgerTable" class="table table-striped">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border p-2">Full Name</th>
                                <th class="border p-2">Phone</th>
                                <th class="border p-2">Tracking ID</th>
                                <th class="border p-2">Actual Amount</th>
                                <th class="border p-2">Net Amount</th>
                                <th class="border p-2">
                                    Amount Referral Will Receive
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(entry, index) in combinedTransactions"
                                :key="index"
                                class="text-center"
                            >
                                <td>
                                    <a
                                        href="#"
                                        class="theme-text-color"
                                        @click="openModal(entry)"
                                    >
                                        {{ entry.visa.full_name }}
                                    </a>
                                    <!-- Show family members if entry type is 'Family' -->
                                    <div v-if="entry.entry_type === 'Family'">
                                        <span
                                            v-for="(familyMember, fmIndex) in entry.family_members"
                                            :key="fmIndex"
                                        >
                                            ({{ familyMember.full_name }})
                                        </span>
                                    </div>
                                </td>
                                <td class="border p-2">
                                    {{ entry.visa.phone_number }}
                                </td>
                                <td class="border p-2">
                                    {{ entry.visa.tracking_id }}
                                </td>
                                <td class="border p-2">
                                    {{ entry.cash_in || "-" }}
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
                                <td class="border p-2 text-center" colspan="3">
                                    Total
                                </td>
                                <td class="border p-2">
                                    {{ totalActualAmount }}
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
                            <tbody v-if="selectedVisa.entry_type === 'Family'">
                                <tr
                                    v-for="(record, index) in selectedFamilyRecords"
                                    :key="index"
                                >
                                    <td class="border p-2">{{ index + 1 }}</td>
                                    <td class="border p-2">
                                        {{ record.full_name }}
                                    </td>
                                    <td class="border p-2">
                                        {{ record.phone_number }}
                                    </td>
                                    <td class="border p-2">
                                        {{ record.tracking_id }}
                                    </td>
                                    <td class="border p-2">
                                        {{ record.amount || "N/A" }}
                                    </td>
                                    <td class="border p-2">
                                        {{ record.entry_type }}
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <th scope="row">Date</th>
                                    <td>{{ selectedVisa.date }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Referral commission</th>
                                    <td>
                                        {{ selectedVisa.referral_commission }}
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">Status</th>
                                    <td>{{ selectedVisa.status }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Actual Amount</th>
                                    <td>{{ selectedVisa.amount }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Visa Fee</th>
                                    <td>{{ selectedVisa.visa_fee }}</td>
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

        <!-- Modal for Family Records -->
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div class="bg-white p-5 rounded-lg shadow-lg w-3/4">
                <h2 class="text-lg font-bold mb-4">Family Visa Details</h2>

                <table
                    class="table-auto w-full border-collapse border border-gray-300"
                >
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border p-2">#</th>
                            <th class="border p-2">Name</th>
                            <th class="border p-2">Phone</th>
                            <th class="border p-2">Tracking ID</th>
                            <th class="border p-2">Amount</th>
                            <th class="border p-2">Entry Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(record, index) in selectedFamilyRecords"
                            :key="index"
                        >
                            <td class="border p-2">{{ index + 1 }}</td>
                            <td class="border p-2">{{ record.full_name }}</td>
                            <td class="border p-2">
                                {{ record.phone_number }}
                            </td>
                            <td class="border p-2">{{ record.tracking_id }}</td>
                            <td class="border p-2">
                                {{ record.amount || "N/A" }}
                            </td>
                            <td class="border p-2">{{ record.entry_type }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Close Button -->
                <button
                    @click="showModal = false"
                    class="mt-4 bg-red-500 text-white px-4 py-2 rounded"
                >
                    Close
                </button>
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
    props: ["referral", "transactions"],
    data() {
        return {
            selectedYear: new Date().getFullYear(),
            selectedMonth: new Date().getMonth() + 1,
            years: [2024, 2023, 2022, 2021], // Define years dynamically if needed
            months: [
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
            ],
            selectedVisa: {},
            dataTable: null,
            showModal: false,
            selectedFamilyRecords: [],
        };
    },

    computed: {
        // Combine transactions and related family members, adding entry_type to each
        combinedTransactions() {
            return this.transactions.map((transaction) => {
                const entry = {
                    ...transaction,
                    entry_type: transaction.visa.entry_type,
                    family_members:
                        transaction.visa.entry_type === "Family"
                            ? transaction.visa.familyMembers
                            : [],
                };
                return entry;
            });
        },

        // Calculate total of actual amounts
        totalActualAmount() {
            return this.combinedTransactions
                .reduce((sum, entry) => sum + parseFloat(entry.cash_in), 0)
                .toFixed(2);
        },

        // Calculate total net amount
        totalNetAmount() {
            return this.combinedTransactions
                .reduce(
                    (sum, entry) =>
                        sum +
                        (parseFloat(entry.cash_in) - parseFloat(entry.cash_out)),
                    0
                )
                .toFixed(2);
        },

        // Calculate total amount person will receive after commission
        totalAmountReceived() {
            return this.combinedTransactions
                .reduce(
                    (sum, entry) =>
                        sum +
                        parseFloat(this.calculateAmountAfterCommission(entry)),
                    0
                )
                .toFixed(2);
        },
    },

    methods: {
        openModal(entry) {
            this.selectedVisa = entry.visa;
            this.selectedFamilyRecords = entry.family_members.length > 0 
                ? [entry.visa, ...entry.family_members] 
                : [entry.visa];
            const modal = new bootstrap.Modal(
                document.getElementById("visaModal")
            );
            modal.show();
        },

        calculateNetAmount(entry) {
            const cashIn = parseFloat(entry.cash_in || 0);
            const cashOut = parseFloat(entry.cash_out || 0);
            return (cashIn - cashOut).toFixed(2);
        },

        calculateAmountAfterCommission(entry) {
            const cashIn = parseFloat(entry.cash_in || 0);
            const cashOut = parseFloat(entry.cash_out || 0);
            const netAmount = cashIn - cashOut;
            const commissionPercentage = parseFloat(entry.referral_commission || 0);
            const commissionAmount = (commissionPercentage / 100) * netAmount;

            return commissionAmount.toFixed(2);
        },
    },

    mounted() {
        this.$nextTick(() => {
            // Initialize any needed functionality
        });
    },
};
</script>
