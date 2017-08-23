<template>
    <svg></svg>
</template>
<script>
    import * as d3 from 'd3'
    export default {
        props: ['records'],
        mounted() {
            const svg = d3.select("svg")
            const margin = {
                top: 20,
                right: 20,
                bottom: 60,
                left: 60
            }
            const width = parseInt(svg.style("width")) - margin.left - margin.right
            const height = width * 0.5625 - margin.top - margin.bottom

            svg.style("height", height + margin.top + margin.bottom);
            const g = svg.append("g").attr("transform", `translate(${margin.left}, ${margin.top})`)

            const parse = d3.timeParse("%Y-%m-%d %H:%M:%S")
            const x = d3.scaleTime()
                .rangeRound([0, width])
            const y = d3.scaleLinear()
                .rangeRound([height, 0])
            const line1 = d3.line()
                .curve(d3.curveMonotoneX)
                .x(d => x(d.date))
                .y(d => y(d.sale))

            const line2 = d3.line()
                .curve(d3.curveMonotoneX)
                .x(d => x(d.date))
                .y(d => y(d.original))

            const data = this.records.map((item) => {
                return {
                    date: parse(item.created_at),
                    sale: item.sale.replace(",", ""),
                    original: item.original.replace(",", "")
                }
            })

            x.domain(d3.extent(data, d => d.date))
            y.domain([d3.min(data, function (d) {
                return Math.min(d.sale, d.original);
            }) * 0.9, d3.max(data, function (d) {
                return Math.max(d.sale, d.original);
            }) * 1.1]);

            const xAxis = d3.axisBottom(x).ticks(d3.timeWeek)
            const yAxis = d3.axisLeft(y)

            g.append("g")
                .attr("class", "x axis")
                .attr("transform", `translate(0, ${height})`)
                .call(xAxis)

            g.append("g")
                .attr("class", "y axis")
                .call(yAxis)
                .append("text")
                .attr("y", 6)
                .attr("x", 80)
                .attr("dy", "1rem")
                .attr("fill", "#333")
                .text("Total Price ($)");

            g.append("path")
                .attr("class", "line-1")
                .attr("fill", "none")
                .attr("stroke", "tomato")
                .attr("stroke-linejoin", "round")
                .attr("stroke-linecap", "round")
                .attr("stroke-width", 1.5)
                .attr("d", line1(data))

            g.append("path")
                .attr("class", "line-2")
                .attr("fill", "none")
                .attr("stroke", "steelblue")
                .attr("stroke-linejoin", "round")
                .attr("stroke-linecap", "round")
                .attr("stroke-width", 1.5)
                .attr("d", line2(data))

            g.append("g").selectAll('dot')
                .data(data).enter()
                .append("circle")
                .attr("r", 3.5)
                .attr("cx", d => x(d.date))
                .attr("cy", d => y(d.sale))

            g.append("g").selectAll('dot')
                .data(data).enter()
                .append("circle")
                .attr("r", 3.5)
                .attr("cx", d => x(d.date))
                .attr("cy", d => y(d.original))
        }
    }
</script>
<style scoped>
    svg {
        width: 100%;
    }
</style>