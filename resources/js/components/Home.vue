<template>
  <div v-if="!record" class="mt-5 text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="d-none">Loading...</span>
      </div>
  </div>
  <div class="mt-5" v-else-if="record">
    <p>Ever wonder how much does it cost to buy all games from Steam?</p>
    <p>Well, at the moment it costs <span class="text-danger font-weight-bold">${{ record.sale }}</span> at a discounted
      price which costs <span
          class="text-danger font-weight-bold">${{ record.original }}</span> at full price.</p>
    <p class="text-muted"><small>This page was last updated {{ formatDistanceToNow(new Date(record.created_at)) }}.</small></p>
  </div>
  <h2 class="mt-5">Trends</h2>
  <div v-if="!records.length" class="mt-5 text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="d-none">Loading...</span>
      </div>
  </div>
  <Chart v-if="records.length" :records="records"></Chart>
</template>

<script>
import { formatDistanceToNow } from 'date-fns'
import Chart from './Chart.vue'


export default {
  name: "Home",
  components: {
    Chart
  },
  data() {
    return {
      records: [],
    }
  },
  computed: {
    record() {
      if (this.records.length > 0) {
        return this.records[this.records.length - 1]
      }

      return null
    },
  },
  methods: {formatDistanceToNow},
  mounted: async function () {
    const response = await fetch('/api/records')
    this.records = (await response.json()).data
  }

}
</script>

