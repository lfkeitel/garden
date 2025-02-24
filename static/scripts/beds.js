const bulk_edit_button = document.getElementById("bulk_edit_btn");
const bulk_delete_button = document.getElementById("bulk_delete_btn");
const beds_sel_all = document.getElementById("select_all");
const beds_sel_checks = document.getElementsByName("beds_selection");

function setup() {
  for (let i = 0; i < beds_sel_checks.length; i++) {
    const elem = beds_sel_checks[i];
    elem.addEventListener("click", sel_toggle);
  }

  bulk_edit_button.addEventListener("click", bulk_edit_btn_click);
  bulk_delete_button.addEventListener("click", bulk_delete_btn_click);
  beds_sel_all.addEventListener("click", beds_sel_all_click);
}

function post(path, params, method = 'post') {
  const form = document.createElement('form');
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  form.submit();
}

function bulk_edit_btn_click() {
  window.location.href = "/beds/edit?selected=" + set_to_string(selected_beds);
}

function bulk_delete_btn_click() {
  if (form_confirm()) {
    post("/beds", {
      "action": "bulk_delete",
      "selection": set_to_string(selected_beds),
    });
  }
}

function set_to_string(s) {
  let str = "";
  for (const i of s) {
    str += "," + i;
  }
  return str.substring(1);
}

const selected_beds = new Set();

function sel_toggle(ev) {
  if (ev.target.checked) {
    selected_beds.add(ev.target.value);
  } else {
    selected_beds.delete(ev.target.value);
  }
}

function beds_sel_all_click(ev) {
  const checked = ev.target.checked;

  for (let i = 0; i < beds_sel_checks.length; i++) {
    beds_sel_checks[i].checked = checked;
    if (checked) {
      selected_beds.add(beds_sel_checks[i].value);
    } else {
      selected_beds.delete(beds_sel_checks[i].value);
    }
  }
}

setup();
