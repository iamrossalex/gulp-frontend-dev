/*
	npm install -g gulp-cli
	npm install --save-dev gulp del gulp-autoprefixer autoprefixer gulp-file-include gulp-sass gulp-uglify uglify-js pump gulp-watch yargs
*/


'use strict';
var argv = require('yargs').argv;

if (argv.project === undefined) {
    process.exit();
}

var folder = "../" + argv.project + "/",
	gulp = require('gulp'),

	fileinclude = require('gulp-file-include'),
	sass = require('gulp-sass'),
	pump = require('pump'),
	watch = require('gulp-watch'),
	del = require("del"),

	autoprefixer = require("gulp-autoprefixer"),
    plumber = require("gulp-plumber"),
    
	// cssnano = require("cssnano"),
    // postcss = require("gulp-postcss"),
    cssnano = require('gulp-cssnano'),
    rename = require("gulp-rename"),
    
    uglify = require('gulp-uglify-es').default;

let path = {
    build: {
        html: 'html/build/',
        js: 'html/build/js/',
        css: 'html/build/css/'
    },
    src: {
        html: 'html/src/*.html',
        js: 'html/src/js/*.js',
        css: 'html/src/css/*.scss'
    },
    watch: {
        html: 'html/src/**/*.html',
        js: 'html/src/js/**/*.js',
        css: 'html/src/css/**/*.scss'
    }
};
function uClean(cb){return del([folder + path.build.html], {force: true});}
function uHTML(cb) {
    pump([
        gulp.src(folder + path.src.html),
        plumber(),
        fileinclude({prefix: '// @@', basepath: '@file'}),
        gulp.dest(folder + path.build.html)
    ], cb);
}
function uJavaScript(cb) {
    pump([
        gulp.src(folder + path.src.js),
        plumber(),
        fileinclude({prefix: '// @@', basepath: '@file'}),
        uglify(),
        gulp.dest(folder + path.build.js)
    ], cb);
}
function uCSS(cb) {
	pump([
        gulp.src(folder + path.src.css),
        plumber(),
        fileinclude({prefix: '// @@', basepath: '@file'}),
        sass({ outputStyle: "expanded" }),
        gulp.dest(folder + path.build.css),
        rename({ suffix: ".min" }),
        cssnano(),
        // postcss([autoprefixer(), cssnano()]),
        gulp.dest(folder + path.build.css)
    ], cb);
}
function uWatchFiles(cb){
    watch(folder + path.watch.html, uHTML);
    watch(folder + path.watch.js, uJavaScript);
    watch(folder + path.watch.css, uCSS);
	cb();
}

gulp.task("clean",uClean);
gulp.task("html",uHTML);
gulp.task("js",uJavaScript);
gulp.task("css",uCSS);
gulp.task("watch",uWatchFiles);

gulp.task("build", gulp.parallel("css","js","html"));
gulp.task("default", gulp.parallel("watch"));
