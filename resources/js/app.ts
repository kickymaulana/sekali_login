import '../css/app.css'
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
