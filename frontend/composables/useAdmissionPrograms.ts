export const PROGRAM_AUTO = 'Automobile Technology'
export const PROGRAM_BUILD = 'Building Construction'
export const PROGRAM_TAILOR = 'Tailoring'
export const PROGRAM_CARP = 'Carpentry'

export const ADMISSION_LEVELS = [
  { value: 'Level 1 - Short Course', i18n: 'admission.level1' },
  { value: 'Level 3 - Senior 4', i18n: 'admission.level3' },
  { value: 'Level 4 - Senior 5', i18n: 'admission.level4' },
  { value: 'Level 5 - Senior 6', i18n: 'admission.level5' },
] as const

export const PROGRAM_LABEL_KEYS: Record<string, string> = {
  [PROGRAM_AUTO]: 'admission.progAutoTitle',
  [PROGRAM_BUILD]: 'admission.progBuildTitle',
  [PROGRAM_TAILOR]: 'admission.tailoring',
  [PROGRAM_CARP]: 'admission.carpentry',
}

const LEVEL1_PROGRAMS = [PROGRAM_TAILOR, PROGRAM_BUILD, PROGRAM_CARP, PROGRAM_AUTO]
const SENIOR_PROGRAMS = [PROGRAM_BUILD, PROGRAM_AUTO]

export const PROGRAM_SEP = ' | '

export function programsForLevel(level: string): string[] {
  if (level === 'Level 1 - Short Course') return [...LEVEL1_PROGRAMS]
  if (
    level === 'Level 3 - Senior 4' ||
    level === 'Level 4 - Senior 5' ||
    level === 'Level 5 - Senior 6'
  ) {
    return [...SENIOR_PROGRAMS]
  }
  return []
}

export function encodeAdmissionChoice(level: string, program: string) {
  const l = (level || '').trim()
  const p = (program || '').trim()
  if (l && p) return `${l}${PROGRAM_SEP}${p}`
  return p || l
}

export function parseAdmissionChoice(app: { level?: string | null; program?: string | null }) {
  const storedLevel = String(app?.level || '').trim()
  const storedProgram = String(app?.program || '').trim()

  if (storedProgram.includes(PROGRAM_SEP)) {
    const [level, program] = storedProgram.split(PROGRAM_SEP).map((s) => s.trim())
    return { level: storedLevel || level, program }
  }

  if (storedLevel && storedProgram && storedLevel !== storedProgram) {
    return { level: storedLevel, program: storedProgram }
  }

  if (storedProgram.startsWith('Level ')) {
    return { level: storedProgram, program: '' }
  }

  return { level: storedLevel, program: storedProgram }
}
