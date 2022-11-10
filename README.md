# Docker environment for front-end development

__Under construction__. Under MIT Licence.

It uses clean docker alpine image and installs `node npm gulp` and some modules. All of components are independent.

## Features

There are 2 sections:

- Components
	- Editor
	- Viewer
- Projects
	- Project
		- Settings
		- Page composer
		- Preview
## Folders structure

- html
	- *src/* - folder for sources
		- *styles/* - basic scss files
		- *scripts/* - basic js files
		- *components/*
    		- 
		- **.html* - HTML files to compose
	- *build/* - folder where gulp saves composed files
		- *img/* - for jpg/png images
		- *svg/* - for svg images
		- *js/* - composed and minimized JS files
		- *css/* - composed, converted and minimized SCSS to CSS files
		- *fonts/* - fonts folder
		- **.html* - Composed HTML pages
- *gulpfile.js*
- *docker-compose.yml*

## Usage

Just `docker-compose up -d`. On startup it creates all folders if they are not exist.

## MAP

- [x] Instalation
- [x] Alpine-based image "wacdis/galp"
- [x] Gulpfile (default included)
