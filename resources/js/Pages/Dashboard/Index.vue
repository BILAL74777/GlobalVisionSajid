<script>
import Master from "../Layout/Master.vue";
import { Chart, registerables } from "chart.js";
import Multiselect from "@vueform/multiselect";
import axios from "axios";

Chart.register(...registerables);

export default {
    layout: Master,
    components: { Multiselect },

    data() {
        return {
            // Raw transactions assembled from API
            transactionEntries: [],

            // KPI values
            cashIn: 0,
            cashOut: 0,
            balance: 0,

            // Approval / Rejection
            rates: { approval_rate: 0, rejection_rate: 0 },

            // Chart datasets (aggregated)
            last6MonthsData: [],
            last12MonthsData: [],

            // Filters
            selectedFilter: { value: 1, label: "Current Month" }, // default
            filterOptions: [
                { value: 1, label: "Current Month" },
                { value: 3, label: "Last 3 Months" },
                { value: 6, label: "Last 6 Months" },
                { value: 12, label: "Last 1 Year" },
                { value: "all", label: "Overall" },
            ],
            filterLabel: "Last 3 Months",

            // Chart instances
            barChart: null,
            approvalRejectionChart: null,
        };
    },

    created() {
        this.fetchApprovalRejectionRates();
    },

    mounted() {
        this.fetchTransactionEntries();
    },

    watch: {
        selectedFilter() {
            this.applyFilter();
        },
    },

    methods: {
        // ---- Helpers ----
        formatCurrency(value) {
            const n = Number(value || 0);
            return new Intl.NumberFormat("en-PK", {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(n);
        },

        // ---- API ----
        fetchTransactionEntries() {
            axios
                .get(route("api.dashboard.transaction.fetch"))
                .then((response) => {
                    const visaData = response.data.visas || [];
                    const familyVisaData = response.data.family_visas || [];

                    // Normalize
                    this.transactionEntries = [
                        ...visaData.map((v) => ({
                            transaction_date: v.date,
                            cash_in: parseFloat(v.amount) || 0,
                            cash_out: parseFloat(v.visa_fee) || 0,
                        })),
                        ...familyVisaData.map((fv) => ({
                            transaction_date: fv.date,
                            cash_in: parseFloat(fv.amount) || 0,
                            cash_out: parseFloat(fv.visa_fee) || 0,
                        })),
                    ];

                    this.applyFilter();
                })
                .catch((error) => {
                    console.error("Error fetching visa transactions:", error);
                    // Reset visuals safely
                    this.transactionEntries = [];
                    this.applyFilter();
                });
        },

        fetchApprovalRejectionRates() {
            axios
                .get("/api/approval-rejection-rate")
                .then((response) => {
                    this.rates = {
                        approval_rate: Number(
                            response.data?.approval_rate || 0
                        ),
                        rejection_rate: Number(
                            response.data?.rejection_rate || 0
                        ),
                    };
                    this.updateApprovalRejectionChart();
                })
                .catch((error) => {
                    console.error(
                        "Error fetching approval/rejection rates:",
                        error
                    );
                    this.rates = { approval_rate: 0, rejection_rate: 0 };
                    this.updateApprovalRejectionChart();
                });
        },

        // ---- Filters / Aggregation ----
        applyFilter() {
            const selected = this.selectedFilter?.value ?? this.selectedFilter;
            const isOverall = selected === "all";

            if (isOverall) {
                this.filterLabel = "Overall";
                this.calculateStats(this.transactionEntries);
            } else {
                const months = parseInt(selected, 10);
                this.filterLabel =
                    months === 1 ? "Current Month" : `Last ${months} Months`;
                // Date-range filter (inclusive)
                const now = new Date();
                const start = new Date(now);

                if (months === 1) {
                    // First day of current month
                    start.setDate(1);
                } else {
                    // Go back N months, start at month start
                    start.setMonth(now.getMonth() - months);
                    start.setDate(1);
                }

                const filtered = this.transactionEntries.filter((entry) => {
                    const d = new Date(entry.transaction_date);
                    return !isNaN(d) && d >= start && d <= now;
                });

                this.calculateStats(filtered);
            }

            this.updateBarChart();
        },

        calculateStats(filteredEntries) {
            // KPIs
            this.cashIn = filteredEntries.reduce(
                (sum, e) => sum + (parseFloat(e.cash_in) || 0),
                0
            );
            this.cashOut = filteredEntries.reduce(
                (sum, e) => sum + (parseFloat(e.cash_out) || 0),
                0
            );
            this.balance = this.cashIn - this.cashOut;

            // Aggregations
            this.last6MonthsData = this.groupDataByMonths(filteredEntries, 6);
            this.last12MonthsData = this.groupDataByMonths(filteredEntries, 12);
        },

        groupDataByMonths(entries, months) {
            const now = new Date();
            const formatMonth = new Intl.DateTimeFormat("en-US", {
                month: "short",
                year: "numeric",
            });

            // Seed buckets for the last N months (oldest -> newest)
            const buckets = [];
            for (let i = months - 1; i >= 0; i--) {
                const m = new Date(now);
                m.setDate(1);
                m.setMonth(now.getMonth() - i);
                buckets.push({
                    key: `${m.getFullYear()}-${m.getMonth() + 1}`,
                    label: formatMonth.format(m),
                    income: 0,
                    expense: 0,
                });
            }

            // Fill
            entries.forEach((e) => {
                const d = new Date(e.transaction_date);
                if (isNaN(d)) return;
                const key = `${d.getFullYear()}-${d.getMonth() + 1}`;
                const bucket = buckets.find((b) => b.key === key);
                if (bucket) {
                    bucket.income += parseFloat(e.cash_in) || 0;
                    bucket.expense += parseFloat(e.cash_out) || 0;
                }
            });

            return buckets.map(({ key, ...rest }) => rest); // strip key
        },

        // ---- Charts ----
        updateBarChart() {
            const ctx = document.getElementById("barChart")?.getContext("2d");
            if (!ctx) return;

            if (this.barChart) {
                this.barChart.destroy();
                this.barChart = null;
            }

            const selected = this.selectedFilter?.value ?? this.selectedFilter;
            const isOverall = selected === "all";

            if (isOverall) {
                // Overall totals
                const totalIn = this.transactionEntries.reduce(
                    (sum, e) => sum + (parseFloat(e.cash_in) || 0),
                    0
                );
                const totalOut = this.transactionEntries.reduce(
                    (sum, e) => sum + (parseFloat(e.cash_out) || 0),
                    0
                );

                this.barChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: ["Cash In", "Cash Out"],
                        datasets: [
                            {
                                label: "Overall",
                                data: [totalIn, totalOut],
                                backgroundColor: ["#28a745", "#dc3545"], // green, red
                            },
                        ],
                    },
                    options: this.barOptionsBasic("Transaction Type", "Amount"),
                });
            } else {
                const months = parseInt(selected, 10);
                const source =
                    months === 12
                        ? this.last12MonthsData
                        : this.last6MonthsData.slice(-months);

                this.barChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: source.map((x) => x.label),
                        datasets: [
                            {
                                label: "Cash In",
                                data: source.map((x) => x.income),
                                backgroundColor: "#28a745",
                            },
                            {
                                label: "Cash Out",
                                data: source.map((x) => x.expense),
                                backgroundColor: "#dc3545",
                            },
                            {
                                label: "Balance",
                                data: source.map((x) => x.income - x.expense),
                                backgroundColor: "#FFC107",
                            },
                        ],
                    },
                    options: this.barOptionsBasic("Month", "Amount", true),
                });
            }
        },

        updateApprovalRejectionChart() {
            const ctx = document
                .getElementById("approvalRejectionChart")
                ?.getContext("2d");
            if (!ctx) return;

            if (this.approvalRejectionChart) {
                this.approvalRejectionChart.destroy();
                this.approvalRejectionChart = null;
            }

            this.approvalRejectionChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ["Approval", "Rejection"],
                    datasets: [
                        {
                            label: "Approval vs Rejection Rates",
                            data: [
                                this.rates.approval_rate,
                                this.rates.rejection_rate,
                            ],
                            backgroundColor: ["#28a745", "#dc3545"],
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) =>
                                    `${ctx.label}: ${Number(ctx.raw || 0)}%`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: "Approval vs Rejection",
                            },
                        },
                        y: {
                            title: { display: true, text: "Rate (%)" },
                            beginAtZero: true,
                            suggestedMax: 100,
                        },
                    },
                },
            });
        },

        // Chart options preset
        barOptionsBasic(xTitle, yTitle, showLegend = false) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: showLegend, position: "top" },
                    tooltip: {
                        callbacks: {
                            label: (context) =>
                                `${
                                    context.dataset.label
                                }: ${this.formatCurrency(context.raw)}`,
                        },
                    },
                },
                scales: {
                    x: { title: { display: true, text: xTitle } },
                    y: {
                        title: { display: true, text: yTitle },
                        beginAtZero: true,
                    },
                },
            };
        },
    },
};
</script>

