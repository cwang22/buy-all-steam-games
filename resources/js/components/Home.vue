<template>

  <div class="mt-5" v-if="record">
    <p>Ever wonder how much does it cost to buy all games from Steam?</p>
    <p>Well, at the moment it costs <span class="text-danger">{{ record.sale }}</span> at a discounted
      price which costs <span
          class="text-danger">{{ record.original }}</span> at full price.</p>
    <p>This page was last updated {{ formatDistanceToNow(new Date(record.created_at)) }}.</p>
  </div>
  <h2 class="mt-5">Trends</h2>
  <Chart :records="records"></Chart>
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
      return this.records[0]
    },
  },
  methods: {formatDistanceToNow},
  mounted: async function () {
    const response = await fetch('/api/records')
    this.records = (await response.json()).data
  }

}
</script>

