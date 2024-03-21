function handleScriptLoad() {
	return new Promise((resolve) => {
		var loadingIndicator = document.getElementById("loading-indicator");
		// Apply CSS transition to make it fade out gradually
		loadingIndicator.style.transition = "opacity 0.5s";
		// Set the opacity to 0 to initiate the fade-out effect
		loadingIndicator.style.opacity = "0";
		// After the fade-out animation completes, hide the loading indicator
		setTimeout(function () {
			loadingIndicator.style.display = "none";
			resolve(); // Resolve the promise once the loading indicator is hidden
		}, 500); // 500 milliseconds = 0.5 seconds

		// Any other code you want to execute after the script has loaded
	});
}

function fireSwalOnSubmit() {
	Swal.fire({
		imageUrl: "../assets/images/loading-37.webp",
		text: "กำลังดำเนินการ...",
		imageWidth: "77px",
		showConfirmButton: false,
		showCloseButton: false,
		showCancelButton: false,
		backdrop: true,
		allowOutsideClick: false,
	});
}

async function fireSwalOnError(msg, error) {
	Swal.fire({
		icon: "error",
		title: "บันทึกข้อมูลไม่สำเร็จ",
		html: msg,
		footer: error,
		showConfirmButton: true,
	});
}
async function fireSwalOnErrorCustom(title,msg, error) {
	Swal.fire({
		icon: "error",
		title: title,
		html: msg,
		footer: error,
		showConfirmButton: true,
	});
}

function setDropdownRewardValue(v_type, v_category, v_active, v_feq, v_hot) {
	if (v_hot == "") {
		v_hot = "0";
	}
	document.getElementById("par_type").value = v_type;
	document.getElementById("par_category").value = v_category;
	document.getElementById("par_active").value = v_active;
	document.getElementById("par_redeemfeq").value = v_feq;
	document.getElementById("par_ishot").value = v_hot;
}

function clickToLogout() {
	Swal.fire({
		title: "คุณต้องการที่จะออกจากระบบหรือไม่?",
		icon: "question",
		showCancelButton: true,
		confirmButtonText: "ใช่",
		cancelButtonText: "ไม่",
		confirmButtonColor: "#FE0000",
	}).then((result) => {
		if (result.isConfirmed) {
		// Redirect to logout.php
		window.location.href = "../phpfunc/logout.php";
		}
	});
}

