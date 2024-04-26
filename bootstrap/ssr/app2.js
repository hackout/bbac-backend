import axios from "axios";
import { ref, useSSRContext, createApp } from "vue";
import { ssrRenderAttrs, ssrInterpolate } from "vue/server-renderer";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
const _sfc_main = {
  __name: "index",
  __ssrInlineRender: true,
  setup(__props) {
    const count = ref(0);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}>${ssrInterpolate(count.value)}</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/views/index/index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
createApp(_sfc_main).mount("#app");
