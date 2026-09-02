<template>
  <div class="overflow-x-auto">
    <div ref="containerRef" class="chart-area shadow-xl bg-[#4a76c8] rounded-2xl p-4 sm:p-10 relative">
      <svg class="connecting-lines" :width="svgSize.width" :height="svgSize.height">
        <defs>
          <marker id="orgArrow" markerHeight="6" markerWidth="6" orient="auto-start-reverse" refX="5" refY="5" viewBox="0 0 10 10">
            <path d="M 0 0 L 10 5 L 0 10 z" />
          </marker>
        </defs>
        <line
          v-for="(l, i) in lines"
          :key="'l' + i"
          :x1="l.x1" :y1="l.y1" :x2="l.x2" :y2="l.y2"
          :marker-end="l.markerEnd ? 'url(#orgArrow)' : null"
          :marker-start="l.markerStart ? 'url(#orgArrow)' : null"
        />
        <path
          v-for="(p, i) in paths"
          :key="'p' + i"
          :d="p.d"
          :marker-end="p.markerEnd ? 'url(#orgArrow)' : null"
          :marker-start="p.markerStart ? 'url(#orgArrow)' : null"
        />
      </svg>

      <div ref="contentRef" class="relative z-10 w-full flex flex-col items-center min-w-[760px]">
        <!-- Level 1 -->
        <div class="chart-row">
          <div :ref="setNodeRef('diocese')" class="org-node">{{ $t('orgChart.diocese') }}</div>
        </div>

        <!-- Level 2 -->
        <div class="chart-row flex justify-between items-center w-full px-4" style="margin-top: -10px;">
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('dioceseRep')" class="org-node">{{ $t('orgChart.dioceseRep') }}</div>
          </div>
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('director')" class="org-node" style="margin-top: 20px;">{{ $t('orgChart.director') }}</div>
          </div>
          <div class="w-1/3 flex justify-center flex-col items-center gap-6" style="margin-top: -40px;">
            <div :ref="setNodeRef('parentCouncil')" class="org-node org-node-small">{{ $t('orgChart.parentCouncil') }}</div>
            <div :ref="setNodeRef('internalAuditors')" class="org-node org-node-small" style="margin-left: 20px;">{{ $t('orgChart.internalAuditors') }}</div>
          </div>
        </div>

        <!-- Level 3 -->
        <div class="chart-row flex justify-between w-full px-4" style="margin-top: 10px;">
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('secretary')" class="org-node">{{ $t('orgChart.secretary') }}</div>
          </div>
          <div class="w-1/3"></div>
          <div class="w-1/3"></div>
        </div>

        <!-- Level 4 -->
        <div class="chart-row flex justify-between w-full px-8" style="margin-top: 20px;">
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('staffStudies')" class="org-node">{{ $t('orgChart.staffStudies') }}</div>
          </div>
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('staffDiscipline')" class="org-node">{{ $t('orgChart.staffDiscipline') }}</div>
          </div>
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('accountant')" class="org-node">{{ $t('orgChart.accountant') }}</div>
          </div>
        </div>

        <!-- Level 5 -->
        <div class="chart-row flex justify-between w-full px-8">
          <div class="w-1/3 flex justify-around">
            <div :ref="setNodeRef('headOfDept')" class="org-node org-node-small">{{ $t('orgChart.headOfDept') }}</div>
            <div :ref="setNodeRef('librarian')" class="org-node org-node-small">{{ $t('orgChart.librarian') }}</div>
          </div>
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('traineesAdvisor')" class="org-node org-node-small">{{ $t('orgChart.traineesAdvisor') }}</div>
          </div>
          <div class="w-1/3"></div>
        </div>

        <!-- Level 6 -->
        <div class="chart-row flex justify-between w-full px-8" style="margin-top: 10px;">
          <div class="w-1/3 flex justify-center">
            <div :ref="setNodeRef('teachers')" class="org-node org-node-small">{{ $t('orgChart.teachers') }}</div>
          </div>
          <div class="w-1/3"></div>
          <div class="w-1/3"></div>
        </div>

        <!-- Level 7 -->
        <div class="chart-row" style="margin-top: 20px;">
          <div :ref="setNodeRef('trainees')" class="org-node org-node-small">{{ $t('orgChart.trainees') }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, nextTick, watch } from 'vue'

const { locale } = useI18n()

const containerRef = ref(null)
const contentRef = ref(null)
const nodeEls = {}
const setNodeRef = (key) => (el) => {
  if (el) nodeEls[key] = el
}

const svgSize = reactive({ width: 0, height: 0 })
const lines = ref([])
const paths = ref([])

const rectOf = (el) => {
  const c = containerRef.value.getBoundingClientRect()
  const r = el.getBoundingClientRect()
  const left = r.left - c.left
  const top = r.top - c.top
  return {
    left,
    top,
    right: left + r.width,
    bottom: top + r.height,
    centerX: left + r.width / 2,
    centerY: top + r.height / 2,
  }
}

const edgePoint = (rect, edge) => {
  if (edge === 'top') return { x: rect.centerX, y: rect.top }
  if (edge === 'bottom') return { x: rect.centerX, y: rect.bottom }
  if (edge === 'left') return { x: rect.left, y: rect.centerY }
  return { x: rect.right, y: rect.centerY }
}

