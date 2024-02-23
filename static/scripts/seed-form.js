const seed_type_select = document.getElementsByName('seed_type')[0];
const veg_name_select = document.getElementsByName('seed_vegetable_name')[0];
const herb_name_select = document.getElementsByName('seed_herb_name')[0];
const fruit_name_select = document.getElementsByName('seed_fruit_name')[0];
const flower_name_select = document.getElementsByName('seed_flower_name')[0];

function updateCommonNameVisible(val) {
  veg_name_select.style.display = val == 'Vegetable' ? 'inline-block' : 'none';
  herb_name_select.style.display = val == 'Herb' ? 'inline-block' : 'none';
  fruit_name_select.style.display = val == 'Fruit' ? 'inline-block' : 'none';
  flower_name_select.style.display = val == 'Flower' ? 'inline-block' : 'none';
}
updateCommonNameVisible(seed_type_select.value);

seed_type_select.addEventListener('change', function(e) {
  updateCommonNameVisible(e.target.value);
});

const seed_form = document.getElementById('seed-form');
seed_form.addEventListener('submit', function(evt) {
  const variety_input = document.getElementsByName('variety_name')[0];
  if (variety_input.value === '') {
    evt.preventDefault();
    alert("Seed must have a variety name.");
  }
});
