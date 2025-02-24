<?php

$seed_types = [
    'Vegetable',
    'Herb',
    'Fruit',
    'Flower',
];

$veggie_seeds = [
    'Artichokes',
    'Asparagus',
    'Beans',
    'Beets',
    'Broccoli',
    'Brussels Sprouts',
    'Burdock',
    'Cabbage',
    'Cardoon',
    'Carrots',
    'Cauliflower',
    'Celery and Celeriac',
    'Chicory',
    'Chinese Cabbage',
    'Collards',
    'Corn',
    'Cucumbers',
    'Eggplant',
    'Fennel',
    'Garlic',
    'Gourds',
    'Greens',
    'Horseradish',
    'Husk Cherry',
    'Kale',
    'Kalettes',
    'Kohlrabi',
    'Leeks',
    'Lettuce',
    'Melons',
    'Mix',
    'Microgreens',
    'Okra',
    'Onions',
    'Parsnips',
    'Peanuts',
    'Peas',
    'Peppers',
    'Potatoes',
    'Pumpkins',
    'Radishes',
    'Rutabagas',
    'Salsify',
    'Scorzonera',
    'Shallots',
    'Shoots',
    'Spinach',
    'Sprouts',
    'Squash, Summer',
    'Squash, Winter',
    'Sweet Potatoes',
    'Swiss Chard',
    'Tomatillos',
    'Tomatoes',
    'Turnips',
    'Watermelons',
    'Other',
];

$herb_seeds = [
    'Angelica',
    'Anise Hyssop',
    'Asclepias (Butterfly Weed)',
    'Basil',
    'Bee Balm',
    'Borage',
    'Calendula',
    'Caraway',
    'Catnip',
    'Chamomile',
    'Chervil',
    'Chives',
    'Cilantro (Coriander)',
    'Cumin',
    'Cutting Celery',
    'Dandelion',
    'Dill',
    'Echinacea (Coneflower)',
    'Fennel (Leaf)',
    'Ginseng',
    'Goldenseal',
    'Herbs for Salad Mix',
    'Hyssop',
    'Lavender',
    'Lemon Balm',
    'Lemon Grass',
    'Lovage',
    'Marjoram',
    'Mexican Mint Marigold',
    'Microgreens',
    'Milk Thistle',
    'Mint',
    'Mountain Mint',
    'Oregano',
    'Parsley',
    'Rosemary',
    'Rue',
    'Sage',
    'Salad Burnet',
    'Saltwort',
    'Savory',
    'Shiso',
    'Stevia',
    'Tarragon',
    'Thyme',
    'Valerian',
    'Other',
];

$fruit_names = [
    'Apple',
    'Orange',
    'Strawberry',
    'Other',
];

$flower_seeds = [
    'Ageratum',
    'Agrostemma',
    'Alyssum',
    'Amaranthus',
    'Ammi',
    'Ammobium',
    'Artemisia',
    'Asclepias',
    'Aster',
    'Atriplex',
    'Basil, Ornamental',
    'Bells of Ireland',
    'Borage',
    'Bupleurum',
    'Calendula',
    'Campanula',
    'Carthamus',
    'Celosia',
    'Centaurea',
    'Cerinthe',
    'Clarkia',
    'Columbine',
    'Cosmos',
    'Craspedia',
    'Cress, Ornamental',
    'Cynoglossum',
    'Dahlia',
    'Daucus',
    'Delphinium',
    'Dianthus',
    'Didiscus',
    'Digitalis',
    'Dill',
    'Dusty Miller',
    'Echinacea',
    'Echinops',
    'Eryngium',
    'Eucalyptus',
    'Euphorbia',
    'Flower Collecti',
    'Gomphrena',
    'Grasses, Ornamental',
    'Gypsophila',
    'Helipterum',
    'Hibiscus',
    'Hyacinth Bean',
    'Kale, Ornamental',
    'Larkspur',
    'Lavender',
    'Lisianthus',
    'Lupine',
    'Marigold',
    'Marjoram',
    'Matricaria',
    'Monarda',
    'Mountain Mint',
    'Narcissus',
    'Nasturtium',
    'Nicotiana',
    'Nigella',
    'Orlaya',
    'Phacelia',
    'Phlox',
    'Poppy',
    'Rudbeckia',
    'Rue',
    'Salvia',
    'Saponaria',
    'Scabiosa',
    'Scarlet Runner Bean',
    'Snapdragon',
    'Statice',
    'Stock',
    'Strawflower',
    'Sunflowers',
    'Sweet Peas',
    'Talinum',
    'Tithonia',
    'Tulips',
    'Verbena',
    'Viola',
    'Wildflower Mixes',
    'Yarrow',
    'Xeranthemum',
    'Zinnias',
    'Other',
];

$planting_statuses = [
    'Planned',
    'Active',
    'Harvested',
    'Finished',
    'Failed',
    'Transplanted',
];

$psudo_planting_statuses = [
    'Sprouted',
    'Not Yet Sprouted',
];

return [
    'seed_data' => [
        'types' => $seed_types,
        'common_names' => [
            'veggie' => $veggie_seeds,
            'herb' => $herb_seeds,
            'fruit' => $fruit_names,
            'flower' => $flower_seeds,
        ]
    ],
    'planting_statuses' => $planting_statuses,
    'psudo_planting_statuses' => $psudo_planting_statuses,
    'first_frost' => \Garden\next_day(10, 28),
    'last_frost' => \Garden\next_day(4, 7),
    'usda_zone' => '7a',
];
