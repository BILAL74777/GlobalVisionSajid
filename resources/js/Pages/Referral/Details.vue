<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1 class="theme-text-color">{{ referral.name }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">Global Vision</a>
                        </li>
                        <li class="breadcrumb-item">Referral</li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="section">
            <div class="card p-3">
                <div class="d-flex gap-2 align-items-center">
                    <Multiselect
                        v-model="selectedYear"
                        :options="years"
                        :searchable="true"
                        placeholder="Select Year"
                    />
                    <Multiselect
                        v-model="selectedMonth"
                        :options="months"
                        :searchable="true"
                        placeholder="Select Month"
                        label="label"
                        track-by="value"
                    />
                </div>
            </div>
        </section>

        <!-- Transactions Section -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">Transactions</h5>
                    <div class="table-responsive">
                        <table
                            class="table table-striped"
                            id="transactionsTable"
                        >
                            <thead>
                                <tr>
                                    <th>Transaction Type</th>
                                    <th>Phone #</th>
                                    <th>Tracking ID</th>
                                    <th>Actual Amount</th>
                                    <th>Visa Fee</th>
                                    <th>System Amount</th>
                                    <th>Referral Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="group in filteredGroupedData"
                                    :key="group.parent_id"
                                >
                                    <td
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"
                                        @click="showDetails(group)"
                                        style="
                                            font-weight: bold;
                                            text-align: left;
                                            cursor: pointer;
                                        "
                                    >
                                        <div
                                            v-if="
                                                group.transactions[0].visa &&
                                                group.transactions[0].visa
                                                    .entry_type === 'Family'
                                            "
                                        >
                                            <span>{{
                                                group.transactions[0].visa
                                                    .family_name
                                            }}</span>
                                            <div style="color: green">
                                                <!-- Show main applicant -->
                                                <b>
                                                    {{
                                                        group.transactions[0]
                                                            .visa.full_name
                                                    }}</b
                                                >
                                                <!-- Show family members -->
                                                <div
                                                    v-for="member in group
                                                        .transactions[0]
                                                        .familyMembers"
                                                    :key="member.id"
                                                >
                                                    <b>
                                                        {{
                                                            member.full_name
                                                        }}</b
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>
                                            Individual
                                            <br />
                                            <b style="color: green">
                                                {{
                                                    group.transactions[0].visa
                                                        ?.full_name
                                                }}
                                            </b>
                                        </div>
                                    </td>

                                    <td>
                                        {{
                                            group.transactions[0].visa
                                                .phone_number
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            group.transactions[0].visa
                                                .tracking_id
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            formatCurrency(group.total_cash_in)
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            formatCurrency(group.total_cash_out)
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            formatCurrency(
                                                group.total_commission_amount
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{ formatCurrency(systemFee(group)) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="3"><strong>Total</strong></td>
                                    <td>{{ formatCurrency(totalCashIn) }}</td>
                                    <td>{{ formatCurrency(totalCashOut) }}</td>
                                    
                                    <td>
                                        {{ formatCurrency(totalSystemAmount) }}
                                    </td>

                                    <td>
                                        {{
                                            formatCurrency(
                                                totalCommissionAmount
                                            )
                                        }}
                                    </td>
                                    
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modal for displaying detailed records -->

        <div
            class="modal fade"
            id="exampleModal"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h5 class="modal-title">Visa Record Details</h5> -->

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div
                        class="modal-body"
                        v-if="transactionDetails?.familyMembers?.length"
                    >
                        <div class="row">
                            <!-- Check if there is only one member, use full-width card -->
                            <h4 class="text-success">
                                {{
                                    transactionDetails.family_name ??
                                    "Individual Apply"
                                }}
                            </h4>
                            <div
                                v-if="
                                    transactionDetails.familyMembers.length ===
                                    1
                                "
                                class="col-12"
                            >
                                <div class="card">
                                    <div class="card-body p-3">
                                        <p>
                                            <strong>Full Name:</strong>
                                            {{
                                                transactionDetails
                                                    .familyMembers[0].full_name
                                            }}
                                        </p>
                                        <p>
                                            <strong>Phone:</strong>
                                            {{
                                                transactionDetails
                                                    .familyMembers[0]
                                                    .phone_number
                                            }}
                                        </p>
                                        <p>
                                            <strong>Tracking ID:</strong>
                                            {{
                                                transactionDetails
                                                    .familyMembers[0]
                                                    .tracking_id
                                            }}
                                        </p>
                                        <p>
                                            <strong>Status:</strong>
                                            {{
                                                transactionDetails
                                                    .familyMembers[0].status
                                            }}
                                        </p>
                                        <p>
                                            <strong>Amount:</strong>
                                            {{
                                                formatCurrency(
                                                    transactionDetails
                                                        .familyMembers[0].amount
                                                )
                                            }}
                                        </p>
                                        <p>
                                            <strong>Visa Fee:</strong>
                                            {{
                                                formatCurrency(
                                                    transactionDetails
                                                        .familyMembers[0]
                                                        .visa_fee
                                                )
                                            }}
                                        </p>
                                        <p>
                                            <strong>Gmail:</strong>
                                            {{
                                                transactionDetails
                                                    .familyMembers[0].gmail
                                            }}
                                        </p>
                                        <p>
                                            <strong>Pak Visa Password:</strong>
                                            {{
                                                transactionDetails
                                                    .familyMembers[0]
                                                    .pak_visa_password
                                            }}
                                        </p>
                                        <p>
                                            <strong>Gender:</strong>
                                            {{
                                                transactionDetails
                                                    .familyMembers[0].gender
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- If there are two members, show them side by side in 6-6 columns -->
                            <div
                                v-else-if="
                                    transactionDetails.familyMembers.length ===
                                    2
                                "
                                class="col-md-6"
                                v-for="(
                                    member, index
                                ) in transactionDetails.familyMembers"
                                :key="member.id"
                            >
                                <div class="card">
                                    <div class="card-header text-success">
                                        <b>Member #{{ index + 1 }}</b>
                                    </div>
                                    <div class="card-body p-3">
                                        <p>
                                            <strong>Full Name:</strong>
                                            {{ member.full_name }}
                                        </p>
                                        <p>
                                            <strong>Phone:</strong>
                                            {{ member.phone_number }}
                                        </p>
                                        <p>
                                            <strong>Tracking ID:</strong>
                                            {{ member.tracking_id }}
                                        </p>
                                        <p>
                                            <strong>Status:</strong>
                                            {{ member.status }}
                                        </p>
                                        <p>
                                            <strong>Amount:</strong>
                                            {{ formatCurrency(member.amount) }}
                                        </p>
                                        <p>
                                            <strong>Visa Fee:</strong>
                                            {{
                                                formatCurrency(member.visa_fee)
                                            }}
                                        </p>
                                        <p>
                                            <strong>Gmail:</strong>
                                            {{ member.gmail }}
                                        </p>
                                        <p>
                                            <strong>Pak Visa Password:</strong>
                                            {{ member.pak_visa_password }}
                                        </p>
                                        <p>
                                            <strong>Gender:</strong>
                                            {{ member.gender }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- If there are more than two members, display them in a 6-column layout per card -->
                            <div
                                v-else
                                class="col-md-6"
                                v-for="(
                                    member, index
                                ) in transactionDetails.familyMembers"
                                :key="member.id"
                            >
                                <div class="card">
                                    <div class="card-header text-success">
                                        <b>Member #{{ index + 1 }}</b>
                                    </div>
                                    <div class="card-body p-3">
                                        <p>
                                            <strong>Full Name:</strong>
                                            {{ member.full_name }}
                                        </p>
                                        <p>
                                            <strong>Phone:</strong>
                                            {{ member.phone_number }}
                                        </p>
                                        <p>
                                            <strong>Tracking ID:</strong>
                                            {{ member.tracking_id }}
                                        </p>
                                        <p>
                                            <strong>Status:</strong>
                                            {{ member.status }}
                                        </p>
                                        <p>
                                            <strong>Amount:</strong>
                                            {{ formatCurrency(member.amount) }}
                                        </p>
                                        <p>
                                            <strong>Visa Fee:</strong>
                                            {{
                                                formatCurrency(member.visa_fee)
                                            }}
                                        </p>
                                        <p>
                                            <strong>Gmail:</strong>
                                            {{ member.gmail }}
                                        </p>
                                        <p>
                                            <strong>Pak Visa Password:</strong>
                                            {{ member.pak_visa_password }}
                                        </p>
                                        <p>
                                            <strong>Gender:</strong>
                                            {{ member.gender }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import Master from "../Layout/Master.vue";
import Multiselect from "@vueform/multiselect";

export default {
    layout: Master,
    components: { Multiselect },
    props: ["referral", "transactions", "groupedData"],
    data() {
        return {
            transactionDetails: null,
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
            neatData: [],
        };
    },
    mounted() {
        // Initialize DataTable after the component is mounted
        this.$nextTick(() => {
            $("#transactionsTable").DataTable({
                responsive: true,
                autoWidth: false,
                paging: true,
                searching: true,
                ordering: true,
                lengthMenu: [15, 30, 100], // Customize the number of rows to display
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
            });
        });
    },
    methods: {
        formatCurrency(amount) {
            return ` ${parseFloat(amount).toFixed(2)}`;
        },
        showDetails(group) {
            const visa = group.transactions[0].visa;
            if (!visa) return;

            // Combine primary applicant with family members
            this.transactionDetails = {
                ...visa,
                familyMembers: [visa, ...group.transactions[0].familyMembers],
            };
        },
        systemFee(group) {
            const cashIn = Number(group?.total_cash_in ?? 0);
            const cashOut = Number(group?.total_cash_out ?? 0);
            const referral = Number(group?.total_commission_amount ?? 0);
            return cashIn - cashOut - referral;
        },
    },
    computed: {
        filteredGroupedData() {
            return this.groupedData.filter((group) => {
                const d = group.transactions?.[0]?.visa?.date;
                if (!d) return false;
                const date = new Date(d);
                return (
                    date.getFullYear() === this.selectedYear &&
                    date.getMonth() + 1 === this.selectedMonth
                );
            });
        },
        totalCashIn() {
            return this.filteredGroupedData.reduce(
                (total, g) => total + Number(g.total_cash_in ?? 0),
                0
            );
        },
        totalCashOut() {
            return this.filteredGroupedData.reduce(
                (total, g) => total + Number(g.total_cash_out ?? 0),
                0
            );
        },
        totalCommissionAmount() {
            return this.filteredGroupedData.reduce(
                (total, g) => total + Number(g.total_commission_amount ?? 0),
                0
            );
        },
        totalSystemAmount() {
            return (
                this.totalCashIn -
                this.totalCashOut -
                this.totalCommissionAmount
            );
        },
    },
};
</script>

<style scoped>
/* Optionally, add styling for the DataTable if necessary */
#transactionsTable {
    width: 100%;
    margin: 20px 0;
}

#transactionsTable th,
#transactionsTable td {
    text-align: center;
}
.dataTables_empty {
    display: none !important;
}
</style>
