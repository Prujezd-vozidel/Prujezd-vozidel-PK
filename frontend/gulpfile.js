var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var rename = require("gulp-rename");
var concat = require("gulp-concat");
var uglify = require('gulp-uglify');

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

gulp.task('fix-sass', function () {
    const gulpStylelint = require('gulp-stylelint');

    return gulp.src('./assets/sass/**/*.scss')
        .pipe(gulpStylelint({
            fix: true
        }))
        .pipe(gulp.dest('./assets/sass/'));
});

gulp.task('lint-sass', function () {
    const gulpStylelint = require('gulp-stylelint');

    return gulp.src('./assets/sass/**/*.scss')
        .pipe(gulpStylelint({
            reporters: [
                {formatter: 'string', console: true}
            ]
        }));
});

gulp.task('build-js', function () {
    return gulp.src([
        './app/app.module.js',
        './app/app.config.js',
        './app/services/*.js',
        './app/controllers/*.js',
        './app/directives/*.js'
    ])
        .pipe(sourcemaps.init())
        .pipe(concat('app.js'))
        .pipe(gulp.dest('./'))
        .pipe(uglify())
        .pipe(rename({
            suffix: ".min",
            basename: "app",
            extname: ".js"
        }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./'));
});

gulp.task('build-sass', gulp.series('lint-sass', 'sass'));

gulp.task('build', gulp.parallel('build-sass', 'build-js'));


gulp.task('default', gulp.parallel('build'));