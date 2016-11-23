# Hazel's Heritage Backend
## Overview
This is backend portion of the decoupled/headless site [hazelsheritage.com](https://hazelsheritage.com), powered by [WordPress](https://wordpress.org).

The React frontend can be found at [github.com/ataylorme/hazels-heritage-frontend](https://github.com/ataylorme/hazels-heritage-frontend). 

Dependencies, such as WordPress core and plugins, are installed with [Composer](https://getcomposer.org).

The build artifact is created with [CircleCI](https://circleci.com) and deployed to [Pantheon](https://pantheon.io).

## Changelog
### 1.0
* Register `recipe` custom post type
* Register `recipe_main_ingredient` and `recipe_type` custom taxonomies
* Register metaboxes for recipe details with [CMB2](https://wordpress.org/plugins/cmb2/)
* Registers the API endpoints
	* [`/wp-json/recipes/v1/recipes`](https://backend.hazelsheritage.com/wp-json/recipes/v1/recipes) - lists all recipes. Takes parameters for `per_page` and `paged` for pagination.
	* [`/wp-json/recipes/v1/recipes/{ID}`](https://backend.hazelsheritage.com/wp-json/recipes/v1/recipes/62) - lists a specific recipe's full details.
	* [`/wp-json/recipes/v1/recipes/main-ingredients`](https://backend.hazelsheritage.com/wp-json/recipes/v1/main-ingredients) - lists terms for the main ingredients taxonomy.
	* [`/wp-json/recipes/v1/recipes/main-ingredients/{ID}`](https://backend.hazelsheritage.com/wp-json/recipes/v1/main-ingredients/24) - lists recipes with the ID of the main ingredients taxonomy.
	* [`/wp-json/recipes/v1/recipes/recipe-types`](https://backend.hazelsheritage.com/wp-json/recipes/v1/recipe-types) - lists terms for the main ingredients taxonomy.
	* [`/wp-json/recipes/v1/recipes/recipe-types/{ID}`](https://backend.hazelsheritage.com/wp-json/recipes/v1/recipe-types/31) - lists recipes with the ID of the recipe types taxonomy.
* Creates a custom user role `recipe_author` with the [Members plugin](https://wordpress.org/plugins/members/).

## License
Hazel's Heritage Backend
Copyright (C) 2016  Andrew Taylor

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.