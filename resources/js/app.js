import("./bootstrap");

import Vue from "vue";
import { createInertiaApp, InertiaLink } from "@inertiajs/inertia-vue";
import { InertiaProgress } from "@inertiajs/progress";
import axios from "axios";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import Vuelidate from "vuelidate";
import PrimeVue from "primevue/config";

import "primevue/resources/themes/saga-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";

import "@/assets/vendors/PrimeVue/app.scss";

import JSZip from "jszip";

InertiaProgress.init();
Vue.prototype.$route = route;
Vue.prototype.$http = axios;
Vue.use(Vuelidate);
Vue.use(PrimeVue, {
    locale: {
        startsWith: "Empieza con",
        contains: "Contiene",
        notContains: "No contiene",
        endsWith: "Termina con",
        equals: "Igual a",
        notEquals: "No igual",
        noFilter: "No Filter",
        lt: "Less than",
        lte: "Less than or equal to",
        gt: "Greater than",
        gte: "Greater than or equal to",
        dateIs: "Date is",
        dateIsNot: "Date is not",
        dateBefore: "Date is before",
        dateAfter: "Date is after",
        clear: "Limpiar",
        apply: "Aplicar",
        matchAll: "Match All",
        matchAny: "Match Any",
        addRule: "Add Rule",
        removeRule: "Remove Rule",
        accept: "Yes",
        reject: "No",
        choose: "Choose",
        upload: "Upload",
        cancel: "Cancel",
        completed: "Completed",
        pending: "Pending",
        dayNames: [
            "Domingo",
            "Lunes",
            "Martes",
            "Miércoles",
            "Jueves",
            "Viernes",
            "Sábado",
        ],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        monthNames: [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Setiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        ],
        monthNamesShort: [
            "Ene",
            "Feb",
            "Mar",
            "Abr",
            "May",
            "Jun",
            "Jul",
            "Ago",
            "Set",
            "Oct",
            "Nov",
            "Dic",
        ],
        chooseYear: "Choose Year",
        chooseMonth: "Choose Month",
        chooseDate: "Choose Date",
        prevDecade: "Previous Decade",
        nextDecade: "Next Decade",
        prevYear: "Previous Year",
        nextYear: "Next Year",
        prevMonth: "Previous Month",
        nextMonth: "Next Month",
        prevHour: "Previous Hour",
        nextHour: "Next Hour",
        prevMinute: "Previous Minute",
        nextMinute: "Next Minute",
        prevSecond: "Previous Second",
        nextSecond: "Next Second",
        am: "am",
        pm: "pm",
        today: "Today",
        weekHeader: "Wk",
        firstDayOfWeek: 0,
        dateFormat: "mm/dd/yy",
        weak: "Weak",
        medium: "Medium",
        strong: "Strong",
        passwordPrompt: "Enter a password",
        emptyFilterMessage: "No results found", // @deprecated Use 'emptySearchMessage' option instead.
        searchMessage: "{0} results are available",
        selectionMessage: "{0} items selected",
        emptySelectionMessage: "No selected item",
        emptySearchMessage: "No results found",
        emptyMessage: "No available options",
        aria: {
            trueLabel: "True",
            falseLabel: "False",
            nullLabel: "Not Selected",
            star: "1 star",
            stars: "{star} stars",
            selectAll: "All items selected",
            unselectAll: "All items unselected",
            close: "Close",
            previous: "Previous",
            next: "Next",
            navigation: "Navigation",
            scrollTop: "Scroll Top",
            moveTop: "Move Top",
            moveUp: "Move Up",
            moveDown: "Move Down",
            moveBottom: "Move Bottom",
            moveToTarget: "Move to Target",
            moveToSource: "Move to Source",
            moveAllToTarget: "Move All to Target",
            moveAllToSource: "Move All to Source",
            pageLabel: "{page}",
            firstPageLabel: "First Page",
            lastPageLabel: "Last Page",
            nextPageLabel: "Next Page",
            prevPageLabel: "Previous Page",
            rowsPerPageLabel: "Rows per page",
            previousPageLabel: "Previous Page",
            jumpToPageDropdownLabel: "Jump to Page Dropdown",
            jumpToPageInputLabel: "Jump to Page Input",
            selectRow: "Row Selected",
            unselectRow: "Row Unselected",
            expandRow: "Row Expanded",
            collapseRow: "Row Collapsed",
            showFilterMenu: "Show Filter Menu",
            hideFilterMenu: "Hide Filter Menu",
            filterOperator: "Filter Operator",
            filterConstraint: "Filter Constraint",
            editRow: "Row Edit",
            saveEdit: "Save Edit",
            cancelEdit: "Cancel Edit",
            listView: "List View",
            gridView: "Grid View",
            slide: "Slide",
            slideNumber: "{slideNumber}",
            zoomImage: "Zoom Image",
            zoomIn: "Zoom In",
            zoomOut: "Zoom Out",
            rotateRight: "Rotate Right",
            rotateLeft: "Rotate Left",
        },
    },
});

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        Vue.use(plugin, JSZip);

        Vue.component("inertia-link", InertiaLink);
        new Vue({
            render: (h) => h(App, props),
        }).$mount(el);
    },
});
