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
  
      <!-- Transactions Section -->
      <section class="section">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title theme-text-color">Transactions</h5>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Transaction Type</th>
                  <th>Phone #</th>
                  <th>Tracking ID</th>
                  <th>Actual Amount</th>
                  <th>Net Amount</th>
                  <th>Referral Percentage</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="group in groupedData" :key="group.parent_id">
                  <td @click="showDetails(group)">
                    {{ group.transactions.length > 1 ? 'Family' : 'Individual' }}
                  </td>
                  <td>{{ group.transactions[0].visa.phone_number }}</td>
                  <td>{{ group.transactions[0].visa.tracking_id }}</td>
                  <td>{{ formatCurrency(group.total_cash_in) }}</td>
                  <td>{{ formatCurrency(group.total_cash_out) }}</td>
                  <td>{{ formatCurrency(group.total_commission_amount) }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"><strong>Total</strong></td>
                  <td>{{ formatCurrency(totalCashIn) }}</td>
                  <td>{{ formatCurrency(totalCashOut) }}</td>
                  <td>{{ formatCurrency(totalCommissionAmount) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </section>
  
      <!-- Modal for displaying detailed records -->
      <div v-if="showModal" class="modal" tabindex="-1" style="display:block;">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Visa Record Details</h5>
              <button type="button" class="btn-close" @click="showModal = false"></button>
            </div>
            <div class="modal-body">
              <div v-if="transactionDetails.entry_type === 'Individual'">
                <p><strong>Full Name:</strong> {{ transactionDetails.full_name }}</p>
                <p><strong>Phone:</strong> {{ transactionDetails.phone_number }}</p>
                <p><strong>Tracking ID:</strong> {{ transactionDetails.tracking_id }}</p>
                <p><strong>Status:</strong> {{ transactionDetails.status }}</p>
                <p><strong>Amount:</strong> {{ formatCurrency(transactionDetails.amount) }}</p>
                <p><strong>Visa Fee:</strong> {{ formatCurrency(transactionDetails.visa_fee) }}</p>
                <p><strong>Gmail:</strong> {{ transactionDetails.gmail }}</p>
                <p><strong>Gender:</strong> {{ transactionDetails.gender }}</p>
              </div>
              <div v-else>
                <div v-for="member in transactionDetails.familyMembers" :key="member.id">
                  <p><strong>Full Name:</strong> {{ member.full_name }}</p>
                  <p><strong>Phone:</strong> {{ member.phone_number }}</p>
                  <p><strong>Tracking ID:</strong> {{ member.tracking_id }}</p>
                  <p><strong>Status:</strong> {{ member.status }}</p>
                  <p><strong>Amount:</strong> {{ formatCurrency(member.amount) }}</p>
                  <p><strong>Visa Fee:</strong> {{ formatCurrency(member.visa_fee) }}</p>
                  <p><strong>Gmail:</strong> {{ member.gmail }}</p>
                  <p><strong>Gender:</strong> {{ member.gender }}</p>
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
        showModal: false,
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
      };
    },
    methods: {
      formatCurrency(amount) {
        return `Rs ${parseFloat(amount).toFixed(2)}`;
      },
      showDetails(group) {
        this.transactionDetails = group.transactions[0].visa;
        if (group.transactions[0].visa.entry_type === "Family") {
          this.transactionDetails.familyMembers = group.transactions[0].familyMembers;
        }
        this.showModal = true;
      },
    },
    computed: {
      totalCashIn() {
        return this.groupedData.reduce((total, group) => total + group.total_cash_in, 0);
      },
      totalCashOut() {
        return this.groupedData.reduce((total, group) => total + group.total_cash_out, 0);
      },
      totalCommissionAmount() {
        return this.groupedData.reduce((total, group) => total + group.total_commission_amount, 0);
      },
    },
  };
  </script>
  