/// <reference types="vite/client" />

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

// 🟢 Tambahkan deklarasi module untuk CSS & sub-path Varlet di bawah ini:
declare module '@varlet/ui/es/style'
declare module '*.css'
