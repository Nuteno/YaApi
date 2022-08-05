let qs = (selector) => document.querySelector(selector);
let tempPath;
let submit = qs("#input_sub");


submit.addEventListener("click", async (e) => {
	e.preventDefault();
	let file = qs("#input_file").files[0];
	const formdata = new FormData();
	formdata.append("file", file);
	if (qs(".pag_wrapper") != undefined) {
		formdata.append("pag", qs(".pag_wrapper").dataset.pag);
	}
	qs(".files_ul").innerHTML = "Идет загрузка";
	let r = await fetch("controller.php", {
		method: "POST",
		body: formdata,
	}).then((response) => response.text());
	if (r) {
		qs(".files_ul").innerHTML = r;
	}
});
document.addEventListener("click", async (e) => {

	if (e.target.classList.contains("delete_icon")) {
		const formdata = new FormData();
		formdata.append("delete", e.target.closest("div").dataset.path);
		if (qs(".pag_wrapper") != undefined) {
			formdata.append("pag", qs(".pag_wrapper").dataset.pag);
		}
		qs(".files_ul").innerHTML = "Подождите";
		let r = await fetch("controller.php", {
			method: "POST",
			body: formdata,
		}).then((response) => response.text());
		if (r) {
			qs(".files_ul").innerHTML = r;
		}
	}
	if (e.target.classList.contains("download_icon")) {
		location.href =
			"controller.php?download=" + e.target.closest("div").dataset.path;
	}


	if (e.target.classList.contains("edit_icon")) {
		tempPath = e.target.closest("div").dataset.path;
		qs(".modal_rename_wrapper").style.display = "block";
	}
	if (e.target.classList.contains("icon_close")) {
		qs(".modal_rename_wrapper").style.display = "none";
	}
	//
	if (e.target.classList.contains("rename_sub")) {
		e.preventDefault();
		qs(".modal_rename_wrapper").style.display = "none";
		const formdata = new FormData();
		formdata.append("rename", tempPath);
		//Значение из инпута
		formdata.append("newname", qs("#rename_input").value.trim());
		if (qs(".pag_wrapper") != undefined) {
			formdata.append("pag", qs(".pag_wrapper").dataset.pag);
		}


		qs(".files_ul").innerHTML = "Подождите";
		let r = await fetch("controller.php", {
			method: "POST",
			body: formdata,
		}).then((response) => response.text());
		if (r) {
			qs(".files_ul").innerHTML = r;
		}
	}
});
