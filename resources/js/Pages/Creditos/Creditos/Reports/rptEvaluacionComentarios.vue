<template>
	<!-- ------ AREA IMPRIMIBLE --------- -->
	<div id="rptEvaluacionComentarios">
		<h1
			align="center"
			style="font-size: 20px; color: black; font-weight: bolder"
		>
			COMENTARIOS DE EVALUACIÓN FINANCIERA
		</h1>

		<div class="row" style="margin-top: 30px">
			<div class="col-12">
				<label class="label-title"
					>TITULAR:
					<p class="blue-title">
						{{ titular }}
					</p></label
				>
			</div>
			<div class="col-12">
				<label class="label-title"
					>PARIENTES:
					<p class="blue-title" v-for="(item, index) in parientes" :key="index">
						{{
							item.apellido_paterno +
							" " +
							item.apellido_materno +
							" " +
							item.nombres +
							" - " +
							item.dni
						}}
					</p>
					<p class="blue-title" v-if="parientes.length == 0">-</p>
				</label>
			</div>

			<div class="col-12">
				<label class="label-title"
					>AVALES:
					<p class="blue-title" v-for="(item, index) in avales" :key="index">
						{{
							item.apellido_paterno +
							" " +
							item.apellido_materno +
							" " +
							item.nombres +
							" - " +
							item.dni
						}}
					</p>
					<p class="blue-title" v-if="avales.length == 0">-</p></label
				>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">ANTECENDENTES DEL CLIENTE</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.antecedentes_cliente) }}
				</p>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">REFERENCIAS DEL NEGOCIO</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.referencias_negocio) }}
				</p>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">REFERENCIAS DEL DOMICILIO</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.referencias_domicilio) }}
				</p>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">REFERENCIA DEL FAMILIAR O VECINOS</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.referencias_familiar_vecino) }}
				</p>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">REFERENCIAS DEL PARIENTE</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.referencias_pariente) }}
				</p>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">REFERENCIAS DEL AVAL</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.referencias_aval) }}
				</p>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">DESTINO DEL PRÉSTAMO</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.destino_prestamo) }}
				</p>
			</div>
		</div>
		<div class="form-group col-md-12">
			<label class="label-title">OTROS COMENTARIOS</label>
			<div class="box">
				<p class="text center">
					{{ IsNullValue(comentarios.otros_comentarios) }}
				</p>
			</div>
		</div>
		<div class="form-row" style="margin-top: 150px">
			<div class="col-6">
				<hr class="line-sign" />
				<p align="center" style="color: black">ASESOR DE NEGOCIO</p>
			</div>
			<div class="col-6">
				<hr class="line-sign" />
				<p align="center" style="color: black">FIRMA DEL CLIENTE</p>
			</div>
		</div>
	</div>
	<!-- -----FIN IMPRIMIBLE ---------- -->
</template>


<script>
export default {
	props: { agencia_id: Number },
	data() {
		return {
			parientes: [],
			avales: [],
			datos_personales: [],
			comentarios: [],
		};
	},
	computed: {
		titular() {
			if (!this.IsEmpty(this.datos_personales)) {
				let obj = this.datos_personales;
				return (
					obj.apellido_paterno +
					" " +
					obj.apellido_materno +
					" " +
					obj.nombres +
					" - " +
					obj.dni
				);
			}
			return "-";
		},
	},

	methods: {
		IsEmpty(obj) {
			return Object.keys(obj).length === 0;
		},
		IsNullValue(property) {
			if (property == null) {
				return "Ninguno";
			} else {
				return property;
			}
		},
		ListarParientes() {
			let self = this;

			return axios
				.post(
					route("cli.listado_registro.listar_parientes", {
						cliente_id: self.datos_personales.id,
						agencia_id: self.agencia_id,
					})
				)
				.then(function (response) {
					let resultado = response.data;
					if (resultado.length > 0) {
						self.parientes = resultado.filter((item) => item.vinculado == 1);
					} else {
						self.parientes = resultado;
					}
				});
		},
		ListarAvales() {
			let self = this;

			return axios
				.post(
					route("cli.listado_registro.listar_avales", {
						cliente_id: self.datos_personales.id,
						agencia_id: self.agencia_id,
					})
				)
				.then(function (response) {
					let resultado = response.data;
					if (resultado.length > 0) {
						self.avales = resultado.filter((item) => item.vinculado == 1);
					} else {
						self.avales = resultado;
					}
				});
		},
	},
};
</script>

<style lang="css">
#rptEvaluacionComentarios {
	padding: 5mm !important;
	width: 100% !important;
	position: absolute;
	display: none;
}

.box {
	width: 100% !important;
	height: auto;
	border: 1px dotted var(--plomoOscuroEmpresarial) !important;
	-webkit-print-color-adjust: exact;
}

.box .text {
	text-align: center;
	color: var(--plomoOscuroEmpresarial);
}

.blue-title {
	font-size: 18px;
	color: var(--azulOscuroEmpresarial);
	font-weight: bolder;
	text-transform: uppercase;

	margin: 0;
}
.line-sign {
	width: 50%;
	margin-left: 25%;
	background-color: black;
	-webkit-print-color-adjust: exact;
}
</style>