<template>
    <div>
        <main id="main" class="main">
            <!-- Page Title -->
            <div
                class="pagetitle d-flex align-items-center justify-content-between mb-4"
            >
                <div>
                    <h1 class="mb-1">Dashboard</h1>
                    <nav>
                        <ol class="breadcrumb pretty-breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="dashboard">Global Vision</a>
                            </li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </nav>
                </div>

                <!-- Filter -->
                <div class="filter-wrap" style="width: 300px">
                    <label class="filter-label">Filter By</label>
                    <Multiselect
                        v-model="selectedFilter"
                        :options="filterOptions"
                        :searchable="true"
                        label="label"
                        track-by="value"
                        placeholder="Select range"
                        class="msx"
                    />
                </div>
            </div>

            <section class="section dashboard">
                <div class="row g-4">
                    <!-- KPI Cards -->
                    <div class="col-xxl-4 col-md-4">
                        <div class="kpi-card hover-lift">
                            <div class="kpi-icon bg-success">
                                <i class="bi bi-cash text-white"></i>
                            </div>
                            <div class="kpi-body">
                                <span class="kpi-label">Cash In</span>
                                <div class="kpi-value">
                                    {{ formatCurrency(cashIn) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-md-4">
                        <div class="kpi-card hover-lift">
                            <div class="kpi-icon bg-danger">
                                <i class="bi bi-cash-coin text-white"></i>
                            </div>
                            <div class="kpi-body">
                                <span class="kpi-label">Cash Out</span>
                                <div class="kpi-value text-danger">
                                    {{ formatCurrency(cashOut) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-md-4">
                        <div class="kpi-card hover-lift">
                            <div
                                :class="[
                                    'kpi-icon',
                                    balance >= 0 ? 'bg-warning' : 'bg-danger',
                                ]"
                            >
                                <i class="bi bi-bank text-white"></i>
                            </div>
                            <div class="kpi-body">
                                <span class="kpi-label">Balance</span>
                                <div
                                    class="kpi-value"
                                    :class="
                                        balance >= 0
                                            ? 'text-warning'
                                            : 'text-danger'
                                    "
                                >
                                    {{ formatCurrency(balance) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="col-lg-8">
                        <div class="glass-card hover-lift h-100">
                            <div
                                class="card-header-lite d-flex align-items-center justify-content-between"
                            >
                                <h5 class="card-title m-0">
                                    Cash In vs Cash Out
                                    <span class="tag">{{ filterLabel }}</span>
                                </h5>
                                <span class="dots">
                                    <span></span><span></span><span></span>
                                </span>
                            </div>
                            <div class="chart-wrap">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="glass-card hover-lift h-100">
                            <div
                                class="card-header-lite d-flex align-items-center justify-content-between"
                            >
                                <h5 class="card-title m-0">
                                    Approval vs Rejection
                                </h5>
                                <i
                                    class="bi bi-graph-up-arrow text-primary-ink"
                                ></i>
                            </div>
                            <div class="chart-wrap small">
                                <canvas id="approvalRejectionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped>
@import "@vueform/multiselect/themes/default.css";

/* ============= Root brand ============= */
:root {
    --brand: #1c0d82; /* primary */
    --brand-ink: #0f0a4b; /* darker ink */
    --brand-soft: rgba(28, 13, 130, 0.08);
    --glass: rgba(255, 255, 255, 0.8);
    --shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
}

/* ============= Page Title ============= */
.title-gradient {
    font-weight: 800;
    letter-spacing: 0.2px;
    background: linear-gradient(90deg, var(--brand), #5a4ae3);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 0.25rem;
}
.pretty-breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}
.pretty-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: var(--brand);
}
.pretty-breadcrumb a {
    color: var(--brand);
    text-decoration: none;
}
.pretty-breadcrumb .active {
    color: var(--brand-ink);
    font-weight: 600;
}

/* ============= Filter ============= */
.filter-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fff;
    border-radius: 14px;
    padding: 0.6rem 0.8rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--brand-soft);
}
.filter-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--brand-ink);
    opacity: 0.8;
    white-space: nowrap;
}

