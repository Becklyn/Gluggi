var gulp    = require("gulp");
var becklyn = require("becklyn-gulp");
var isDebug = !!require("yargs").argv.debug || !!require("yargs").argv.dev;

gulp.task("css", becklyn.scss("assets/**/*.scss", isDebug));
gulp.task("js",  becklyn.js("assets/js/**/*.js", isDebug));


gulp.task("default", ["css", "js"]);
