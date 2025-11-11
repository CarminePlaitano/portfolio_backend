// assets/controllers/tinymce_controller.js
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ['textarea'];

  connect() {
    // check global tinymce (loaded by your CDN <script> in head)
    if (typeof window.tinymce === 'undefined') {
      console.error('TinyMCE not found on window. Ensure CDN <script> is included before your app module.');
      return;
    }

    this.textareaTargets.forEach((element) => {
      if (!element.id) element.id = `tinymce-${Math.random().toString(36).slice(2)}`

      window.tinymce.init({
        selector: `#${element.id}`,
        plugins: ['autolink', 'link', 'code'],
        toolbar: 'formatselect | bold italic underline | alignleft aligncenter alignright | link | bullist numlist | code',
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3',
        menubar: false,
        statusbar: false,
        height: 300,
        branding: false,
        language: 'it',
        setup: (editor) => {
          element._tinymceEditor = editor

          editor.on('init', () => {
            element.value = editor.getContent()
          })

          editor.on('change keyup', () => {
            element.value = editor.getContent()
            element.dispatchEvent(new Event('input', { bubbles: true }))
          })

          const form = element.closest('form')
          if (form) {
            const submitHandler = () => { element.value = editor.getContent() }
            form.addEventListener('submit', submitHandler)
            element._tinymceSubmitHandler = submitHandler
          }

          document.addEventListener('turbo:submit-end', () => {
            try { editor.destroy() } catch(e) {}
          })
        }
      })
    })
  }

  disconnect() {
    this.textareaTargets.forEach((element) => {
      const ed = element._tinymceEditor
      if (ed && !ed.removed) {
        try { ed.destroy() } catch(e) {}
      }
      const form = element.closest('form')
      if (form && element._tinymceSubmitHandler) {
        form.removeEventListener('submit', element._tinymceSubmitHandler)
      }
      delete element._tinymceEditor
      delete element._tinymceSubmitHandler
    })
  }
}