/* Multiselect theme tweaks (on-brand) */
.msx :deep(.multiselect) {
    --ms-bg: #fff;
    --ms-border-color: var(--brand-soft);
    --ms-border-width: 1px;
    --ms-radius: 12px;
    --ms-ring-color: rgba(28, 13, 130, 0.25);
    --ms-option-bg-selected: var(--brand);
    --ms-option-color-selected: #fff;
    --ms-tag-bg: var(--brand);
    --ms-tag-color: #fff;
}

/* ============= KPI Cards ============= */
.kpi-card {
    display: flex;
    align-items: center;
    gap: 14px;
    background: #fff;
    border-radius: 16px;
    padding: 18px;
    box-shadow: var(--shadow);
    border: 1px solid var(--brand-soft);
    position: relative;
    overflow: hidden;
}
.kpi-card::after {
    content: "";
    position: absolute;
    right: -30px;
    top: -30px;
    width: 120px;
    height: 120px;
    background: radial-gradient(
        closest-side,
        rgba(28, 13, 130, 0.12),
        transparent 60%
    );
    transform: rotate(25deg);
}
.kpi-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08);
}
.kpi-body .kpi-label {
    display: block;
    font-size: 0.82rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}
.kpi-value {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--brand-ink);
    letter-spacing: 0.3px;
}

