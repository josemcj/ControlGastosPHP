/**
 * Se importan las dependencias de desarrollo: 'gulp', 'gulp-sass' y el compilador 'sass'
 */
const gulp = require("gulp");

// CSS
const sass = require("gulp-sass")(require("sass"));
const plumber = require("gulp-plumber");
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');

// JavaScript
const terser = require('gulp-terser-js');

// Compila codigo de sass
function estilos(done) {
    return (
        gulp.src('./src/scss/**/*.scss')
            .pipe( sourcemaps.init() )
            .pipe( plumber() )
            .pipe( sass().on("error", sass.logError) )
            .pipe( postcss( [autoprefixer(), cssnano()] ) )
            .pipe( sourcemaps.write('.') )
            .pipe( gulp.dest('./assets/css') )
    );

    done();
}

// Minifica JavaScript
function javascript(done) {
    gulp.src('./src/js/**/*.js')
        .pipe( sourcemaps.init() )
        .pipe( terser() )
        .pipe( sourcemaps.write('.') )
        .pipe( gulp.dest('./assets/js') );

    done();
}

// WATCH
function dev(done) {
    gulp.watch('./src/scss/**/*.scss', estilos);
    gulp.watch('./src/js/**/*.js', javascript);

    done();
}

// Tareas
exports.estilos = estilos;
exports.javascript = javascript;
exports.dev = dev;