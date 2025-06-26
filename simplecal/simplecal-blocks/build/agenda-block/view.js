import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ "@wordpress/interactivity":
/*!*******************************************!*\
  !*** external "@wordpress/interactivity" ***!
  \*******************************************/
/***/ ((module) => {

module.exports = __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__;

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************************!*\
  !*** ./src/agenda-block/view.js ***!
  \**********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/interactivity */ "@wordpress/interactivity");

const apiFetch = window.wp.apiFetch;
const {
  state
} = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.store)('agendaBlock', {
  state: {
    output: '',
    currentPage: 0,
    morePastEvents: true,
    moreFutureEvents: true
  },
  actions: {
    getEvents: () => {
      const {
        ref
      } = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getElement)();
      const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getContext)();
      switch (ref.dataset.direction) {
        case "future":
          if (!state.moreFutureEvents) {
            return;
          }
          ++state.currentPage;
          break;
        case "previous":
          if (!state.morePastEvents) {
            return;
          }
          --state.currentPage;
          break;
        default:
          break;
      }
      const queryArgs = `page=${state.currentPage.toString()}&postsPerPage=${context.postsPerPage}&agendaLayout=${context.agendaLayout}&monthYearHeadersShow=${context.monthYearHeadersShow}&dayOfWeekShow=${context.dayOfWeekShow}&thumbnailShow=${context.thumbnailShow}&excerptShow=${context.excerptShow}&excerptLines=${context.excerptLines}&pastEventsShow=${context.pastEventsShow}&pastEventsDays=${context.pastEventsDays}&futureEventsDays=${context.futureEventsDays}`;
      apiFetch({
        // TODO: It sure would be nice to use addQueryArgs to build the path, but WordPress doesn't support adding scripts to script modules yet; that's also why apiFetch is being called in a janky way
        path: `/simplecal/v1/events/agenda/?${queryArgs}`
      }).then(response => {
        state.output = response.output;
        state.morePastEvents = response.morePrevious;
        state.moreFutureEvents = response.moreFuture;
      });
    }
  },
  callbacks: {
    updateAgenda: () => {
      const {
        ref
      } = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getElement)();
      ref.innerHTML = state.output;
    }
  }
});
})();


//# sourceMappingURL=view.js.map