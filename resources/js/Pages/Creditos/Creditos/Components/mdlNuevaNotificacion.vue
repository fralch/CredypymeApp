<template>
    <div id="mdlNuevaNotificacion" class="modal">
        <!-- Modal content -->
        <div class="modal-content w-35 mdlNuevaNotificacion">
            <div class="content" style="display: block">
                <div class="card">
                    <headerCloseModal
                        :titulo_modal="'NUEVA NOTIFICACIÓN'"
                        :nombre_modal="'mdlNuevaNotificacion'"
                    >
                    </headerCloseModal>
                    <div class="card-title">INFORMACIÓN</div>
                    <div class="card-body card-block">
                        <div class="form-row">
                            <div class="col-md-8">
                                <label class="label-title">TIPO</label>
                                <span
                                    v-if="
                                        submited &&
                                        !$v.frmDatosNotificacion.tipo_id.noZero
                                    "
                                    class="span-error-message"
                                >
                                    *
                                </span>
                                <select
                                    class="form-control"
                                    v-model="frmDatosNotificacion.tipo_id"
                                >
                                    <option :value="0" selected disabled>
                                        Seleccione...
                                    </option>
                                    <option
                                        v-for="(
                                            item, index
                                        ) in notificaciones_tipos"
                                        :key="index"
                                        :value="item.id"
                                    >
                                        {{ item.tipo }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="label-title">MONTO</label>
                                <span
                                    v-if="
                                        submited &&
                                        (!$v.frmDatosNotificacion.monto
                                            .noZero ||
                                            !$v.frmDatosNotificacion.monto
                                                .required)
                                    "
                                    class="span-error-message"
                                >
                                    *
                                </span>
                                <input
                                    type="number"
                                    class="form-control bolder center"
                                    :min="monto_minimo"
                                    lang="en"
                                    step="1"
                                    @change="Redondear"
                                    style="
                                        font-size: 1.1rem;
                                        color: var(--colorAlto);
                                    "
                                    v-model.number="frmDatosNotificacion.monto"
                                    :disabled="
                                        frmDatosNotificacion.tipo_id == 0
                                    "
                                />
                            </div>
                        </div>

                        <hr />
                        <div class="text-right">
                            <div class="btn-group" role="group">
                                <button
                                    class="btn btn-action btn-icon-split"
                                    @click="Guardar"
                                >
                                    <span class="icon text-white">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span class="text">APLICAR</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import headerCloseModal from "@/Pages/Creditos/Components/header_close_modal.vue";
const noZero = (value) => value != 0;
export default {
    components: { headerCloseModal },
    props: { agencia_id: Number },
    data() {
        return {
            submited: false,
            notificaciones_tipos: [],
            datos_aval: [],
            monto_minimo: 0,
            frmDatosNotificacion: {
                credito_id: null,
                numero_cuota: 0,
                tipo_id: 0,
                monto: 0,
            },
        };
    },
    validations: {
        frmDatosNotificacion: {
            tipo_id: { noZero },
            monto: { noZero, required },
        },
    },

    computed: {
        tipo() {
            return this.frmDatosNotificacion.tipo_id;
        },
    },
    watch: {
        tipo(value) {
            if (value != 0) {
                const notificacion = this.notificaciones_tipos.filter(
                    (item) => item.id == value
                )[0];

                if (
                    notificacion.nombre_archivo === "RECO_PAGO_TITULAR" ||
                    notificacion.nombre_archivo === "NOTI_CREDITO_TITULAR_1"
                ) {
                    this.monto_minimo = parseFloat(notificacion.monto);
                } else {
                    this.monto_minimo = 0.1;
                }

                this.frmDatosNotificacion.monto = this.roundTo(
                    notificacion.monto,
                    2
                );
            }
        },
    },

    methods: {
        hidenav() {
            return this.$parent.$parent.hide_nav();
        },
        shownav() {
            return this.$parent.$parent.show_nav();
        },
        roundTo(value, decimal_places) {
            let valor = 0;
            let numero_decimales = decimal_places;

            if (value) {
                valor = value;
            }
            return parseFloat(valor).toFixed(numero_decimales);
        },
        Redondear(e) {
            let valor = this.monto_minimo;

            if (e.target.value && e.target.value > this.monto_minimo) {
                valor = e.target.value;
            }

            return (this.frmDatosNotificacion.monto = this.roundTo(valor, 2));
        },
        Guardar() {
            let self = this;
            this.submited = true;

            if (this.$v.frmDatosNotificacion.$invalid) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Complete todos los campos.",
                    allowOutsideClick: true,
                });
                return false;
            }

            let nombre_tipo = this.notificaciones_tipos.filter(
                (item) => item.id == this.frmDatosNotificacion.tipo_id
            )[0].tipo;

            if (nombre_tipo.includes("AVAL") && this.datos_aval.length == 0) {
                Swal.fire({
                    icon: "warning",
                    title: "¡Ups!",
                    text: "Este cliente no tiene AVAL.",
                    allowOutsideClick: true,
                });
                return false;
            }

            Swal.fire({
                icon: "question",
                text: "¿Desea aplicar la NOTIFICACIÓN?",
                confirmButtonText:
                    '<i class="fas fa-check" style="color:white;"></i>   Si',
                confirmButtonColor: "var(--colorAlto)",
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times"></i>   No',
                cancelButtonColor: "var(--plomoOscuroEmpresarial)",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = new FormData();
                    data.append("agencia_id", this.agencia_id);
                    data.append(
                        "frmDatosNotificacion",
                        JSON.stringify(self.frmDatosNotificacion)
                    );

                    Swal.fire({
                        title: "APLICANDO NOTIFICACIÓN",
                        text: "Espere porfavor...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();

                            axios
                                .post(
                                    route(
                                        "rep.cre.dias_mora.aplicar_notificacion"
                                    ),
                                    data
                                )
                                .then(function (response) {
                                    self.$parent.datos_credito =
                                        response.data.datos_credito;
                                    self.$parent.datos_cuotas =
                                        response.data.datos_cuotas;
                                    self.$parent.datos_titular =
                                        response.data.datos_titular;
                                    self.$parent.compromisos =
                                        response.data.compromisos;
                                    self.$parent.notificaciones =
                                        response.data.notificaciones;
                                    self.$parent.notificaciones_tipos =
                                        response.data.notificaciones_tipos;

                                    $("#mdlNuevaNotificacion").css(
                                        "display",
                                        "none"
                                    );

                                    return Swal.fire({
                                        icon: "success",
                                        title: "¡ÉXITO!",
                                        allowOutsideClick: false,
                                    });
                                });
                        },
                    });
                }
            });
        },
    },
};
</script>

<style lang="css">
.mdlNuevaNotificacion {
    margin-top: 5%;
}

@media only screen and (max-width: 900px) {
    .mdlNuevaNotificacion {
        margin-top: 35%;
    }
}
</style>
