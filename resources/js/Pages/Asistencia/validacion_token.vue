<template>
	<div>
		<div class="container-header-token">
			<a class="btn btn-primary btn-close" href="/"
				><i class="fas fa-long-arrow-alt-left"></i
			></a>
			<img
				v-bind:src="'/images/general/logo-azul-blanco-2.svg'"
				alt="logo-principal"
			/>
		</div>

		<div class="container-picture-token">
			<img
				v-bind:src="'/images/general/foto-usuario.svg'"
				alt="imagen-principal"
			/>
		</div>

		<!-- <form class="container-register-token" action="/asistencia/token" method="get"> -->
		<form
			class="container-register-token"
			@submit.prevent="ValidarToken"
			autocomplete="off"
		>
			<h1>VALIDACIÓN DE TOKEN</h1>
			<input
				class="form-control"
				placeholder="Ingrese los 4 dígitos"
				oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
				type="number"
				maxlength="4"
				required
				name="token"
				id="txt_token"
				v-model="token"
			/>
			<button class="btn btn-primary">VALIDAR</button>
		</form>
	</div>
</template>

<script>
export default {
	data() {
		return {
			token: null,
		};
	},
	mounted() {
		document.title = "Validar token";
		document.body.style.backgroundImage =
			"url('/images/asistencia/fondo-validacion.png')";
	},
	methods: {
		ValidarToken() {
			self = this;

			axios
				.post(route("gth.asi.validar_token"), { token: self.token })
				.then(function (response) {
					if (response.data == "INCORRECTO") {
						Swal.fire({
							icon: "error",
							title: "¡Ups!",
							text: "El TOKEN ingresado es incorrecto, intente nuevamente.",
						});
						return false;
					} else if (response.data == "CORRECTO") {
						self.$inertia.get(route("gth.asi.marcado_asistencia"));
					}
				});
		},
	},
};
</script>

<style lang="css">
/*//////////////////////////////////////////////////////////////////
[ RESTYLE TAG ]*/

* {
	margin: 0px;
	padding: 0px;
	box-sizing: border-box;
}

html {
	font-size: 62.5%; /*Rest para REMS - 62.5% = 10px de 16px*/
}

body,
html {
	width: 100%;
	height: 100%;
	background-position: center center;
	background-repeat: no-repeat;
	background-attachment: fixed;
	background-size: cover;
}

.container-header-token {
	background-color: transparent;
}

.container-header-token img {
	margin: 0.5rem;
	width: 40%;
	margin-left: 30%;
}

.container-picture-token {
	background-color: transparent;
}

.container-picture-token img {
	width: 50%;
	margin-left: 25%;
	margin-top: 2rem;
	margin-bottom: 2rem;
}

.container-register-token {
	width: 70%;
	height: 15rem;
	margin-left: 15%;
	color: white;
	background-color: rgba(229, 224, 224, 0.3);
	border-color: white;
	border-style: solid;
	border-width: 1px;
	border-radius: 2%;
}

.container-register-token h1 {
	font-size: 1.7rem;
	text-align: center;
}

.container-register-token input,
button {
	width: 90%;
	margin-left: 5%;
	margin-bottom: 2rem;
	font-size: 1.4rem;
	text-align: center;
	height: 4rem;
}

.container-register-token button {
	font-size: 2rem;
	background-color: #006faa;
}

.btn-close {
	position: fixed;
	left: 1rem;
	top: 1rem;
	font-size: 15px;
	display: flex;
	align-items: center;
	justify-content: center;
	height: calc(90% - 5px);
	background-color: #14244c;
	border: #14244c;
	width: 50px;
	height: 30px;
	border-radius: 20px;
}

.btn-close:hover {
	background-color: white;
	color: #14244c;
}
.btn-close:hover > i {
	color: #14244c;
}

.btn-close i {
	font-size: 20px;
	color: white;
}

@media (max-width: 400px) and (max-height: 220px) {
	.container-header-token img {
		width: 9rem;
		margin-left: 40%;
	}

	.container-picture-token {
		position: absolute;
	}

	.container-picture-token img {
		margin-left: 1.5rem;
		width: 13rem;
	}

	.container-register-token {
		position: absolute;
		margin-top: 2rem;
		height: 14rem;
		width: 50%;
		right: 0;
	}

	.container-register-token h1 {
		font-size: 1.2rem;
	}
}

@media (max-width: 900px) and (min-height: 220px) and (orientation: landscape) {
	.container-header-token img {
		width: 20%;
		margin-left: 40%;
	}

	.container-picture-token img {
		width: 30%;
		margin-left: 35%;
	}

	.container-register-token {
		width: 50%;
		margin-left: 25%;
	}
}

@media (min-width: 1000px) {
	.container-header-token img {
		width: 20%;
		margin-left: 40%;
	}

	.container-picture-token img {
		width: 20%;
		margin-left: 40%;
	}

	.container-register-token {
		width: 30%;
		margin-left: 35%;
		height: 19rem;
	}

	.container-register-token h1 {
		font-size: 2.5rem;
	}
	.container-register-token input {
		font-size: 2rem;
	}

	.container-register-token button {
		font-size: 2rem;
	}

	.btn-close {
		width: 70px;
	}
}
</style>