// Simple two-point connections between box edges
const STRAIGHT = [
  { from: 'diocese', fromEdge: 'bottom', to: 'director', toEdge: 'top' },
  { from: 'dioceseRep', fromEdge: 'right', to: 'director', toEdge: 'left', both: true },
  { from: 'parentCouncil', fromEdge: 'bottom', to: 'internalAuditors', toEdge: 'top', both: true },
  { from: 'staffDiscipline', fromEdge: 'bottom', to: 'traineesAdvisor', toEdge: 'top' },
  { from: 'headOfDept', fromEdge: 'bottom', to: 'teachers', toEdge: 'top' },
  { from: 'teachers', fromEdge: 'bottom', to: 'trainees', toEdge: 'top' },
  { from: 'accountant', fromEdge: 'bottom', to: 'trainees', toEdge: 'top' },
]

// Bent (elbow) connections: an ordered list of {x,y} waypoints
const ELBOWS = [
  // Diocese -> Parent's Council: right, then down
  {
    build: (r) => {
      const from = edgePoint(r.diocese, 'right')
      const to = edgePoint(r.parentCouncil, 'top')
      return [from, { x: to.x, y: from.y }, to]
    },
  },
  // Director -> Secretary: down, then left
  {
    build: (r) => {
      const from = edgePoint(r.director, 'bottom')
      const to = edgePoint(r.secretary, 'right')
      return [from, { x: from.x, y: to.y }, to]
    },
  },
]

// Tree branches: one parent fanning out into several children
const TREES = [
  { from: 'director', to: ['staffStudies', 'staffDiscipline', 'accountant'] },
  { from: 'staffStudies', to: ['headOfDept', 'librarian'] },
]

const buildTree = (r, parentKey, childKeys) => {
  const p = r[parentKey]
  const children = childKeys.map((k) => r[k])
  const minX = Math.min(...children.map((c) => c.centerX))
  const maxX = Math.max(...children.map((c) => c.centerX))
  const topY = Math.min(...children.map((c) => c.top))
  // Keep the branch bar close to the children row so it doesn't cross
  // through unrelated boxes that happen to sit between parent and children.
  const midY = Math.max(p.bottom + 10, topY - 25)

  const out = []
  out.push({ x1: p.centerX, y1: p.bottom, x2: p.centerX, y2: midY })
  if (children.length > 1) {
    out.push({ x1: minX, y1: midY, x2: maxX, y2: midY })
  }
  children.forEach((c) => {
    out.push({ x1: c.centerX, y1: midY, x2: c.centerX, y2: c.top, markerEnd: true })
  })
  return out
}

const recompute = () => {
  if (!containerRef.value || !contentRef.value) return
  const keys = Object.keys(nodeEls)
  if (!keys.length) return

  const r = {}
  keys.forEach((k) => { r[k] = rectOf(nodeEls[k]) })

  const nextLines = []
  STRAIGHT.forEach(({ from, fromEdge, to, toEdge, both }) => {
    if (!r[from] || !r[to]) return
    const p1 = edgePoint(r[from], fromEdge)
    const p2 = edgePoint(r[to], toEdge)
    nextLines.push({ x1: p1.x, y1: p1.y, x2: p2.x, y2: p2.y, markerEnd: true, markerStart: !!both })
  })

  TREES.forEach(({ from, to }) => {
    if (!r[from] || to.some((k) => !r[k])) return
    nextLines.push(...buildTree(r, from, to))
  })

  const nextPaths = []
  ELBOWS.forEach(({ build }) => {
    const pts = build(r)
    if (pts.some((pt) => !pt)) return
    const d = pts.map((pt, i) => `${i === 0 ? 'M' : 'L'} ${pt.x} ${pt.y}`).join(' ')
    nextPaths.push({ d, markerEnd: true })
  })

  lines.value = nextLines
  paths.value = nextPaths

  const contentRect = contentRef.value.getBoundingClientRect()
  const containerRect = containerRef.value.getBoundingClientRect()
  svgSize.width = containerRect.width
  svgSize.height = Math.max(containerRect.height, contentRect.bottom - containerRect.top)
}

let resizeObserver = null
let rafId = null

const scheduleRecompute = () => {
  if (rafId) cancelAnimationFrame(rafId)
  rafId = requestAnimationFrame(() => {
    rafId = null
    recompute()
  })
}

onMounted(async () => {
  await nextTick()
  scheduleRecompute()

  if (typeof ResizeObserver !== 'undefined' && containerRef.value) {
    resizeObserver = new ResizeObserver(() => scheduleRecompute())
    resizeObserver.observe(containerRef.value)
  }
  window.addEventListener('resize', scheduleRecompute)

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(() => scheduleRecompute())
  }
})

watch(locale, async () => {
  await nextTick()
  scheduleRecompute()
})

onUnmounted(() => {
  if (resizeObserver) resizeObserver.disconnect()
  window.removeEventListener('resize', scheduleRecompute)
  if (rafId) cancelAnimationFrame(rafId)
})
</script>

<style scoped>
.org-node {
  background-color: #000;
  color: #fff;
  border: 3px solid #d35400;
  border-radius: 8px;
  padding: 10px 15px;
  text-align: center;
  font-size: 14px;
  font-weight: bold;
  width: 220px;
  box-sizing: border-box;
  position: relative;
  z-index: 10;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
}

.org-node-small {
  width: 180px;
  font-size: 13px;
  padding: 8px 10px;
}

.chart-row {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  margin-bottom: 40px;
  position: relative;
  width: 100%;
}

.connecting-lines {
  position: absolute;
  top: 0;
  left: 0;
  pointer-events: none;
  z-index: 1;
}

.connecting-lines line,
.connecting-lines path {
  stroke: #1e1e1e;
  stroke-width: 1.5;
  fill: none;
}

.connecting-lines marker path {
  fill: #1e1e1e;
}
</style>
