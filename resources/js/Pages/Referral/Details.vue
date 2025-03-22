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
                                        <b class="text-success"
                                            >{{ entry.entry_type }} - Apply</b
                                        >
                                        <br />
                                        <b>{{ entry.visa.full_name }}</b>

                                        <!-- Show family members if entry type is 'Family' -->

                                        <div
                                            v-if="entry.entry_type === 'Family'"
                                        >
                                            <span
                                                v-for="(
                                                    familyMember, fmIndex
                                                ) in entry.familyMembers"
                                                :key="fmIndex"
                                            >
                                                <b>{{
                                                    familyMember.full_name
                                                }}</b>
                                            </span>
                                        </div></a
                                    >
                                </td>
                                <td class="border p-2">
                                    {{ entry.visa.phone_number }}
                                </td>
                                <td class="border p-2">
                                    {{ entry.visa.tracking_id }}
                                </td>
                                <td class="border p-2">
                                    <div v-if="entry.entry_type === 'Family'">
                                        {{
                                            sumFamilyActualAmount(
                                                entry.cash_in,
                                                entry.familyMembers
                                            )
                                        }}
                                    </div>
                                    <div v-else>
                                        {{ entry.cash_in || "-" }}
                                    </div>
                                </td>

                                <td class="border p-2 text-green-600">
                                    <div v-if="entry.entry_type === 'Family'">
                                        {{
                                            sumFamilyVisaFeeAmount(
                                                entry.visa,
                                                entry.familyMembers
                                            )
                                        }}
                                        {{
                                            calculateFamilyNetAmount(
                                                entry.cash_in,
                                                entry.familyMembers,
                                                entry
                                            )
                                        }}
                                    </div>
                                    <div v-else>
                                        {{ calculateNetAmount(entry) }}
                                    </div>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">
                            {{
                                selectedVisa.family_name ||
                                selectedVisa.full_name
                            }}
                            {{ selectedVisa.phone_number }}
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
                            <thead>
                                <tr>
                                    <th class="border p-2">#</th>
                                    <th class="border p-2">Status</th>
                                    <th class="border p-2">Date</th>
                                    <th class="border p-2">Full Name</th>
                                    <th class="border p-2">Phone Number</th>

                                    <th class="border p-2">Amount</th>
                                    <th class="border p-2">Visa Fee</th>
                                    <th class="border p-2">Gender</th>
                                </tr>
                            </thead>

                            <tbody v-if="selectedVisa.entry_type === 'Family'">
                                <!-- Main Visa Holder -->
                                <tr>
                                    <td class="border p-2">1</td>
                                    <td class="border p-2">
                                        {{ selectedVisa.status }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.date }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.full_name }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.phone_number }}
                                    </td>

                                    <td class="border p-2">
                                        {{ selectedVisa.amount || "N/A" }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.visa_fee }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.gender }}
                                    </td>
                                </tr>

                                <!-- Family Members -->
                                <tr
                                    v-if="
                                        selectedVisa.familyMembers.length === 0
                                    "
                                >
                                    <td
                                        colspan="7"
                                        class="border p-2 text-center"
                                    >
                                        No family members available
                                    </td>
                                </tr>

                                <tr
                                    v-for="(
                                        familyMember, index
                                    ) in selectedVisa.familyMembers"
                                    :key="familyMember.id"
                                >
                                    <td class="border p-2">{{ index + 2 }}</td>
                                    <td class="border p-2">
                                        {{ familyMember.status }}
                                    </td>
                                    <td class="border p-2">
                                        {{ familyMember.date }}
                                    </td>
                                    <td class="border p-2">
                                        {{ familyMember.full_name }}
                                    </td>
                                    <td class="border p-2">
                                        {{ familyMember.phone_number }}
                                    </td>

                                    <td class="border p-2">
                                        {{ familyMember.amount || "N/A" }}
                                    </td>
                                    <td class="border p-2">
                                        {{ familyMember.visa_fee }}
                                    </td>
                                    <td class="border p-2">
                                        {{ familyMember.gender }}
                                    </td>
                                </tr>
                            </tbody>

                            <!-- Non-Family Visa -->
                            <tbody v-else>
                                <tr>
                                    <td class="border p-2">1</td>
                                    <td class="border p-2">
                                        {{ selectedVisa.status }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.date }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.full_name }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.phone_number }}
                                    </td>

                                    <td class="border p-2">
                                        {{ selectedVisa.amount || "N/A" }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.visa_fee }}
                                    </td>
                                    <td class="border p-2">
                                        {{ selectedVisa.gender }}
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
            years: Array.from(
                { length: 10 },
                (_, i) => new Date().getFullYear() - i
            ),

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
            family_members: [],
        };
    },

    computed: {
        combinedTransactions() {
            return this.transactions.map((transaction) => {
                return {
                    ...transaction,
                    entry_type: transaction.visa.entry_type,
                    family_members:
                        transaction.visa.entry_type === "Family"
                            ? transaction.visa.familyMembers
                            : [],
                };
            });
        },

        // Ensure each transaction's own cash-in amount is correctly calculated
        totalActualAmount() {
            return this.combinedTransactions
                .reduce((sum, entry) => sum + parseFloat(entry.cash_in || 0), 0)
                .toFixed(2);
        },

        totalNetAmount() {
            return this.combinedTransactions
                .reduce((sum, entry) => sum + this.calculateNetAmount(entry), 0)
                .toFixed(2);
        },

        totalAmountReceived() {
            return this.combinedTransactions
                .reduce(
                    (sum, entry) =>
                        sum + this.calculateAmountAfterCommission(entry),
                    0
                )
                .toFixed(2);
        },
        computedActualAmount(entry) {
            if (entry.entry_type === "Family") {
                return entry.familyMembers.reduce((total, member) => {
                    return total + member.amount;
                }, entry.cash_in || 0);
            }
            return entry.cash_in || 0;
        },
    },

    methods: {
        openModal(entry) {
            console.log(entry);
            this.selectedVisa = { ...entry.visa };

            // Ensure familyMembers is properly assigned
            this.selectedVisa.familyMembers = entry.familyMembers || [];

            const modal = new bootstrap.Modal(
                document.getElementById("visaModal")
            );
            modal.show();
        },

        calculateNetAmount(entry) {
            // Ensure cash_in and visa_fee exist
            const cashIn = parseFloat(entry.cash_in || 0);
            const visaFee = parseFloat(entry.visa.visa_fee || 0);
            return cashIn - visaFee;
        },
        sumFamilyActualAmount(cashIn, familyMembers) {
            // Convert cashIn to a number, default to 0 if it's undefined
            let total = Number(cashIn) || 0;

            if (familyMembers && Array.isArray(familyMembers)) {
                total += familyMembers.reduce((sum, member) => {
                    return sum + (Number(member.amount) || 0); // Ensure number conversion
                }, 0);
            }

            return total.toFixed(2); // Format to two decimal places
        },
        sumFamilyVisaFeeAmount(visa, familyMembers) {
           
            let totalVisaFee = Number(visa.visa_fee) || 0; // Extract visa_fee from visa object

            if (familyMembers && Array.isArray(familyMembers)) {
                totalVisaFee += familyMembers.reduce((sum, member) => {
                    return sum + (Number(member.visa_fee) || 0); // Keep your existing logic for family members
                }, 0);
            }

        
            return totalVisaFee;
        },

        calculateFamilyNetAmount(familyMembers) {},

        calculateAmountAfterCommission(entry) {
            const netAmount = this.calculateNetAmount(entry);
            const commissionPercentage = parseFloat(
                entry.referral_commission || 0
            );
            return (netAmount * commissionPercentage) / 100;
        },
        // sumFamilyActualAmount(cashIn, familyAmount) {
        //     return parseFloat(cashIn || 0) + parseFloat(familyAmount || 0);
        // },
    },

    mounted() {
        this.$nextTick(() => {
            // Initialize any needed functionality
        });
    },
};
</script>