/* ============= Cards (Charts) ============= */
.glass-card {
    background: var(--glass);
    backdrop-filter: saturate(180%) blur(6px);
    border-radius: 16px;
    border: 1px solid var(--brand-soft);
    box-shadow: var(--shadow);
}
.card-header-lite {
    padding: 16px;
    border-bottom: 1px dashed var(--brand-soft);
}
.card-title {
    color: var(--brand-ink);
    font-weight: 700;
}
.text-primary-ink {
    color: var(--brand-ink);
}
.tag {
    display: inline-block;
    margin-left: 0.5rem;
    padding: 0.2rem 0.5rem;
    font-size: 0.72rem;
    border-radius: 999px;
    color: var(--brand-ink);
    background: rgba(28, 13, 130, 0.08);
    border: 1px solid rgba(28, 13, 130, 0.14);
}
.dots {
    display: inline-flex;
    gap: 4px;
}
.dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--brand);
    opacity: 0.35;
    animation: pulse 1.8s infinite ease-in-out;
}
.dots span:nth-child(2) {
    animation-delay: 0.25s;
}
.dots span:nth-child(3) {
    animation-delay: 0.5s;
}
@keyframes pulse {
    0%,
    100% {
        transform: scale(1);
        opacity: 0.35;
    }
    50% {
        transform: scale(1.25);
        opacity: 0.7;
    }
}

/* ============= Chart area ============= */
.chart-wrap {
    width: 100%;
    height: 380px;
    padding: 12px 16px 18px 16px;
}
.chart-wrap.small {
    height: 320px;
}
canvas {
    width: 100% !important;
    height: 100% !important;
}

/* ============= Interactive ============= */
.hover-lift {
    transform: translateY(0);
    transition: transform 0.18s ease, box-shadow 0.18s ease,
        border-color 0.18s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(16, 24, 40, 0.12);
    border-color: rgba(28, 13, 130, 0.22);
}

/* ============= Utilities ============= */
.bg-success {
    background-color: #22c55e !important;
} /* softer green */
.bg-danger {
    background-color: #ef4444 !important;
} /* softer red */
.text-success {
    color: #16a34a !important;
}
.text-danger {
    color: #dc2626 !important;
}
</style>