function addslashes(str) {
  	return (str + "").replace(/[\\"']/g, "\\$&").replace(/\u0000/g, "\\0");
}

var dataTableSettings = {
	emptyTable: "ไม่มีรายการ",
	info: "รายการที่ _START_ ถึง _END_ จาก _TOTAL_ รายการ",
	lengthMenu: "แสดง _MENU_ รายการ",
	infoEmpty: "ไม่มีข้อมูล",
	loadingRecords: "โปรดรอซักครู่ กำลังเรียกขอมูล...",
	infoFiltered: "ค้นหาจาก _MAX_ รายการ",
	search: "ค้นหา :",
	paginate: {
		first: "หน้าแรก",
		last: "หน้าสุดท้าย",
		next: "ถัดไป",
		previous: "ก่อนหน้า",
	},
}

let tableDom =
	"<'row mb-2'>" + // Buttons
	"<'row'<'col-2'l><'col-6'B><'col-4'f>>" + // Length and Filtering
	"<'row'<'col-12't>>" + // Table
	"<'row'<'col-6'i><'col-6'p>>" + // Information and Pagination
	"<'clear'>";

let tableButton = ["copy", "excel", "print"];

let aLengthMenu = [
	[5, 10, 25, 50, 100, 250, 500, 1000],
	[5, 10, 25, 50, 100, 250, 500, 1000],
];

let rewardListTable;
let itemListTable;
let giftHistoryTalbe;
let categoryTalbe;

let getUrl = document.URL;
let linkUrl = getUrl.replace("list_question", "v_answerForm");

async function initListTable() {
	rewardListTable = $("#rewardListTable").DataTable({
		stateSave: false,
		aLengthMenu: aLengthMenu,
		iDisplayLength: 25,
		ordering: false,
		language: dataTableSettings,
		dom: tableDom,
		buttons: tableButton,
		scrollY: ($('#page-content-wrapper').height() - 300),
    	scrollCollapse: true,
		columns: [
			{
				render: function (data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				},
			},
			{
				data: "question_name",
				render: function (data, type, row, meta) {
					return `${data}`;
				},
			},
			{
				data: "question_detail",
				render: function (data, type, row, meta) {
					return `${data}`;
				},
			},
			{
				data: "question_compfuncname",
				render: function (data, type, row, meta) {
					return `${data}`;
				},
			},
			{
				data: "question_compfuncdepname",
				render: function (data, type, row, meta) {
					return `${data}`;
				},
			},
			{
				data: "question_mquestiontypename",
				render: function (data, type, row, meta) {
					return `${data}`;
				},
			},
			{
				data: "question_recname",
				render: function (data, type, row, meta) {
					return `${data}`;
				},
			},
			{
				data: "question_recdatetime",
				render: function (data, type, row) {
					return (
						dayjs(data, "YYYY-MM-DD hh:mm").format("DD/MM/BBBB HH:mm")
					);
				},
			},
			{
				data: "giftdetail",
				render: function (data, type, row) {
					let isDisabled;
					if (row.total_items != "0") {
						isDisabled = "disabled";
					} else {
						isDisabled = "";
					}
					isDisabled = "";

					return `<div class="btn-group" role="group">
								<button type="button" class="btn btn-warning" onclick="callAction('edit','${row.question}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
								<button type="button" class="btn btn-secondary" onclick="callAction('copy','${row.question}')"><i class="bi bi-copy"></i> Copy</button>
								<button type="button" class="btn btn-danger ${isDisabled}" onclick="callAction('delete','${row.question}','${row.question_name}','${row.currrent_user}')"><i class="bi bi-trash3"></i> ลบ</button>
								<a href="`+linkUrl +`?formId=`+row.question+`" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i>Link</a>
							</div>`;
				},
			},
		],
	});
}

async function initItemListTable() {
	itemListTable = $("#itemListTalbe").DataTable({
		//data: busBreakdownList,
		stateSave: false,
		aLengthMenu: aLengthMenu,
		iDisplayLength: 25,
		ordering: false,
		order: [[0, "desc"]],
		columnDefs: [
			{
				targets: 0,
				orderable: false,
			},
		],
		language: dataTableSettings,
		dom: tableDom,
		buttons: tableButton,
		//buttons: ["excel"],
		serverSide: true,
		processing: true,
		ajax: {
			url: "../phpfunc/rewardapi.php?method=getItemList",
			type: "POST",
			data: function (d) {
				d.offset = d.start;
				d.limit = d.length;
				d.par_id = par_id;
				d.giftType = par_giftType;
				d.search = d.search.value;
			},
			dataSrc: function (json) {
				let canBeInsert;
				var target = document.getElementById("item_total");
				if (json.giftitems_type != "3") {
				// target.innerHTML = `ทั้งหมด : ${json.recordsTotal} (มีได้สูงสุด ${maximumItems}) | แลกแล้ว : ${json.redeemed} | คงเหลือ : ${json.redeemable} | Reserve : ${json.reserve}`;
				target.innerHTML = `${maximumItems} สิทธิ์`;

				document.getElementById("recordsTotalText").innerHTML = json.recordsTotal;
				document.getElementById("redeemedText").innerHTML = json.redeemed;
				document.getElementById("redeemableText").innerHTML = json.redeemable;
				document.getElementById("reserveText").innerHTML = json.reserve;

				var leftToinsert = parseInt(json.recordsTotal) - maximumItems;
				document.getElementById("times").setAttribute("max", Math.abs(leftToinsert));
				document.getElementById("remainingImport").innerHTML =
					"นำเข้าได้อีก : " + Math.abs(maximumItems - parseInt(json.recordsTotal)) + " สิทธิ์";
				document.getElementById("par_maxInsertCSV").value = Math.abs(maximumItems - parseInt(json.recordsTotal));
				} else {
				// target.innerHTML = `จำนวนการแลกสิทธิ์สูงสุด : ${maximumItems} | แลกแล้ว : ${json.redeemed} | คงเหลือ : ${maximumItems - parseInt(json.redeemed)}`;
				target.innerHTML = `${maximumItems}`;
				// target.innerHTML = `จำนวนการแลกสิทธิ์สูงสุด : ${maximumItems} | แลกแล้ว : ${json.redeemed} | คงเหลือ : ${maximumItems - parseInt(json.redeemed)}`;
				document.getElementById("recordsTotalText").innerHTML = maximumItems;
				document.getElementById("redeemedText").innerHTML = json.redeemed;
				document.getElementById("redeemableText").innerHTML = maximumItems - parseInt(json.redeemed);
				document.getElementById("reserveText").innerHTML = "N/A";

				if (json.recordsTotal == "0") {
					document.getElementById("noCodeAlert").removeAttribute("hidden");
				} else {
					document.getElementById("noCodeAlert").setAttribute("hidden", "");
				}

				var leftToinsert = parseInt(json.recordsTotal);
				if (json.data != "") {
					document.getElementById("times").setAttribute("max", "0");
					document.getElementById("insertBatchBtn").setAttribute("disabled", "");
					leftToinsert = 0;
				} else {
					document.getElementById("times").setAttribute("max", "1");
					document.getElementById("insertBatchBtn").removeAttribute("disabled");

					leftToinsert = 1;
				}
				document.getElementById("remainingImport").innerHTML = "นำเข้าได้อีก : " + Math.abs(leftToinsert) + " สิทธิ์";
				document.getElementById("par_maxInsertCSV").value = Math.abs(leftToinsert);
				}
				if (leftToinsert == 0) {
				document.getElementById("message").innerHTML = "จำนวนของของพรีเมี่ยม มีจำนวนสูงสุดแล้ว";
				document.getElementById("message2").innerHTML = "จำนวนของของพรีเมี่ยม มีจำนวนสูงสุดแล้ว";
				document.getElementById("csvFileInput").setAttribute("disabled", "");
				document.getElementById("uploadButton").setAttribute("disabled", "");
				document.getElementById("insertBatchBtn").setAttribute("disabled", "");
				leftToinsert = 0;
				} else {
				document.getElementById("message").innerHTML = "";
				document.getElementById("message2").innerHTML = "";
				document.getElementById("csvFileInput").removeAttribute("disabled");
				document.getElementById("uploadButton").removeAttribute("disabled");
				document.getElementById("insertBatchBtn").removeAttribute("disabled");
				}
				document.getElementById("checkbox4selectall").checked = false;
				return json.data;
			},
		},
		columns: [
			{
				data: "giftitems",
				render: function (data, type, row, meta) {
				if (row.giftitems_isused == "1") {
					return `<input class="form-check-input" type="checkbox" value="${data}" name="selectItemsData_used[]" id="ck_${data}" disabled>`;
				} else {
					return `<input class="form-check-input" type="checkbox" value="${data}" name="selectItemsData[]" id="ck_${data}">`;
				}
				},
			},
			{
				render: function (data, type, row, meta) {
				return meta.row + meta.settings._iDisplayStart + 1;
				},
			},
			{
				data: "giftitems_code",
				render: function (data, type, row, meta) {
				if (row.giftitems_type == "2") {
					if (row.giftrecords_code != "") {
					return `<a href="#" class="fw-bold text-decoration-none point" onclick="window.location.href='v_withdraw.php?pickupCode=${row.giftrecords_code}'">${row.giftrecords_code}</a>`;
					} else {
					return "ยังไม่ถูกแลก";
					}
				} else {
					if (row.giftitems_active == "1") {
					return (
						`<a href="#" class="fw-bold text-decoration-none point" onclick="window.location.href='v_withdraw.php?pickupCode=${data}'">${data}</a>`
					);
					} else {
					return data;
					}
				}
				},
			},
			{
				data: "giftitems_expires",
				render: function (data, type, row) {
				return dayjs(data, "YYYY-MM-DD").format("DD/MM/BBBB");
				},
			},
			{
				data: "giftitems_active",
				render: function (data) {
				if (data == 1) {
					return '<h6><span class="badge text-bg-success mt-2"><i class="bi bi-check-circle"></i> เปิดให้แลก</span></h6>';
				} else if (data == 0) {
					return '<h6><span class="badge text-bg-danger mt-2"><i class="bi bi-x-circle"></i> ปิดให้แลก</span></h6>';
				}
				},
			},
			{
				data: "giftitems_isused",
				render: function (data) {
				if (data == 1) {
					return '<h6><span class="badge text-bg-dark mt-2"><i class="bi bi-check-circle"></i> Used</span></h6>';
				} else if (data == 0) {
					return '<h6><span class="badge text-bg-secondary mt-2"><i class="bi bi-check-circle"></i> Not Used</span></h6>';
				} else if (data == 3) {
					return '<h6><span class="badge text-bg-dark mt-2"><i class="bi bi-check2-all"></i> Multi Used</span></h6>';
				}
				},
			},
			{
				data: "giftitems_modidspm",
			},
			{
				data: "giftitems_moddatetime",
				render: function (data, type, row) {
				return dayjs(data, "YYYY-MM-DD HH:mm:ss").format("HH:mm DD/MM/BBBB");
				},
			},
		],
	});
}

async function initGiftHistoryTalbe() {
	giftHistoryTalbe = $("#giftHistoryTalbe").DataTable({
		//data: busBreakdownList,
		stateSave: false,
		aLengthMenu: aLengthMenu,
		iDisplayLength: 25,
		ordering: false,
		order: [[0, "desc"]],
		columnDefs: [
		{
			targets: 0,
			orderable: false,
		},
		],
		language: dataTableSettings,
		dom: tableDom,
		buttons: tableButton,
		//buttons: ["excel"],
		serverSide: true,
		processing: true,
		ajax: {
		url: "../phpfunc/rewardapi.php?method=getGiftTransectionHistory",
		type: "POST",
		data: function (d) {
			d.offset = d.start;
			d.limit = d.length;
			d.search = d.search.value;
			d.rewardId = filterReward;
			d.outletId = filterOutlet;
			d.iscomplete = filterStatus;
		},
		dataSrc: function (json) {
			return json.data;
		},
		},
		columns: [
		{
			render: function (data, type, row, meta) {
			return meta.row + meta.settings._iDisplayStart + 1;
			},
		},
		{
			data: "giftdetail_image1",
			render: function (data, type, row, meta) {
			return `<img class="img-fluid" src="../storage/${row.giftdetail}/${data}"/>`;
			},
		},
		{
			data: "giftdetail_name",
			render: function (data, type, row, meta) {
			return row.giftdetail_partnername + " : " + data;
			},
		},
		{
			data: "giftrecords_code",
			render: function (data) {
			if (data != "") {
				return (
				`<a href="#" class="fw-bold text-decoration-none point" onclick="window.location.href='v_withdraw.php?pickupCode=${data}'">${data}</a>`
				);
			} else {
				return data;
			}
			},
		},
		{
			data: "giftrecords_type",
			render: function (data, type, row) {
			if (data == 1) {
				return '<i class="bi bi-ticket"></i> คูปอง';
			} else if (data == 2) {
				return '<i class="bi bi-box2-heart"></i> สิ่งของ';
			} else if (data == 3) {
				return '<i class="bi bi-ticket-perforated"></i> คูปอง (โค้ดเดียว)';
			} else {
				return data;
			}
			},
		},
		{
			data: "giftcategory_name",
		},
		{
			data: "giftrecords_user",
		},
		{
			data: "giftrecords_usernm",
		},
		{
			data: "giftrecords_point",
			render: function (data) {
			return parseInt(data).toLocaleString("th-TH") + "pts.";
			},
		},
		{
			data: "giftrecords_pickupdate",
			render: function (data) {
			return dayjs(data, "YYYY-MM-DD").format("DD MMMM YYYY");
			},
		},
		{
			data: "giftrecords_pickupoutletnm",
		},
		{
			data: "giftrecords_iscomplete",
			render: function (data, type, row) {
			if (data == 1) {
				if (row.giftrecords_type == "2") {
				var completeDetail = `${row.giftrecords_modidsnm}<br>${dayjs(
					row.giftrecords_pickupdatetime,
					"YYYY-MM-DD HH:mm:ss"
				).format("HH:mm DD/MM/BB")}`;
				} else {
				var completeDetail = "";
				}
				return (
				'<h6><span class="badge text-bg-success mt-2"><i class="bi bi-check-circle"></i> สำเร็จ</span></h6>' +
				`${completeDetail}`
				);
			} else if (data == 0) {
				if (isDateMoreThan15Days(dayjs(row.giftrecords_pickupdate, "YYYY-MM-DD").startOf("day"))) {
				return '<h6><span class="badge text-bg-danger mt-2"><i class="bi bi-clock"></i> เลยกำหนดรับ<br>(มากกว่า 15 วัน)</span></h6>';
				}
				return '<h6><span class="badge text-bg-warning mt-2"><i class="bi bi-box-seam"></i> รอรับของ</span></h6>';
			}
			},
		},
		{
			data: "giftrecords_datemaker",
			render: function (data) {
				return dayjs(data, "YYYY-MM-DD HH:mm:ss").format("HH:mm DD/MM/BBBB");
			},
		},
		],
	});
}

async function getQuestionListDataFromAPI() {
	try {
		var endpoint = `../phpfunc/questiondata.php?method=getQuestionList`;
		console.log("Test",endpoint);
		const response = await axios.get(endpoint);
		console.log(response.data);
		var data = response.data;
		rewardListTable.clear();
		rewardListTable.rows.add(data);
		rewardListTable.draw();
		//console.log(response);
	} catch (error) {
		console.log(error);
	}
}

async function getItemListDataFromAPI(par_id) {
	return;
	try {
		var endpoint = `../phpfunc/rewardapi.php?method=getItemList&par_id=${par_id}`;
		const response = await axios.get(endpoint);
		var data = response.data;
		//console.log("check item list");
		if (data != null) {
		itemListTable.clear();
		itemListTable.rows.add(data.rtn);
		itemListTable.draw();
		}
		var target = document.getElementById("item_total");
		//target.innerHTML = `ทั้งหมด : ${data.total} | แลกแล้ว : ${data.redeemed} | คงเหลือ : ${data.redeemable} | Reserve : ${data.reserve}`;
	} catch (error) {
		console.log(error);
	}
}

function callAction(action, id, name, currentUserId=0) {
	if (action == "edit") {

		window.location.href = `addquestion.php?id=${id}`;

	} else if (action == "copy") {

		window.location.href = `addquestion.php?id=${id}&copy=1`;

	} else if (action == "delete") {

		Swal.fire({
			title: "คุณต้องการที่จะลบรายการนี้หรือไม่?",
			text: `รายการ : ${name}`,
			icon: "question",
			showCancelButton: true,
			confirmButtonText: "ใช่",
			cancelButtonText: "ไม่",
			confirmButtonColor: "#FE0000",
		}).then((result) => {
			console.log(result.isConfirmed);
			if (result.isConfirmed == true) {
			// window.location.href = `../phpfunc/curd.php?method=del&id=${id}&currentUserId=${$currentUserId}`;
			// var endpoint = `../phpfunc/rewardapi.php?method=getItemList&par_id=${par_id}`;
				if(id > 0 && currentUserId > 0){
					
					let urlendpoint = `../phpfunc/curd.php`;
					$.ajax({
						url: urlendpoint,
						type: "POST",
						// contentType: "application/json",
        				// dataType: 'JSON',
						data: {
							mode : 'del',
							id : id,
							currentUserId : currentUserId,
						},
						success: function(data) {

							console.log(data);
							if(data.success > 0){
								Swal.fire({
									icon: "success",
									text: "บันทึกข้อมูลสำเร็จ",
									timer: 2000,
									showConfirmButton: false,
									showCloseButton: false,
									}).then(() => {
									// getItemListDataFromAPI(par_id);
									setTimeout(() => {
										getQuestionListDataFromAPI().then(() => {
											handleScriptLoad();
										});
									}, 100);
								});
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(jqXHR, textStatus, errorThrown);
							alert('Error occurred!');
						}
					
					});
				}
			}
		});

	}
}

function isObject(value) {
	const target = document.getElementById("div_datepicker");
	if (value == "1") {
		target.setAttribute("hidden", "true");
	} else if (value == "2") {
		target.removeAttribute("hidden");
	} else {
		target.setAttribute("hidden", "true");
	}
}

async function submitFromAddBatchItemes() {
	fireSwalOnSubmit();
	event.preventDefault();
	// Get form data
	var par_id = document.getElementById("par_id").value;
	const formElement = document.getElementById("frm_batch");
	const formData = new FormData(formElement);
	const formDataObject = {};

	formData.forEach((value, key) => {
		if (formDataObject[key]) {
		if (!Array.isArray(formDataObject[key])) {
			formDataObject[key] = [formDataObject[key]];
		}
		formDataObject[key].push(value);
		} else {
		formDataObject[key] = value;
		}
	});
  // console.log(formData);
	var endpoint = "../phpfunc/rewardapi.php?method=insertItemsAsBatch";
	await axios
		.post(endpoint, formData)
		.then(function (response) {
		console.log(response);
		if (response.data.resCode == "1") {
			Swal.fire({
			icon: "success",
			text: "บันทึกข้อมูลสำเร็จ",
			timer: 2000,
			showConfirmButton: false,
			showCloseButton: false,
			}).then(() => {
			// getItemListDataFromAPI(par_id);
			setTimeout(() => {
				// window.location.reload();
				itemListTable.ajax.reload();
			}, 100);
			});
		} else {
			fireSwalOnError("ไม่สามารถเพิ่มข้อมูลได้", JSON.stringify(response.data));
		}
		})
		.catch(function (error) {
		console.log(error);
		return;
		});

	var myModalEl = document.getElementById("createAsBatchModal");
	var modal = bootstrap.Modal.getInstance(myModalEl);
	modal.hide();
}

async function importCodeByCSV() {
	fireSwalOnSubmit();
	const fileInput = document.getElementById("csvFileInput");
	const file = fileInput.files[0];
	var par_id = document.getElementById("par_id").value;
	const par_type = document.getElementById("par_type_csv").value;
	const par_userId = document.getElementById("par_userId").value;
	const par_usernm = document.getElementById("par_usernm").value;
	const par_maxInsertCSV = document.getElementById("par_maxInsertCSV").value;

	if (file) {
		const formData = new FormData();
		formData.append("csvFile", file);
		formData.append("par_id", par_id);
		formData.append("par_type", par_type);
		formData.append("par_userId", par_userId);
		formData.append("par_usernm", par_usernm);
		formData.append("par_maxInsertCSV", par_maxInsertCSV);

		// console.log(formData);
		await axios
		.post("../phpfunc/rewardapi.php?method=importCodeByCSV", formData, {
			headers: {
			"Content-Type": "multipart/form-data",
			},
		})
		.then((response) => {
			//document.getElementById("message").textContent = JSON.stringify(response.data);
			console.log(response.data);
			if (response.data.resCode == "1") {
			Swal.fire({
				icon: "success",
				text: "บันทึกข้อมูลสำเร็จ",
				timer: 2000,
				showConfirmButton: false,
				showCloseButton: false,
			}).then(() => {
				// getItemListDataFromAPI(par_id);
				setTimeout(() => {
				// window.location.reload();
				itemListTable.ajax.reload();
				}, 100);
			});
			} else {
			fireSwalOnError("ไม่สามารถเพิ่มข้อมูลได้", JSON.stringify(response.data));
			getItemListDataFromAPI(par_id);
			}
		})
		.catch((error) => {
			console.error("Error:", error);
			fireSwalOnError("ไม่สามารถเพิ่มข้อมูลได้ (API ERROR)", error);
		});
	} else {
		document.getElementById("message").textContent = "No file selected.";
	}
	var myModalEl = document.getElementById("importCSVModal");
	var modal = bootstrap.Modal.getInstance(myModalEl);
	modal.hide();
}

function renderRewardInfoTable(filed, id) {
	let target = document.getElementById(id);
	let value = target.innerHTML;
	let html;
	if (filed == "type") {
		if (value == 1) {
		html = '<i class="bi bi-ticket"></i> คูปอง';
		} else if (value == 2) {
		html = '<i class="bi bi-box2-heart"></i> สิ่งของ';
		} else if (value == 3) {
		html = '<i class="bi bi-ticket-perforated"></i> คูปอง (โค้ดเดียว)';
		}
	} else if (filed == "feq") {
		if (value == "freely") {
		html = "ไม่จำกัด";
		} else if (value == "once") {
		html = "ครั้งเดียว";
		} else if (value == "weekly") {
		html = "รายสัปดาห์";
		} else if (value == "monthly") {
		html = "รายเดือน";
		} else if (value == "daily") {
		html = "รายวัน";
		}
	} else if (filed == "category") {
		if (value == 1) {
		html = "ท่องเที่ยวเดินทาง";
		} else if (value == 2) {
		html = "อาหารและเครื่องดื่ม";
		} else if (value == 3) {
		html = "บันเทิง";
		} else if (value == 4) {
		html = "ช็อปปิ้ง";
		} else if (value == 5) {
		html = "นครชัยแอร์";
		} else if (value == 6) {
		html = "ไลน์สติกเกอร์";
		} else if (value == 7) {
		html = "โอนคะแนน";
		} else if (value == 8) {
		html = "สุขภาพและความงาม";
		} else if (value == 9) {
		html = "อื่นๆ";
		}
	} else if (filed == "evnetDuration") {
		ArrayDate = value.split(" - ");
		html =
		dayjs(ArrayDate[0], "YYYY-MM-DD").format("DD MMMM BBBB") +
		" - " +
		dayjs(ArrayDate[1], "YYYY-MM-DD").format("DD MMMM BBBB");
	} else if (filed == "rewardActive") {
		if (value == 0) {
		html = '<span class="badge text-bg-danger"><i class="bi bi-x-circle"></i> ปิดให้แลก</span>';
		} else if (value == 1) {
		html = '<span class="badge text-bg-success"><i class="bi bi-check-circle"></i> เปิดให้แลก</span>';
		}
	}
	target.innerHTML = html;
	// console.log(html);
}

async function actionItemsAsBatch(action) {
	let checkboxes = document.getElementsByName("selectItemsData[]");
	let vals = "";

	console.log(action);

	for (var i = 0, n = checkboxes.length; i < n; i++) {
		if (checkboxes[i].checked) {
		vals += "," + checkboxes[i].value;
		}
	}

	if (vals == undefined || vals == "") {
		vals = "";
		fireAlertToast("ไม่มีรายการที่เลือก");
		return;
	}

	vals = vals.substring(1);
	console.log(vals);
	var par_id = document.getElementById("par_id").value;
	var par_userId = document.getElementById("par_userId").value;
	var par_usernm = document.getElementById("par_usernm").value;
	if (action == "del") {
		var title = "คุณต้องการที่จะลบรายการเหล่านี้หรือไม่?";
		var text = "รายการที่ลบไม่สามารถกู้คืนมาได้";
		var apiendpoint = "../phpfunc/rewardapi.php?method=deleteItemsAsBatch";
	} else if (action == "switch") {
		var title = "คุณต้องการที่เปลี่ยนแปลงรายการเหล่านี้หรือไม่?";
		var text = "รายการที่เปิดให้แลกจะเปลี่ยนเป็นปิดให้แลกและรายการที่ปิดให้แลกจะเปลี่ยนเป็นเปิดให้แลก (สลับสถานะ)";
		var apiendpoint = "../phpfunc/rewardapi.php?method=switchActiveItemsAsBatch";
	}

	Swal.fire({
		title: title,
		text: text,
		icon: "question",
		// footer: vals,
		showCancelButton: true,
		confirmButtonText: "ใช่",
		cancelButtonText: "ไม่",
		confirmButtonColor: "#FE0000",
	}).then(async (result) => {
		if (result.isConfirmed) {
		fireSwalOnSubmit();
		const formData = new FormData();
		formData.append("target", vals);
		formData.append("par_userId", par_userId);
		formData.append("par_usernm", par_usernm);
		console.log(formData);
		await axios
			.post(apiendpoint, formData, {
			headers: {
				"Content-Type": "multipart/form-data",
			},
			})
			.then((response) => {
			console.log(response);
			if (response.data.resCode == "1") {
				Swal.fire({
				icon: "success",
				text: "ดำเนินการสำเร็จ",
				timer: 2000,
				showConfirmButton: false,
				showCloseButton: false,
				}).then(() => {
				//getItemListDataFromAPI(par_id);
				itemListTable.ajax.reload();
				document.getElementById("checkbox4selectall").checked = false;
				setTimeout(() => {
					// window.location.reload();
				}, 100);
				});
			} else {
				fireSwalOnError("ไม่สามารถดำเนินการได้", JSON.stringify(response.data));
				// getItemListDataFromAPI(par_id);
				itemListTable.ajax.reload();
				document.getElementById("checkbox4selectall").checked = false;
			}
			})
			.catch((error) => {
			console.error("Error:", error);
			fireSwalOnError("ไม่สามารถดำเนินการได้ (API ERROR)", error);
			itemListTable.ajax.reload();
			document.getElementById("checkbox4selectall").checked = false;
			});
		}
	});
}

function toggleSelectAll(source) {
	checkboxes = document.getElementsByName("selectItemsData[]");
	for (var i = 0, n = checkboxes.length; i < n; i++) {
		if (checkboxes[i].checked) {
		checkboxes[i].checked = false;
		} else {
		checkboxes[i].checked = true;
		}
	}
}

async function fireAlertToast(msg) {
	Swal.fire({
		icon: "info",
		title: "แจ้งเตือน",
		html: msg,
		toast: true,
		timer: 1500,
		timerProgressBar: true,
		position: "center",
		showConfirmButton: false,
	});
}

function initSiwper() {
	var swiper = new Swiper(".mySwiper", {
		loop: true,
		autoHeight: true,
		spaceBetween: 0,
		slidesPerView: 5,
		freeMode: true,
		watchSlidesProgress: true,
	});
	var swiper2 = new Swiper(".mySwiper2", {
		loop: true,
		autoHeight: true,
		spaceBetween: 0,
		navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
		},
		thumbs: {
		swiper: swiper,
		},
	});
	}

async function callDeleteImage(par_id, par_imageOrder, par_imageName) {
	if (par_imageName == null || par_imageName == "") {
		alert(`รูปลำดับที่ ${par_imageOrder} ไม่ได้ถูกอัปโหลดไว้`);
		return;
	}
	let apiendpoint = "../phpfunc/rewardapi.php?method=deleteImage";
	Swal.fire({
		title: "คุณต้องการที่จะรูปภาพหรือไม่?",
		text: `รูปภาพที่ : ${par_imageOrder}`,
		icon: "question",
		showCancelButton: true,
		confirmButtonText: "ใช่",
		cancelButtonText: "ไม่",
		confirmButtonColor: "#FE0000",
	}).then(async (result) => {
		if (result.isConfirmed) {
		fireSwalOnSubmit();
		const formData = new FormData();
		formData.append("par_id", par_id);
		formData.append("par_imageOrder", par_imageOrder);
		formData.append("par_imageName", par_imageName);
		console.log(formData);
		await axios
			.post(apiendpoint, formData, {
			headers: {
				"Content-Type": "multipart/form-data",
			},
			})
			.then((response) => {
			console.log(response);
			if (response.data.resCode == "1") {
				Swal.fire({
				icon: "success",
				text: "ดำเนินการสำเร็จ",
				timer: 2000,
				showConfirmButton: false,
				showCloseButton: false,
				}).then(() => {
				window.location.reload();
				});
			} else {
				fireSwalOnError("ไม่สามารถดำเนินการได้", JSON.stringify(response.data));
			}
			})
			.catch((error) => {
			console.error("Error:", error);
			fireSwalOnError("ไม่สามารถดำเนินการได้ (API ERROR)", error);
			});
		}
	});
}

function truncateHalfString(inputString) {
	const replacementChar = "#";
	const halfLength = Math.floor(inputString.length / 2);
	const truncatedString = inputString.substring(0, halfLength);
	const replacementPart = replacementChar.repeat(halfLength);
	const resultString = truncatedString + replacementPart;
	return resultString;
}

function checkFileSizeAndExtension(id) {
	const fileInput = document.getElementById(id);
	const file = fileInput.files[0];

	if (file) {
		if (!isAllowedFile(file)) {
		userInputNotValid("รูปแบบไฟล์รูปภาพไม่ถูกต้อง (รองรับ .jpg, .jpng, .png & .webp)");
		removeFileFromInput(id);
		return;
		}
		if (!isValidFile(file, 1024 * 1024)) {
		userInputNotValid("ขนาดของไฟล์รูปใหญ่เกินไป (ไม่เกิน 1 MB)");
		removeFileFromInput(id);
		return;
		}
	} else {
	}
}

function isAllowedFile(file) {
	const allowedExtensions = [".jpg", ".jpeg", ".png", ".webp"];
	// const extension = file.name.slice(((file.name.lastIndexOf(".") - 1) >>> 0) + 2).toLowerCase();
	const extension = file.name.slice(((file.name.lastIndexOf(".") - 1) >>> 0) + 2);
	return allowedExtensions.includes("." + extension);
}

function isValidFile(file, maxSize) {
	console.log("file.size : " + file.size);
	return file.size <= maxSize;
}

function userInputNotValid(msg) {
	Swal.fire({
		html: msg,
		icon: "error",
		toast: true,
		position: "center",
		timer: 3000,
		timerProgressBar: true,
		showConfirmButton: false,
		didOpen: (toast) => {
		toast.addEventListener("mouseenter", Swal.stopTimer);
		toast.addEventListener("mouseleave", Swal.resumeTimer);
		},
	});
	}

function removeFileFromInput(id) {
  	document.getElementById(id).value = "";
}


async function fireSwal(icon, title, msg) {
	Swal.fire({
		icon: icon,
		title: title,
		html: msg,
		showConfirmButton: true,
	});
}

function isDateMoreThan15Days(date) {
	const parsedInputDate = dayjs(date,"YYYY-MM-DD").startOf("day");
	const currentDate = dayjs().endOf("day");
	const daysDifference = parsedInputDate.diff(currentDate, 'day');
	// console.log(parsedInputDate.format("YYYY-MM-DD"));
	// console.log(currentDate.format("YYYY-MM-DD"));
	// console.log(`daysDifference : ${daysDifference}`);
	return daysDifference < -15;
}

function renderPickupStatus(data,type,date) {
	console.log(`data : ${data}`);
	console.log(`type : ${type}`);
	const pickupDate = dayjs(date,"YYYY-MM-DD");
	const pickupStatus = data;
	console.log(isDateMoreThan15Days(pickupDate));
	if(type == "2"){
		if (pickupStatus == "1") {
		return '<h3><span class="badge text-bg-success mt-2"><i class="bi bi-check-circle"></i> สำเร็จ</span></h3>';
		} else if (isDateMoreThan15Days(pickupDate)) {
		return '<h3><span class="badge text-bg-danger mt-2"><i class="bi bi-clock"></i> เลยกำหนดรับ (หลังวันกำหนดรับมากกว่า 15 วัน)</span></h3>';
		} else if (data == "0") {
		return '<h3><span class="badge text-bg-warning mt-2"><i class="bi bi-box-seam"></i> รอรับของ</span></h3>';
		}
	}else{
		if (data == 1) {
		return '<h3><span class="badge text-bg-success mt-2"><i class="bi bi-check-circle"></i> สำเร็จ</span></h3>';
		} else if (data == 0) {
		return '<h3><span class="badge text-bg-warning mt-2"><i class="bi bi-box-seam"></i> รอรับของ</span></h3>';
		}
	}
}

function validPickupCycel() {
	const form = document.getElementById("frm");
	const type = document.getElementById("par_type").value;
	const conditionTH = document.getElementById("par_condition").value;
	const conditionEN = document.getElementById("par_conditionen").value;
	event.preventDefault();
	if (conditionTH == "" || conditionEN == null) {
		fireSwal("info", "ระบุข้อมูลไม่ครบ", "โปรดระบุเงื่อนไขการแลกรับสิทธิ์");
		return;
	}
	form.submit();
}

$(".show_confirm").click(function (event) {
	var form = $(this).closest("form");
	var name = $(this).data("name");
	event.preventDefault();
	swal
		.fire({
		title: "ยืนยันการส่งมอบ",
		text: "ยืนยันการส่งมอบหรือไม่?",
		icon: "info",
		showCloseButton: false,
		showCancelButton: true,
		confirmButtonColor: "#009c6b",
		confirmButtonText: "ใช่",
		cancelButtonText: "ไม่",
		})
		.then((result) => {
		if (result.isConfirmed) {
			form.submit();
		}
		});
});

function checkNullOrEmpty(data) {
	if (data == "" || data == null) {
		return "N/A";
	} else {
		return dayjs(data,"YYYY-MM-DD HH:mm:ss").format("HH:mm:ss D MMMM BBBB");
	}
}

let gStartDate;
function setGobalStartDate(value) {
	gStartDate = dayjs(value, "YYYY-MM-DD").format("DD/MM/YYYY");
	$(".datepicker").datepicker("setStartDate", gStartDate);
}

let gEndDate;
function setGobalEndDate(value) {
	gEndDate = dayjs(value, "YYYY-MM-DD").format("DD/MM/YYYY");
	// $(".datepicker").datepicker("setEndDate", gEndDate);
}

function setMinDate() {
	const minDate = document.getElementById("par_startdate").value;
	document.getElementById("par_enddate").min = minDate;
}

function setMaxDate() {
	const maxDate = document.getElementById("par_enddate").value;
	document.getElementById("par_startdate").min = maxDate;
}

function alertSwalSuccess(text=""){
	/* Swal.fire({
		icon: "success",
		text: "บันทึกสำเร็จ",
		timer: 200,
		showConfirmButton: false,
		showCloseButton: false,
	}) */
	if(text != ""){
		swtext = text;
		
	}else{
		swtext = "บันทึกข้อมูลสำเร็จ";
	}
	Swal.fire({
		icon: "success",
		text: swtext,
		showConfirmButton: false,
		showCloseButton: false,
	}).then(() => {
		/* setTimeout(() => {

		}, 500); */
	});
}
