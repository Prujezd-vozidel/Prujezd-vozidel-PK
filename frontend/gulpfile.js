var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var rename = require("gulp-rename");

gulp.task('sass', function () {
    return gulp.src('./assets/sass/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('fix-sass', function fixCssTask() {
    const gulpStylelint = require('gulp-stylelint');

    return gulp.src('./assets/sass/**/*.scss')
        .pipe(gulpStylelint({
            fix: true
        }))
        .pipe(gulp.dest('./assets/sass/'));
});

gulp.task('lint-sass', function lintCssTask() {
    const gulpStylelint = require('gulp-stylelint');

    return gulp.src('./assets/sass/**/*.scss')
        .pipe(gulpStylelint({
            reporters: [
                {formatter: 'string', console: true}
            ]
        }));
});

gulp.task('styles', gulp.series('fix-sass', 'sass'));

gulp.task('default', gulp.parallel('styles'));