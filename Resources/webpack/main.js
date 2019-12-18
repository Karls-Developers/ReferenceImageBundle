
import Vue from "vue";
import vueCustomElement from 'vue-custom-element';

import ReferenceImage from "./vue/field/ReferenceImage.vue";

// Use VueCustomElement
Vue.use(vueCustomElement);

if(!customElements.get('karls-reference-image-field')) {
    Vue.customElement('karls-reference-image-field', ReferenceImage);
}
