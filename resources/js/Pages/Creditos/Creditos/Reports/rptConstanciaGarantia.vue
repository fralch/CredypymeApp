<template></template>

<script>
import Docxtemplater from "docxtemplater";
import PizZip from "pizzip";
import PizZipUtils from "pizzip/utils/index.js";
import { saveAs } from "file-saver";

function loadFile(url, callback) {
	PizZipUtils.getBinaryContent(url, callback);
}
export default {
	data() {
		return {
			data: [],
			document_title: null,
		};
	},
	methods: {
		GenerarWord(element, filename = "") {
			let self = this;
			const file_path =
				"/report_templates/creditos/evaluaciones/rptConstanciaGarantia.docx";
			let orden = 0;
			//   this.lista_datos.forEach((element) => {
			//     element.orden = orden + 1;
			//   });

			loadFile(file_path, function (error, content) {
				if (error) {
					throw error;
				}
				const zip = new PizZip(content);
				const doc = new Docxtemplater(zip, {
					paragraphLoop: true,
					linebreaks: true,
				});
				doc.setData(self.data);
				try {
					doc.render();
				} catch (error) {
					// The error thrown here contains additional information when logged with JSON.stringify (it contains a properties object containing all suberrors).
					function replaceErrors(key, value) {
						if (value instanceof Error) {
							return Object.getOwnPropertyNames(value).reduce(function (
								error,
								key
							) {
								error[key] = value[key];
								return error;
							},
							{});
						}
						return value;
					}
					// console.log(JSON.stringify({ error: error }, replaceErrors));

					if (error.properties && error.properties.errors instanceof Array) {
						const errorMessages = error.properties.errors
							.map(function (error) {
								return error.properties.explanation;
							})
							.join("\n");
						// console.log("errorMessages", errorMessages);
						// errorMessages is a humanly readable message looking like this:
						// 'The tag beginning with "foobar" is unopened'
					}
					throw error;
				}
				const out = doc.getZip().generate({
					type: "blob",
					mimeType:
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				});

				// Output the document using Data-URI
				saveAs(out, self.document_title + ".docx");
				// print(out, self.document_title + ".pdf");
			});
		},
	},
};
</script>

<style>
</style>
