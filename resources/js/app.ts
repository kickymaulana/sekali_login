import '../css/app.css'
import Varlet from '@varlet/ui'
import '@varlet/ui/es/style'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
  withApp(app) {
    app.use(Varlet)
  },
})
