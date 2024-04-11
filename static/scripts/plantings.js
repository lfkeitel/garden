const bulk_edit_button = document.getElementById("bulk_edit_btn");
const bulk_delete_button = document.getElementById("bulk_delete_btn");
const plantings_sel_checks = document.getElementsByName("plantings_selection");

function setup() {
  for (let i = 0; i < plantings_sel_checks.length; i++) {
    const elem = plantings_sel_checks[i];
    elem.addEventListener("click", sel_toggle);
  }

  bulk_edit_button.addEventListener("click", bulk_edit_btn_click);
  bulk_delete_button.addEventListener("click", bulk_delete_btn_click);
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
  window.location.href = "/plantings/edit?selected=" + set_to_string(selected_plantings);
}

function bulk_delete_btn_click() {
  if (form_confirm()) {
    post("/plantings", {
      "action": "bulk_delete",
      "selection": set_to_string(selected_plantings),
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

const selected_plantings = new Set();

function sel_toggle(ev) {
  if (ev.target.checked) {
    selected_plantings.add(ev.target.value);
  } else {
    selected_plantings.delete(ev.target.value);
  }
}

setup();
