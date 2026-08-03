import '../css/app.css'
import '@fontsource/inter/400.css'
import '@fontsource/inter/500.css'
import '@fontsource/inter/600.css'
import '@fontsource/inter/700.css'
import '@fontsource/inter/800.css'
import Varlet from '@varlet/ui'
import '@varlet/ui/es/style'
import { ZiggyVue } from 'ziggy-js'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
  withApp(app) {
    app
      .use(Varlet)
      .use(ZiggyVue) // 2. Pasang plugin ZiggyVue
  },
})
