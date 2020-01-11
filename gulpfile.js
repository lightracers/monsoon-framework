
var gulp     = require('gulp');
var less     = require('gulp-less');
var concat   = require('gulp-concat');
var uglify   = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');
var rename   = require("gulp-rename");

// Watchers
gulp.task('watch', function () {
    gulp.watch('resources/less/**/*.less', ['less']);
    gulp.watch('resources/js/bundle/*.js', ['js']);
    gulp.watch('resources/css/**/*.css', ['css']);
    gulp.watch('resources/images/**/*', ['images']);
    gulp.watch('resources/fonts/**/*', ['fonts']);
});

// Using gulp-less
gulp.task('less', function () {
    return gulp.src('resources/less/**/*.less')
        .pipe(less())
        .pipe(gulp.dest('public/css'))
});

// Using gulp-uglify
gulp.task('js', function () {
    return gulp.src(['resources/js/**/*.js', 'resources/js/*.js'])
        .pipe(concat('bundle.js'))
        .pipe(uglify())
        .pipe(rename({
            basename: 'bundle',
            suffix: '.min',
        }))
        .pipe(gulp.dest('public/js'))
});

// Using gulp-minify-css
gulp.task('css', function () {
    return gulp.src('resources/css/**/*.css')
        .pipe(cleanCSS())
        .pipe(concat('styles.css'))
        .pipe(rename({
            basename: 'styles',
            suffix: '.min',
        }))
        .pipe(gulp.dest('public/css'))
});

// Images
gulp.task('images', function() {
    return gulp.src('resources/images/**/*')
        .pipe(gulp.dest('public/images'));
});

// Fonts
gulp.task('fonts', function() {
    return gulp.src('resources/fonts/**/*')
        .pipe(gulp.dest('public/fonts'));
});

gulp.task('default',
    gulp.series('less', 'js', 'css', 'images', 'fonts')
);