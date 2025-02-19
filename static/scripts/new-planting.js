const parent_select = document.getElementsByName('parent')[0];
const seed_select = document.getElementsByName('seed')[0];

function fetch_planting_list(id) {
  fetch("/plantings/search?" + new URLSearchParams({
    seed_id: id,
  }), {
    method: "GET",
  })
    .then((response) => response.json())
    .then((json) => {
      parent_select.innerHTML = '';

      const none = document.createElement("option");
      none.textContent = "None";
      none.value = "";
      parent_select.appendChild(none);

      json.forEach(planting => {
        if (planting.bed === null) return;
        const item = document.createElement("option");
        item.value = planting.id;
        item.text = `${planting.date} ${planting.seed.common_name} - ${planting.seed.variety} in ${planting.bed.name} [${planting.row}/${planting.column}] - ${planting.status}`;
        parent_select.appendChild(item);
      });
    })
    .catch((e) => {
      alert(e);
    });
}

fetch_planting_list(seed_select.value);

seed_select.addEventListener('change', (ev) => {
  fetch_planting_list(ev.target.value);
});
